<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class KasirMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Cek apakah user sudah login dan usertype-nya 'kasir'
        if (Auth::check() && Auth::user()->usertype === 'kasir') {
            return $next($request);
        }

        // Jika tidak, tendang ke halaman login
        return redirect()->route('login')->with('error', 'Anda tidak memiliki hak akses.');
    }
}
