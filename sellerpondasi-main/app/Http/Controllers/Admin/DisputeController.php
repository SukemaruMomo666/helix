<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DisputeController extends Controller
{
    /**
     * Menampilkan Dasbor Pusat Resolusi
     */
    public function index(Request $request)
    {
        $status = $request->get('status', 'aktif');
        $search = $request->get('search');

        // Statistik Cepat (Untuk Widget Atas)
        $stats = [
            'total_kasus' => DB::table('tb_komplain')->count(),
            'perlu_tindakan' => DB::table('tb_komplain')->whereIn('status_komplain', ['investigasi', 'menunggu_tanggapan_toko'])->count(),
            'dana_dikembalikan' => DB::table('tb_komplain')->where('status_komplain', 'refund_pembeli')->count(),
            'dana_diteruskan' => DB::table('tb_komplain')->where('status_komplain', 'teruskan_dana_toko')->count(),
        ];

        // Query Mengambil Data Kasus
        $query = DB::table('tb_komplain as k')
            ->join('tb_transaksi as trx', 'k.transaksi_id', '=', 'trx.id')
            ->join('tb_toko as t', 'k.toko_id', '=', 't.id')
            ->join('tb_user as u', 'k.user_id', '=', 'u.id')
            ->select(
                'k.*', 
                'trx.kode_invoice', 'trx.total_final',
                't.nama_toko', 't.telepon_toko',
                'u.nama as nama_pembeli', 'u.no_telepon as telepon_pembeli'
            );

        // Filter Tab (Kasus Aktif vs Selesai)
        if ($status === 'aktif') {
            $query->whereIn('k.status_komplain', ['investigasi', 'menunggu_tanggapan_toko']);
        } elseif ($status === 'selesai') {
            $query->whereIn('k.status_komplain', ['refund_pembeli', 'teruskan_dana_toko', 'selesai']);
        }

        // Pencarian (Berdasarkan Invoice, Toko, atau Pembeli)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('trx.kode_invoice', 'LIKE', "%$search%")
                  ->orWhere('u.nama', 'LIKE', "%$search%")
                  ->orWhere('t.nama_toko', 'LIKE', "%$search%");
            });
        }

        $disputes = $query->latest('k.created_at')->paginate(10)->withQueryString();

        return view('admin.disputes.index', compact('disputes', 'status', 'search', 'stats'));
    }

    /**
     * Memproses Keputusan Hakim (Admin)
     */
    public function resolve(Request $request, $id)
    {
        $request->validate([
            'keputusan' => 'required|in:refund_pembeli,teruskan_dana_toko',
            'keputusan_admin' => 'required|string|min:10'
        ], [
            'keputusan_admin.required' => 'Anda wajib memberikan alasan hukum/keputusan.',
            'keputusan_admin.min' => 'Alasan keputusan harus jelas dan detail.'
        ]);

        try {
            DB::beginTransaction();

            $komplain = DB::table('tb_komplain')->where('id', $id)->first();
            if (!$komplain) {
                return back()->with('error', 'Kasus tidak ditemukan!');
            }

            // 1. Update Status Komplain
            DB::table('tb_komplain')->where('id', $id)->update([
                'status_komplain' => $request->keputusan,
                'keputusan_admin' => $request->keputusan_admin,
                'updated_at' => now()
            ]);

            // 2. Efek ke Transaksi Global
            if ($request->keputusan == 'refund_pembeli') {
                // Batalkan pesanan, dana akan diproses refund oleh tim finance
                DB::table('tb_transaksi')->where('id', $komplain->transaksi_id)->update([
                    'status_pesanan_global' => 'dibatalkan',
                    'catatan' => 'Dibatalkan oleh Pusat Resolusi. Alasan: ' . $request->keputusan_admin
                ]);
            } else {
                // Teruskan dana ke toko, anggap pesanan selesai
                DB::table('tb_transaksi')->where('id', $komplain->transaksi_id)->update([
                    'status_pesanan_global' => 'selesai'
                ]);
                
                // Opsional: Masukkan otomatis ke antrean Payout untuk Toko
                // DB::table('tb_payouts')->insert([...]);
            }

            DB::commit();

            $pesan = ($request->keputusan == 'refund_pembeli') 
                ? 'Palu diketuk! Dana akan dikembalikan ke pembeli.' 
                : 'Palu diketuk! Komplain ditolak, dana diteruskan ke Toko.';

            return back()->with('success', $pesan);

        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'Terjadi kesalahan sistem: ' . $e->getMessage());
        }
    }
}