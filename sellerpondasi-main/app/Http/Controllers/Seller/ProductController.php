<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ProductImport;
use App\Exports\ProductTemplateExport;

class ProductController extends Controller
{
    /**
     * Menampilkan daftar produk seller
     */
    public function index(Request $request)
    {
        $user = Auth::user();

        // 1. Ambil Data Toko (Aman)
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();

        if (!$toko) {
            return redirect()->route('seller.dashboard')->with('error', 'Data Toko tidak ditemukan. Silakan lengkapi profil toko Anda terlebih dahulu.');
        }

        // 2. Query Produk
        $query = DB::table('tb_barang')->where('toko_id', $toko->id);

        // 3. Filter Pencarian
        if ($request->has('search') && $request->search != '') {
            $query->where('nama_barang', 'like', '%' . $request->search . '%');
        }

        // 4. Filter Status
        if ($request->has('status') && $request->status != '') {
            $status = $request->status;
            if ($status == 'active') {
                $query->where('is_active', 1)->where('status_moderasi', 'approved');
            } elseif ($status == 'inactive') {
                $query->where('is_active', 0)->where('status_moderasi', 'approved');
            } elseif ($status == 'pending') {
                $query->where('status_moderasi', 'pending');
            } elseif ($status == 'rejected') {
                $query->where('status_moderasi', 'rejected');
            }
        }

        // 5. Pagination (Tetap pertahankan query string agar pencarian tidak reset)
        $products = $query->orderByDesc('created_at')->paginate(10)->withQueryString();

        return view('seller.products.index', compact('products', 'toko'));
    }

    /**
     * Menampilkan Form Tambah Produk
     */
    public function create()
    {
        $categories = DB::table('tb_kategori')->orderBy('nama_kategori', 'ASC')->get();

        return view('seller.products.create', [
            'categories' => $categories,
            'product' => null
        ]);
    }

    /**
     * Menyimpan Produk Baru (INTEGRASI AUTO-APPROVE ADMIN & VALIDASI KETAT)
     */
    public function store(Request $request)
    {
        $hasVariasi = $request->has('has_variasi') && $request->has_variasi == 1;

        // 1. Validasi Input Super Aman
        $request->validate([
            'nama_barang' => 'required|string|min:5|max:255',
            'kategori_id' => 'required|exists:tb_kategori,id',
            'harga'       => $hasVariasi ? 'nullable|numeric|min:0' : 'required|numeric|min:0',
            'stok'        => $hasVariasi ? 'nullable|integer|min:0' : 'required|integer|min:0',
            'min_order'   => 'required|integer|min:1',
            'berat_kg'    => 'required|numeric|min:0.01',
            'panjang_cm'  => 'nullable|numeric|min:0',
            'lebar_cm'    => 'nullable|numeric|min:0',
            'tinggi_cm'   => 'nullable|numeric|min:0',
            'deskripsi'   => 'required|string|min:20',
            'gambar'      => 'required',
            'merk_barang' => 'nullable|string|max:100',
            'kode_barang' => 'nullable|string|max:50',

            // PERBAIKAN: Jika ada tipe_diskon, nilai_diskon WAJIB diisi
            'tipe_diskon'     => 'nullable|in:NOMINAL,PERSEN',
            'nilai_diskon'    => 'required_with:tipe_diskon|nullable|numeric|min:0',
            'diskon_mulai'    => 'nullable|date',
            'diskon_berakhir' => 'nullable|date|after_or_equal:diskon_mulai',
        ]);

        if ($hasVariasi) {
            $request->validate([
                'variasi_nama' => 'required|array',
                'sku_harga' => 'required|array',
                'sku_stok' => 'required|array',
            ]);
        }

        // Proteksi Logika Diskon Persen (Maks 100%)
        if ($request->tipe_diskon == 'PERSEN' && $request->nilai_diskon > 100) {
            return back()->withInput()->with('error', 'Diskon persen tidak boleh lebih dari 100%.');
        }

        // PERBAIKAN: Pembersihan data diskon jika Seller tidak memilih tipe diskon
        $tipeDiskon = $request->tipe_diskon;
        $nilaiDiskon = $tipeDiskon ? $request->nilai_diskon : null;
        $diskonMulai = $tipeDiskon ? $request->diskon_mulai : null;
        $diskonBerakhir = $tipeDiskon ? $request->diskon_berakhir : null;

        // 2. Proses Upload Gambar Fleksibel (Single/Array Filepond)
        $gambarName = 'default.jpg';
        if ($request->hasFile('gambar')) {
            $fileData = $request->file('gambar');
            $file = is_array($fileData) ? $fileData[0] : $fileData;

            $gambarName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('assets/uploads/products'), $gambarName);

            // SINKRONISASI: Copy juga ke folder customer (repopondasi)
            $customerProductDir = base_path('../repopondasi/public/assets/uploads/products');
            if(!\Illuminate\Support\Facades\File::exists($customerProductDir)) { 
                \Illuminate\Support\Facades\File::makeDirectory($customerProductDir, 0777, true); 
            }
            \Illuminate\Support\Facades\File::copy(
                public_path('assets/uploads/products/' . $gambarName),
                $customerProductDir . '/' . $gambarName
            );
        }

        // 3. Ambil ID Toko
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        // 4. CEK REGULASI ADMIN (Auto Approve Product)
        $autoApprove = DB::table('tb_pengaturan')->where('setting_nama', 'auto_approve_products')->value('setting_nilai');
        $statusModerasi = ($autoApprove == '1') ? 'approved' : 'pending';

        // 5. Simpan ke Database
        // Jika ada variasi, harga termurah akan jadi patokan harga utama (display price)
        // Dan total stok variasi akan jadi patokan stok utama
        $hargaUtama = $request->harga ?? 0;
        $stokUtama = $request->stok ?? 0;

        if ($hasVariasi && !empty($request->sku_harga)) {
            $hargaUtama = min($request->sku_harga);
            $stokUtama = array_sum($request->sku_stok);
        }

        $barangId = DB::table('tb_barang')->insertGetId([
            'toko_id'         => $toko->id,
            'kategori_id'     => $request->kategori_id,
            'nama_barang'     => $request->nama_barang,
            'merk_barang'     => $request->merk_barang,
            'kode_barang'     => $request->kode_barang,
            'harga'           => $hargaUtama,
            'min_order'       => $request->min_order,
            'stok'            => $stokUtama,
            'berat_kg'        => $request->berat_kg,
            'panjang_cm'      => $request->panjang_cm ?? 0,
            'lebar_cm'        => $request->lebar_cm ?? 0,
            'tinggi_cm'       => $request->tinggi_cm ?? 0,
            'satuan_unit'     => $request->satuan_unit ?? 'pcs',
            'deskripsi'       => $request->deskripsi,
            'tipe_diskon'     => $tipeDiskon,
            'nilai_diskon'    => $nilaiDiskon,
            'diskon_mulai'    => $diskonMulai,
            'diskon_berakhir' => $diskonBerakhir,
            'gambar_utama'    => $gambarName,
            'status_moderasi' => $statusModerasi,
            'is_active'       => 1,
            'created_at'      => now(),
            'updated_at'      => now()
        ]);

        // LOGIKA INSERT VARIASI (MATRIKS)
        if ($hasVariasi) {
            $variasiNama = $request->variasi_nama; // array [0 => 'Warna', 1 => 'Ukuran']
            $variasiOpsi = $request->variasi_opsi; // array [0 => ['Merah','Biru'], 1 => ['S','M']]
            
            $opsiMap = []; // format: ['Warna' => ['Merah' => id, 'Biru' => id]]

            foreach ($variasiNama as $index => $nama) {
                if (empty($nama)) continue;
                
                $varId = DB::table('tb_barang_variasi')->insertGetId([
                    'barang_id' => $barangId,
                    'nama_variasi' => $nama,
                    'urutan' => $index + 1,
                    'created_at' => now()
                ]);

                $opsiList = $variasiOpsi[$index] ?? [];
                foreach ($opsiList as $opsiNama) {
                    if (empty($opsiNama)) continue;
                    $opsiId = DB::table('tb_barang_variasi_opsi')->insertGetId([
                        'variasi_id' => $varId,
                        'nama_opsi' => $opsiNama,
                        'created_at' => now()
                    ]);
                    $opsiMap[$index][$opsiNama] = $opsiId;
                }
            }

            // Membangun Matriks SKU
            $skuOpsi1 = $request->sku_opsi1 ?? [];
            $skuOpsi2 = $request->sku_opsi2 ?? [];
            $skuHarga = $request->sku_harga ?? [];
            $skuStok = $request->sku_stok ?? [];
            $skuKode = $request->sku_kode ?? [];

            foreach ($skuOpsi1 as $i => $o1) {
                $o2 = $skuOpsi2[$i] ?? null;
                
                $id_opsi1 = isset($opsiMap[0][$o1]) ? $opsiMap[0][$o1] : null;
                $id_opsi2 = (!empty($o2) && isset($opsiMap[1][$o2])) ? $opsiMap[1][$o2] : null;
                
                if ($id_opsi1) {
                    DB::table('tb_barang_sku')->insert([
                        'barang_id' => $barangId,
                        'opsi_1_id' => $id_opsi1,
                        'opsi_2_id' => $id_opsi2,
                        'harga' => $skuHarga[$i] ?? 0,
                        'stok' => $skuStok[$i] ?? 0,
                        'kode_sku' => $skuKode[$i] ?? null,
                        'created_at' => now()
                    ]);
                }
            }
        }

        $pesan = ($statusModerasi == 'approved')
                 ? 'Produk berhasil ditambahkan dan langsung tayang!'
                 : 'Produk berhasil ditambahkan. Menunggu persetujuan Admin.';

        return redirect()->route('seller.products.index')->with('success', $pesan);
    }

    /**
     * Menampilkan Form Edit
     */
    public function edit($id)
    {
        $product = DB::table('tb_barang')->where('id', $id)->first();
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        if (!$product || $product->toko_id !== $toko->id) {
            abort(403, 'Anda tidak memiliki izin untuk mengedit produk ini.');
        }

        $categories = DB::table('tb_kategori')->orderBy('nama_kategori', 'ASC')->get();

        return view('seller.products.create', compact('product', 'categories'));
    }

    /**
     * Update Produk (Aman & Tidak Merusak Gambar Lama)
     */
    public function update(Request $request, $id)
    {
        // 1. Validasi
        $request->validate([
            'nama_barang' => 'required|string|min:5|max:255',
            'kategori_id' => 'required|exists:tb_kategori,id',
            'harga'       => 'required|numeric|min:0',
            'min_order'   => 'required|integer|min:1',
            'stok'        => 'required|integer|min:0',
            'berat_kg'    => 'required|numeric|min:0.01',
            'panjang_cm'  => 'nullable|numeric|min:0',
            'lebar_cm'    => 'nullable|numeric|min:0',
            'tinggi_cm'   => 'nullable|numeric|min:0',
            'satuan_unit' => 'nullable|string',
            'deskripsi'   => 'required|string|min:20',
            'gambar'      => 'nullable',
            'merk_barang' => 'nullable|string|max:100',
            'kode_barang' => 'nullable|string|max:50',

            // PERBAIKAN: Jika ada tipe_diskon, nilai_diskon WAJIB diisi
            'tipe_diskon'     => 'nullable|in:NOMINAL,PERSEN',
            'nilai_diskon'    => 'required_with:tipe_diskon|nullable|numeric|min:0',
            'diskon_mulai'    => 'nullable|date',
            'diskon_berakhir' => 'nullable|date|after_or_equal:diskon_mulai',
        ]);

        if ($request->tipe_diskon == 'PERSEN' && $request->nilai_diskon > 100) {
            return back()->withInput()->with('error', 'Diskon persen tidak boleh lebih dari 100%.');
        }

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();
        $existingProduct = DB::table('tb_barang')->where('id', $id)->where('toko_id', $toko->id)->first();

        if (!$existingProduct) { abort(404); }

        // PERBAIKAN BUG GHOST DISCOUNT
        $tipeDiskon = $request->tipe_diskon;
        $nilaiDiskon = $tipeDiskon ? $request->nilai_diskon : null;
        $diskonMulai = $tipeDiskon ? $request->diskon_mulai : null;
        $diskonBerakhir = $tipeDiskon ? $request->diskon_berakhir : null;

        $gambarName = $existingProduct->gambar_utama;

        // 2. Cek Upload Gambar Baru (Penghapusan File Aman)
        if ($request->hasFile('gambar')) {
            $oldImagePath = public_path('assets/uploads/products/' . $gambarName);
            if ($gambarName && $gambarName != 'default.jpg' && File::exists($oldImagePath)) {
                File::delete($oldImagePath);
            }
            // Hapus juga file lama di customer (repopondasi)
            $oldImageCustomer = base_path('../repopondasi/public/assets/uploads/products/' . $gambarName);
            if ($gambarName && $gambarName != 'default.jpg' && File::exists($oldImageCustomer)) {
                File::delete($oldImageCustomer);
            }

            $fileData = $request->file('gambar');
            $file = is_array($fileData) ? $fileData[0] : $fileData;

            $gambarName = time() . '_' . uniqid() . '.' . $file->extension();
            $file->move(public_path('assets/uploads/products'), $gambarName);

            // SINKRONISASI: Copy juga ke folder customer (repopondasi)
            $customerProductDir = base_path('../repopondasi/public/assets/uploads/products');
            if(!File::exists($customerProductDir)) { 
                File::makeDirectory($customerProductDir, 0777, true); 
            }
            File::copy(
                public_path('assets/uploads/products/' . $gambarName),
                $customerProductDir . '/' . $gambarName
            );
        }

        // 3. Update Database
        DB::table('tb_barang')->where('id', $id)->update([
            'kategori_id'     => $request->kategori_id,
            'nama_barang'     => $request->nama_barang,
            'merk_barang'     => $request->merk_barang,
            'kode_barang'     => $request->kode_barang,
            'harga'           => $request->harga,
            'min_order'       => $request->min_order,
            'stok'            => $request->stok,
            'berat_kg'        => $request->berat_kg,
            'panjang_cm'      => $request->panjang_cm ?? 0,
            'lebar_cm'        => $request->lebar_cm ?? 0,
            'tinggi_cm'       => $request->tinggi_cm ?? 0,
            'satuan_unit'     => $request->satuan_unit ?? 'pcs',
            'deskripsi'       => $request->deskripsi,

            // Update dengan data diskon yang sudah bersih
            'tipe_diskon'     => $tipeDiskon,
            'nilai_diskon'    => $nilaiDiskon,
            'diskon_mulai'    => $diskonMulai,
            'diskon_berakhir' => $diskonBerakhir,

            'gambar_utama'    => $gambarName,
            'updated_at'      => now()
        ]);

        return redirect()->route('seller.products.index')->with('success', 'Informasi produk berhasil diperbarui.');
    }

    /**
     * Hapus Produk secara Aman (Mencegah Ghost Data)
     */
    public function destroy($id)
    {
        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();
        $product = DB::table('tb_barang')->where('id', $id)->where('toko_id', $toko->id)->first();

        if ($product) {
            $imagePath = public_path('assets/uploads/products/' . $product->gambar_utama);
            if ($product->gambar_utama && $product->gambar_utama != 'default.jpg' && File::exists($imagePath)) {
                File::delete($imagePath);
            }

            DB::table('tb_barang')->where('id', $id)->delete();
            return redirect()->route('seller.products.index')->with('success', 'Produk berhasil dihapus permanen.');
        }

        return redirect()->route('seller.products.index')->with('error', 'Produk gagal dihapus atau tidak ditemukan.');
    }

    /**
     * Toggle Status (Aktif/Nonaktif) via AJAX Switch
     */
    public function toggleStatus(Request $request)
    {
        // PERBAIKAN: Tambah Validasi AJAX agar aman dari manipulasi inspect element
        $request->validate([
            'product_id' => 'required|integer',
            'is_active'  => 'required|boolean',
        ]);

        $toko = DB::table('tb_toko')->where('user_id', Auth::id())->first();

        $updated = DB::table('tb_barang')
            ->where('id', $request->product_id)
            ->where('toko_id', $toko->id) // Memastikan hanya produk toko dia yang bisa diubah
            ->update(['is_active' => $request->is_active, 'updated_at' => now()]);

        return response()->json(['success' => (bool)$updated]);
    }

    /**
     * ==========================================
     * FITUR IMPORT EXCEL & DOWNLOAD TEMPLATE
     * ==========================================
     */
    public function importExcel(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xls,xlsx,csv|max:10240' // Max 10MB
        ]);

        try {
            Excel::import(new ProductImport, $request->file('file_excel'));
            return redirect()->back()->with('success', 'Berhasil! Data material telah di-import dan masuk ke gudang (Off Etalase).');
        } catch (\Exception $e) {
            \Log::error('Import Excel Error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal import! Pastikan file mengikuti format template dan nama kolom tidak diubah.');
        }
    }

    public function downloadTemplate()
    {
        if (ob_get_length() > 0) {
            ob_end_clean();
        }
        return Excel::download(new ProductTemplateExport, 'template_material_pondasikita.xlsx');
    }
}
