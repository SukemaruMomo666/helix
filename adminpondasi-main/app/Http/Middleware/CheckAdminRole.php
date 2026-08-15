<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckAdminRole
{
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        $user = Auth::user();

        // 1. Pastikan dia sudah login dan levelnya admin
        if (!$user || $user->level !== 'admin') {
            abort(403, 'Akses Ditolak! Anda bukan Admin.');
        }

        // 2. Super Admin bisa tembus semua gerbang
        if ($user->admin_role === 'super') {
            return $next($request);
        }

        // 3. Cek apakah role admin saat ini diizinkan masuk ke rute ini
        if (in_array($user->admin_role, $roles)) {
            return $next($request);
        }

        // Jika tidak punya akses
        abort(403, 'Akses Ditolak! Anda tidak memiliki izin untuk membuka halaman ini.');
    }
}