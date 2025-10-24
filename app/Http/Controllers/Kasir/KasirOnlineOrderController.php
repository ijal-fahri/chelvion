<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirOnlineOrderController extends Controller
{
    /**
     * Menampilkan halaman utama transaksi online.
     */
    public function index()
    {
        return view('kasir.online.index');
    }

    /**
     * Menyediakan data pesanan untuk DataTables.
     */
    public function getData(Request $request)
    {
        if (!Auth::check() || !Auth::user()->cabang_id) {
            return response()->json(['error' => 'Unauthorized or no branch assigned'], 403);
        }

        $cabangId = Auth::user()->cabang_id;
        // Status aktif mungkin perlu disesuaikan jika ingin menampilkan status lain
        $activeStatuses = ['pending', 'diproses', 'dikirim', 'menunggu diambil']; 

        $query = Order::with(['user', 'items.product', 'items.variant'])
            ->whereHas('items.product') // Pastikan relasi ada
            ->whereHas('items.variant') // Pastikan relasi ada
            ->where(function($q) use ($cabangId) {
                $q->where(function($subq) use ($cabangId) {
                    $subq->where('delivery_method', 'antar')->where('shipping_cabang_id', $cabangId);
                })->orWhere(function($subq) use ($cabangId) {
                    $subq->where('delivery_method', 'ambil')->where('pickup_cabang_id', $cabangId);
                });
            })
            // Gunakan LOWER case untuk status agar tidak case-sensitive
            ->whereIn(DB::raw('LOWER(status)'), $activeStatuses) 
            ->latest();

        $orders = $query->get();

        // Format data agar sesuai dengan yang diharapkan frontend
        $formattedData = $orders->map(function($order) {
            $firstItem = $order->items->first();

            // Ambil gambar dari varian
            $imageUrl = 'https://placehold.co/80x80/eef2ff/4f46e5?text=VAR'; // Default placeholder
            if ($firstItem && $firstItem->variant && $firstItem->variant->first_image_url) {
                $imageUrl = $firstItem->variant->first_image_url;
            }

            // [+++ TAMBAHAN +++] Hitung subtotal asli
            $subtotal = $order->items->sum(fn($item) => $item->price * $item->quantity);

            return [
                'id' => $order->order_number,
                'customer' => $order->user->name ?? 'Pelanggan Dihapus',
                'phone' => $order->phone_number ?? '-',
                'date' => $order->created_at->format('Y-m-d H:i'), // Tambahkan jam
                'type' => $order->delivery_method === 'antar' ? 'delivery' : 'pickup',
                'address' => $order->full_address,
                'status' => $order->status,
                'subtotal' => (float) $subtotal, // [+++ TAMBAHAN +++] Kirim subtotal
                'discount_amount' => (float) $order->discount_amount, // [+++ TAMBAHAN +++] Kirim diskon
                'voucher_code' => $order->voucher_code, // [+++ TAMBAHAN +++] Kirim kode voucher
                'total' => (float) $order->total_price, // Ini harga final
                'items' => $order->items->map(function($item) {
                    return [
                        'name' => $item->product_name,
                        'qty' => $item->quantity,
                        'price' => (float) $item->price,
                        'variant_info' => $item->variant_info, 
                        'subtotal' => (float) ($item->price * $item->quantity), // [+++ TAMBAHAN +++] Subtotal per item
                    ];
                }),
                'image' => $imageUrl
            ];
        });

        return response()->json([
            'delivery' => $formattedData->where('type', 'delivery')->values(),
            'pickup' => $formattedData->where('type', 'pickup')->values()
        ]);
    }
    /**
     * Menyediakan data untuk summary cards.
     */
    public function getSummary()
    {
        if (!Auth::check() || !Auth::user()->cabang_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }

        $cabangId = Auth::user()->cabang_id;
        $today = Carbon::today();

        $baseQuery = Order::where(function($q) use ($cabangId) {
            $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId);
        });

        $newOrdersToday = (clone $baseQuery)->whereDate('created_at', $today)->count();

        // gunakan LOWER untuk case-insensitive
        $toShip = (clone $baseQuery)
            ->where('delivery_method', 'antar')
            ->whereIn(DB::raw('LOWER(status)'), ['pending', 'diproses'])
            ->count();

        $forPickup = (clone $baseQuery)
            ->where('delivery_method', 'ambil')
            ->whereIn(DB::raw('LOWER(status)'), ['pending', 'menunggu diambil'])
            ->count();
            
        $revenueToday = (clone $baseQuery)->whereDate('created_at', $today)->sum('total_price');

        return response()->json([
            'new_orders' => $newOrdersToday,
            'to_ship' => $toShip,
            'for_pickup' => $forPickup,
            'online_revenue' => (float) $revenueToday,
        ]);
    }

    /**
     * Mengubah status pesanan.
     */
    public function updateStatus(Request $request, $order_number)
    {
        if (!Auth::check() || !Auth::user()->cabang_id) {
            return response()->json(['error' => 'Unauthorized'], 403);
        }
        
        $cabangId = Auth::user()->cabang_id;

        $order = Order::where('order_number', $order_number)
            ->where(function($q) use ($cabangId) {
                $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId);
            })->firstOrFail();

        $validated = $request->validate(['status' => 'required|string']);
        $order->status = $validated['status'];
        $order->save();

        return response()->json(['success' => true, 'message' => 'Status pesanan berhasil diubah.']);
    }
}