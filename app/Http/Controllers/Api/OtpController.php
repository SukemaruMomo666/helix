<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpRegistrationMail;

class OtpController extends Controller
{
    public function sendOtp(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        $email = $request->email;
        
        $otp = rand(100000, 999999);
        
        // Simpan OTP di Cache selama 5 Menit menggunakan patokan Email
        Cache::put('otp_' . $email, $otp, now()->addMinutes(5));

        // Kirim Email via Laravel Mail
        Mail::to($email)->send(new OtpRegistrationMail($otp));

        return response()->json(['status' => 'success']);
    }

    public function verifyOtp(Request $request)
    {
        $request->validate(['email' => 'required|email', 'otp' => 'required']);
        $email = $request->email;
        $savedOtp = Cache::get('otp_' . $email);

        if ($savedOtp && $savedOtp == $request->otp) {
            Cache::forget('otp_' . $email); // Hapus OTP setelah sukses
            return response()->json(['status' => 'success']);
        }
        
        return response()->json(['status' => 'error', 'message' => 'Kode OTP Salah atau Kadaluarsa!'], 400);
    }
}