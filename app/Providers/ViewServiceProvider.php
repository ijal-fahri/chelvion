<?php

namespace App\Providers;

use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Auth; // Import Auth
use App\Models\Checkout;
use App\Models\Cart;     // Import model Cart
use App\Models\Order;    // Import model Order

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Dibiarkan kosong
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Composer LAMA Anda - untuk notifikasi checkout (tetap dipertahankan)
        // Saran: Ganti '*' dengan path view sidebar admin Anda agar lebih efisien,
        // contoh: 'admin.partials.sidebar'
        View::composer('*', function ($view) {
            if (auth()->check()) {
                $pendingCheckoutCount = Checkout::where('status', 'pending')->count();
            } else {
                $pendingCheckoutCount = 0;
            }
            $view->with('pendingCheckoutCount', $pendingCheckoutCount);
        });

        // [INI LOGIKA BARU] Composer khusus untuk Navbar Pelanggan
        // Ini akan memperbaiki error 'Undefined variable $orderCount'
        View::composer('components.navbar-pelanggan', function ($view) {
            if (Auth::check()) {
                // Jika user login, hitung data dari database
                $userId = Auth::id();
                $cartCount = Cart::where('user_id', $userId)->count();
                // Menghitung pesanan yang statusnya masih aktif (belum selesai/batal)
                $orderCount = Order::where('user_id', $userId)
                                   ->whereNotIn('status', ['Selesai', 'Dibatalkan'])
                                   ->count();
            } else {
                // Jika user belum login, set nilai default ke 0
                $cartCount = 0;
                $orderCount = 0;
            }

            // Kirim variabel 'cartCount' dan 'orderCount' ke view component navbar
            $view->with('cartCount', $cartCount)->with('orderCount', $orderCount);
        });
    }
}