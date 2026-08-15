<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KeranjangController extends Controller
{
    /**
     * FUNGSI 1: MENGHADAPI TOMBOL "+ KERANJANG" (AJAX JSON)
     */
    public function tambah(Request $request)
    {
        // Pastikan user sudah login
        if (!Auth::check()) {
            return response()->json(['status' => 'error', 'message' => 'Silakan login terlebih dahulu.'], 401);
        }

        $request->validate([
            'barang_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $barangId = $request->barang_id;
        $qtyBaru = $request->jumlah;

        // 1. Cek Stok Barang
        $barang = DB::table('tb_barang')->where('id', $barangId)->first();

        if (!$barang || $barang->stok < $qtyBaru) {
            return response()->json(['status' => 'error', 'message' => 'Stok tidak mencukupi.'], 400);
        }

        // 2. Cek apakah barang sudah ada di keranjang user ini
        $keranjangLama = DB::table('tb_keranjang')
            ->where('user_id', $userId)
            ->where('barang_id', $barangId)
            ->first();

        if ($keranjangLama) {
            // Jika sudah ada, tambahkan jumlahnya (jangan melebihi stok)
            $totalQty = $keranjangLama->jumlah + $qtyBaru;
            if ($totalQty > $barang->stok) {
                $totalQty = $barang->stok;
            }

            // HAPUS updated_at DISINI
            DB::table('tb_keranjang')
                ->where('id', $keranjangLama->id)
                ->update(['jumlah' => $totalQty]);
        } else {
            // Jika belum ada, insert baru (HAPUS created_at & updated_at DISINI)
            DB::table('tb_keranjang')->insert([
                'user_id' => $userId,
                'barang_id' => $barangId,
                'jumlah' => $qtyBaru
            ]);
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Material berhasil ditambahkan ke keranjang!'
        ]);
    }

    /**
     * FUNGSI 2: MENGHADAPI TOMBOL "BELI SEKARANG" (DIRECT POST)
     */
    public function checkoutLangsung(Request $request)
    {
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login untuk melanjutkan pembelian.');
        }

        $request->validate([
            'barang_id' => 'required|integer',
            'jumlah' => 'required|integer|min:1'
        ]);

        $userId = Auth::id();
        $barangId = $request->barang_id;
        $jumlahBeli = $request->jumlah;

        // Cek keranjang
        $keranjangLama = DB::table('tb_keranjang')
            ->where('user_id', $userId)
            ->where('barang_id', $barangId)
            ->first();

        $cartId = null;

        if ($keranjangLama) {
            // HAPUS updated_at DISINI
            DB::table('tb_keranjang')
                ->where('id', $keranjangLama->id)
                ->update(['jumlah' => $jumlahBeli]);

            $cartId = $keranjangLama->id;
        } else {
            // HAPUS created_at & updated_at DISINI
            $cartId = DB::table('tb_keranjang')->insertGetId([
                'user_id' => $userId,
                'barang_id' => $barangId,
                'jumlah' => $jumlahBeli
            ]);
        }

        // PENTING: Arahkan ke halaman checkout dengan membawa ID keranjang
        return redirect()->route('checkout', ['selected_items' => [$cartId]]);
    }
}
