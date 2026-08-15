<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProductModerationController extends Controller
{
    /**
     * Menampilkan daftar semua produk untuk dimoderasi dengan filter dan statistik.
     */
    public function index(Request $request)
    {
        $status_filter = $request->get('status', 'pending'); // Default: Menampilkan yang perlu ditinjau
        $search = $request->get('search');

        // 1. Ambil Statistik Cepat untuk Action Bar (Berdasarkan data tb_barang)
        $stats = [
            'total'    => DB::table('tb_barang')->count(),
            'pending'  => DB::table('tb_barang')->where('status_moderasi', 'pending')->count(),
            'approved' => DB::table('tb_barang')->where('status_moderasi', 'approved')->count(),
            'rejected' => DB::table('tb_barang')->where('status_moderasi', 'rejected')->count(),
        ];

        // 2. Query Utama dengan Join Toko & Kategori (Mengacu pada tb_user dan tb_toko di DB Anda)
        $query = DB::table('tb_barang as b')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->leftJoin('tb_kategori as k', 'b.kategori_id', '=', 'k.id')
            ->select(
                'b.id', 'b.nama_barang', 'b.gambar_utama', 'b.harga', 
                'b.status_moderasi', 'b.created_at', 'b.stok',
                't.nama_toko', 'k.nama_kategori'
            );

        // Filter Berdasarkan Status Moderasi
        if ($status_filter !== 'semua') {
            $query->where('b.status_moderasi', $status_filter);
        }

        // Filter Pencarian (Nama Barang atau Nama Toko)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('b.nama_barang', 'LIKE', "%$search%")
                  ->orWhere('t.nama_toko', 'LIKE', "%$search%");
            });
        }

        // Eksekusi dengan Pagination (12 Produk per halaman untuk tampilan Grid)
        $products = $query->latest('b.created_at')->paginate(12)->withQueryString();

        return view('admin.products.index', compact('products', 'status_filter', 'search', 'stats'));
    }

    /**
     * Menampilkan Detail Produk lengkap untuk proses audit admin.
     */
    public function show($id)
    {
        // Query detail produk dengan join kategori dan toko
        $produk = DB::table('tb_barang as b')
            ->leftJoin('tb_kategori as k', 'b.kategori_id', '=', 'k.id')
            ->join('tb_toko as t', 'b.toko_id', '=', 't.id')
            ->select('b.*', 'k.nama_kategori', 't.nama_toko', 't.id as toko_id')
            ->where('b.id', $id)
            ->first();

        if (!$produk) {
            return redirect()->route('admin.products.index')->with('error', 'Data material tidak ditemukan.');
        }

        // Ambil galeri gambar tambahan dari tabel tb_gambar_barang
        $gallery = DB::table('tb_gambar_barang')
            ->where('barang_id', $id)
            ->get();

        return view('admin.products.show', compact('produk', 'gallery'));
    }

    /**
     * Memproses Keputusan Moderasi (Setujui atau Tolak).
     */
    public function process(Request $request, $id)
    {
        // Validasi input tindakan dan alasan jika ditolak
        $request->validate([
            'action' => 'required|in:approve,reject',
            'alasan_penolakan' => 'required_if:action,reject|nullable|string|min:10'
        ], [
            'alasan_penolakan.required_if' => 'Anda wajib memberikan alasan mengapa produk ini ditolak.',
            'alasan_penolakan.min' => 'Alasan penolakan harus lebih detail (minimal 10 karakter).'
        ]);

        $status = ($request->action == 'approve') ? 'approved' : 'rejected';
        
        // Update data ke tabel tb_barang sesuai struktur DB Pondasikita
        DB::table('tb_barang')->where('id', $id)->update([
            'status_moderasi' => $status,
            'alasan_penolakan' => ($status == 'rejected') ? $request->alasan_penolakan : null,
            'updated_at' => now()
        ]);

        $msg = ($status == 'approved') ? 'Material berhasil disetujui dan kini tayang di platform.' : 'Pengajuan material telah ditolak.';
        $type = ($status == 'approved') ? 'success' : 'warning';

        return redirect()->route('admin.products.index')->with($type, $msg);
    }
}