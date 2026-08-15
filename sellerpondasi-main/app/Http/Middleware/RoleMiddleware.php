<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
{
    if (!auth()->check() || !in_array(auth()->user()->level, $roles)) {
        // Jika bukan role yang sesuai, lempar ke home atau login
        return redirect('/')->with('error', 'Anda tidak memiliki akses ke halaman ini.');
    }

    return $next($request);
}
    
}
