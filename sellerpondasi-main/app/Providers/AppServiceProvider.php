<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // === LOGIC NAVBAR (Keranjang & User) ===
        // Menggunakan view composer agar variabel ini selalu ada saat navbar dipanggil
        // Ganti '*' dengan 'partials.navbar' atau 'layouts.app' agar lebih efisien (optional)
        View::composer('*', function ($view) {
            
            $total_item_keranjang = 0;

            // 1. Pastikan User Login
            // 2. Pastikan tabel 'tb_keranjang' sudah ada (mencegah error saat migrate fresh)
            if (Auth::check() && Schema::hasTable('tb_keranjang')) {
                
                $user = Auth::user();

                // Logic: Admin tidak punya keranjang belanja
                if ($user->level !== 'admin') {
                    $total_item_keranjang = DB::table('tb_keranjang')
                        ->where('user_id', $user->id)
                        ->sum('jumlah');
                }
            }

            // Kirim variabel ke semua View yang dirender
            $view->with('total_item_keranjang', $total_item_keranjang);
        });
    }
}