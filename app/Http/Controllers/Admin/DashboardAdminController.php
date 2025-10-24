<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\User;
use App\Models\Checkout;

class DashboardAdminController extends Controller
{
    /**
     * Menampilkan halaman dashboard admin dengan data ringkasan.
     */
    public function index()
    {
        // Menghitung total produk untuk ditampilkan di card statistik
        $productCount = Product::count();
        
        // Menghitung total user. Filter 'role' dihapus sementara untuk menghindari error.
        // TODO: Pastikan kolom 'role' ada di tabel 'users' dan jalankan migrasi.
        $userCount = User::count();

        // Menghitung total semua order/checkout yang pernah ada
        $checkoutCount = Checkout::count();

        // [INI YANG BARU] Menghitung jumlah checkout yang statusnya masih 'proses' untuk notifikasi
        $pendingCheckoutCount = Checkout::where('status', 'proses')->count();

        // Mengirim semua data yang dibutuhkan ke view 'admin.dashboard'
        return view('admin.dashboard', compact(
            'productCount',
            'userCount',
            'checkoutCount',
            'pendingCheckoutCount' // Kirim data notifikasi ke view
        ));
    }
}

