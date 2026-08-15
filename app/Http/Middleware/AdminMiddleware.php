<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user login DAN level-nya adalah 'admin'
        if (Auth::check() && Auth::user()->level === 'admin') {
            return $next($request);
        }

        // KUNCI RAHASIA: Jika bukan admin, pura-pura halamannya tidak ada (404)
        // Jangan gunakan return redirect()->back() atau abort(403), agar tidak memancing hacker.
        abort(404);
    }
}