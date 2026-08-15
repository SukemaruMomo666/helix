<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;

class ForgotPasswordController extends Controller
{
    /**
     * Menampilkan halaman form lupa password
     */
    public function showLinkRequestForm()
    {
        return view('pages.lupa_password');
    }

    /**
     * Menangani proses pengiriman email link reset password
     */
    public function sendResetLinkEmail(Request $request)
    {
        // 1. Validasi Input (Cek apakah email ada di tabel users)
        // Ganti 'users' dengan nama tabel Anda jika berbeda (contoh: 'tb_user')
        $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email'    => 'Format email tidak valid.',
            'email.exists'   => 'Email ini tidak terdaftar dalam sistem kami.'
        ]);

        try {
            // 2. Buat Token Unik
            $token = Str::random(64);

            // 3. Simpan token ke tabel password_resets (Bawaan Laravel)
            // Jika tabel belum ada, silakan buat lewat migrasi atau manual
            DB::table('password_reset_tokens')->updateOrInsert(
                ['email' => $request->email],
                [
                    'email' => $request->email,
                    'token' => $token,
                    'created_at' => Carbon::now()
                ]
            );

            // 4. Kirim Email (Logika Pengiriman)
            // Untuk sementara kita gunakan simulasi log agar tidak error jika SMTP belum siap
            // Jika ingin kirim beneran, aktifkan fungsi Mail::send di bawah ini:
            /*
            Mail::send('emails.forgot_password', ['token' => $token], function($message) use($request){
                $message->to($request->email);
                $message->subject('Pulihkan Kata Sandi - Pondasikita');
            });
            */

            return back()->with('status', 'Tautan pemulihan telah dikirim ke email Anda. Silakan periksa inbox atau folder spam.');

        } catch (\Exception $e) {
            return back()->withErrors(['email' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.']);
        }
    }
}