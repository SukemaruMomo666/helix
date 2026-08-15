<?php

namespace App\Http\Controllers\Seller;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Ambil Data Toko Riil
        $toko = DB::table('tb_toko')->where('user_id', $user->id)->first();
        if (!$toko) {
            return redirect()->route('home')->with('error', 'Toko tidak ditemukan.');
        }
        $tokoId = $toko->id;

        // 2. STATISTIK UTAMA (Real-time dari tb_detail_transaksi)
        $total_penjualan = DB::table('tb_detail_transaksi')->where('toko_id', $tokoId)->whereNotIn('status_pesanan_item', ['dibatalkan'])->sum('subtotal');
        $total_pesanan   = DB::table('tb_detail_transaksi')->where('toko_id', $tokoId)->distinct('transaksi_id')->count('transaksi_id');
        $total_item_terjual = DB::table('tb_detail_transaksi')->where('toko_id', $tokoId)->whereNotIn('status_pesanan_item', ['dibatalkan'])->sum('jumlah');
        $total_produk_aktif = DB::table('tb_barang')->where('toko_id', $tokoId)->where('is_active', 1)->count();

        // 3. OPERASIONAL CARDS
        $perlu_diproses = DB::table('tb_detail_transaksi')->where('toko_id', $tokoId)->whereIn('status_pesanan_item', ['diproses', 'menunggu_pembayaran'])->count();
        $telah_diproses = DB::table('tb_detail_transaksi')->where('toko_id', $tokoId)->where('status_pesanan_item', 'siap_kirim')->count();
        $pengembalian = DB::table('tb_komplain')->where('toko_id', $tokoId)->whereIn('status_komplain', ['investigasi', 'menunggu_tanggapan_toko'])->count();
        $dibatalkan = DB::table('tb_detail_transaksi')->where('toko_id', $tokoId)->where('status_pesanan_item', 'dibatalkan')->count();

        // 4. DATA GRAFIK (Tahun Ini & Bulan Ini)
        $now = Carbon::now();
        
        // Penjualan Bulanan (Untuk Grafik Tahunan)
        $dataBulanan = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->select(DB::raw('MONTH(t.tanggal_transaksi) as bulan'), DB::raw('SUM(d.subtotal) as total'))
            ->where('d.toko_id', $tokoId)
            ->whereYear('t.tanggal_transaksi', $now->year)
            ->whereIn('d.status_pesanan_item', ['dikirim', 'sampai_tujuan', 'selesai'])
            ->groupBy('bulan')->pluck('total', 'bulan')->toArray();

        $penjualan_tahunan = [];
        for ($i = 1; $i <= 12; $i++) { $penjualan_tahunan[] = $dataBulanan[$i] ?? 0; }

        // Penjualan Mingguan (Untuk Grafik Bulan Ini)
        $dataMingguan = DB::table('tb_detail_transaksi as d')
            ->join('tb_transaksi as t', 'd.transaksi_id', '=', 't.id')
            ->select(DB::raw('FLOOR((DAY(t.tanggal_transaksi)-1)/7) + 1 as minggu'), DB::raw('SUM(d.subtotal) as total'))
            ->where('d.toko_id', $tokoId)
            ->whereMonth('t.tanggal_transaksi', $now->month)
            ->whereYear('t.tanggal_transaksi', $now->year)
            ->groupBy('minggu')->pluck('total', 'minggu')->toArray();

        $penjualan_mingguan = [];
        for ($i = 1; $i <= 4; $i++) { $penjualan_mingguan[] = $dataMingguan[$i] ?? 0; }

        // 5. TOP PRODUK & KONVERSI
        $topProdukQuery = DB::table('tb_detail_transaksi as d')
            ->join('tb_barang as b', 'd.barang_id', '=', 'b.id')
            ->select('b.nama_barang', DB::raw('SUM(d.jumlah) as total_terjual'))
            ->where('d.toko_id', $tokoId)
            ->groupBy('d.barang_id', 'b.nama_barang')->orderByDesc('total_terjual')->limit(5)->get();

        $konversi = $total_item_terjual > 0 ? round(($total_pesanan / $total_item_terjual) * 100, 2) : 0;

        return view('seller.dashboard', [
            'toko' => $toko,
            'perlu_diproses' => $perlu_diproses,
            'telah_diproses' => $telah_diproses,
            'pengembalian' => $pengembalian,
            'dibatalkan' => $dibatalkan,
            'total_penjualan' => $total_penjualan,
            'total_pesanan' => $total_pesanan,
            'total_produk_aktif' => $total_produk_aktif,
            'konversi' => $konversi,
            'penjualan_tahunan' => $penjualan_tahunan,
            'penjualan_mingguan' => $penjualan_mingguan, // Data Baru
            'top_produk_keys' => $topProdukQuery->pluck('nama_barang')->toArray(),
            'top_produk_values' => $topProdukQuery->pluck('total_terjual')->toArray(),
        ]);
    }
}