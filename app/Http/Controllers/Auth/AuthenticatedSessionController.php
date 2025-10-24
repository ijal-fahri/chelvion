<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        // Ambil usertype dari user yang login
        $usertype = $request->user()->usertype;

        // Arahkan user sesuai dengan usertype-nya
        switch ($usertype) {
            case 'owner':
                return redirect()->intended('/owner/dashboard');

            case 'admin':
                // Nanti kamu bisa buat dashboard khusus admin cabang
                return redirect()->intended('/admin/dashboard'); 

            case 'staf_gudang':
                return redirect()->intended('/staff/dashboard');

            case 'kasir':
                // Nanti kamu bisa buat dashboard khusus kasir
                return redirect()->intended('/kasir/dashboard');

            default: // Untuk 'user' (pelanggan) dan lainnya
                return redirect()->intended('/');
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}

