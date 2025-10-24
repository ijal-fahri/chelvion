<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request; // Diperlukan untuk method 'show'
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class OrderanPelangganController extends Controller
{
    /**
     * Menampilkan daftar semua pesanan pelanggan (halaman riwayat)
     */
    public function index()
    {
        $userId = Auth::id();

        // Statistik
        $allUserOrders = Order::where('user_id', $userId)->get();
        $totalPesanan = $allUserOrders->count();
        $dalamPengiriman = $allUserOrders->whereIn('status', ['Dikirim', 'Diproses', 'Menunggu Diambil'])->count();
        $selesai = $allUserOrders->where('status', 'Selesai')->count();

        // Query dasar
        $query = Order::where('user_id', $userId)->with('items.variant');

        // Terapkan filter pencarian
        if (request()->filled('search')) {
            $search = request()->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('order_number', 'like', "%{$search}%")
                  ->orWhereHas('items', function ($itemQuery) use ($search) {
                      $itemQuery->where('product_name', 'like', "%{$search}%");
                  });
            });
        }

        // Terapkan filter status
        if (request()->filled('status')) {
            $query->where('status', request()->input('status'));
        }

        // Terapkan filter tanggal
        if (request()->filled('date')) {
            $dateFilter = request()->input('date');
            if ($dateFilter === '7days') {
                $query->where('created_at', '>=', Carbon::now()->subDays(7));
            } elseif ($dateFilter === '30days') {
                $query->where('created_at', '>=', Carbon::now()->subDays(30));
            }
        }

        // Eksekusi query
        $orders = $query->latest()->paginate(5)->withQueryString();

        return view('pelanggan.orderan.index', compact(
            'orders',
            'totalPesanan',
            'dalamPengiriman',
            'selesai'
        ));
    }

    /**
     * [BARU] Menampilkan halaman detail satu pesanan
     * Ini adalah method yang hilang dan menyebabkan error
     */
    public function show($id)
    {
        $userId = Auth::id();

        // Cari order berdasarkan ID DAN pastikan milik user yang login
        // Eager load relasi yang dibutuhkan
        $order = Order::with([
                    'items', 
                    'items.variant', 
                    'shippingCabang', 
                    'pickupCabang'
                 ])
                 ->where('id', $id)
                 ->where('user_id', $userId)
                 ->firstOrFail(); // Akan 404 jika tidak ditemukan atau bukan milik user

        // Kirim data order ke view 'show'
        return view('pelanggan.orderan.show', compact('order'));
    }
}