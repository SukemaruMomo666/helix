<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {
        // Mengecek agar tidak ada admin ganda jika perintah di-run 2 kali
        if (User::where('username', 'superadmin')->exists()) {
            return;
        }

        User::create([
            'nama' => 'Administrator Utama',
            'username' => 'superadmin',
            'email' => 'admin@pondasikita.com',
            'password' => Hash::make('rahasiaAdmin123'), // Password akan otomatis dienkripsi dengan aman
            'no_telepon' => '08111222333',
            'level' => 'admin',
            'status' => 'offline',
            'is_verified' => 1,
            'is_banned' => 0
        ]);
    }
}