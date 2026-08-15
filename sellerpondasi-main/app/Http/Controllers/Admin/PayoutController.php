<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PayoutController extends Controller
{
    /**
     * Menampilkan daftar permintaan Payout.
     */
    public function index(Request $request)
    {
        $status_filter = $request->get('status', 'pending');
        $search = $request->get('search');

        // 1. Statistik Keuangan (Quick Metrics)
        $stats = [
            'total_pending_count' => DB::table('tb_payouts')->where('status', 'pending')->count(),
            'total_pending_amount' => DB::table('tb_payouts')->where('status', 'pending')->sum('jumlah_payout'),
            'total_completed_amount' => DB::table('tb_payouts')->where('status', 'completed')->whereMonth('tanggal_proses', Carbon::now()->month)->sum('jumlah_payout'),
            'total_rejected' => DB::table('tb_payouts')->where('status', 'rejected')->count(),
        ];

        // 2. Query Utama dengan Join Toko & User
        // Catatan: Pastikan kolom rekening_bank, nomor_rekening, atas_nama_rekening sudah Anda tambahkan di tb_toko
        $query = DB::table('tb_payouts as p')
            ->join('tb_toko as t', 'p.toko_id', '=', 't.id')
            ->join('tb_user as u', 't.user_id', '=', 'u.id')
            ->select(
                'p.*', 
                't.nama_toko', 
                't.rekening_bank', 
                't.nomor_rekening', 
                't.atas_nama_rekening',
                'u.nama as nama_pemilik',
                'u.email as email_pemilik'
            );

        // Filter Status
        if ($status_filter !== 'semua') {
            $query->where('p.status', $status_filter);
        }

        // Filter Pencarian (Cari berdasarkan nama toko atau nomor invoice payout)
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('t.nama_toko', 'LIKE', "%$search%")
                  ->orWhere('p.id', 'LIKE', "%$search%");
            });
        }

        // Eksekusi data
        $payouts = $query->orderByRaw("FIELD(p.status, 'pending', 'completed', 'rejected')")
                         ->latest('p.tanggal_request')
                         ->paginate(15)
                         ->withQueryString();

        return view('admin.payouts.index', compact('payouts', 'status_filter', 'search', 'stats'));
    }

    /**
     * Memproses persetujuan atau penolakan Payout
     */
    public function process(Request $request, $id)
    {
        $request->validate([
            'action' => 'required|in:approve,reject',
            'catatan_admin' => 'nullable|string'
        ]);

        $payout = DB::table('tb_payouts')->where('id', $id)->first();

        if (!$payout) {
            return back()->with('error', 'Data Payout tidak ditemukan.');
        }

        if ($payout->status !== 'pending') {
            return back()->with('warning', 'Payout ini sudah diproses sebelumnya.');
        }

        $status = ($request->action === 'approve') ? 'completed' : 'rejected';

        // Update status payout
        DB::table('tb_payouts')->where('id', $id)->update([
            'status' => $status,
            'tanggal_proses' => now(),
            'catatan_admin' => $request->catatan_admin,
        ]);

        // (Opsional) Jika ditolak, Anda harus mengembalikan saldo ke toko seller di sini.
        // if ($status === 'rejected') { ... kembalikan saldo dompet_toko ... }

        $msg = ($status === 'completed') ? "Payout #PAY-" . str_pad($id, 5, '0', STR_PAD_LEFT) . " berhasil ditandai sebagai Selesai." : "Permintaan Payout ditolak.";
        $type = ($status === 'completed') ? 'success' : 'warning';

        return back()->with($type, $msg);
    }
}