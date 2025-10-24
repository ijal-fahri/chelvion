<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\OfflineOrder;
use App\Models\Order;
use App\Models\TradeIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirRiwayatController extends Controller
{
    /**
     * Menampilkan halaman utama riwayat transaksi.
     */
    public function index()
    {
        return view('kasir.riwayat.index');
    }

    /**
     * Menyediakan data riwayat transaksi dalam format JSON.
     */
    public function getData(Request $request)
    {
        $kasir = Auth::user();
        if (!$kasir || !$kasir->cabang_id) {
            return response()->json(['error' => 'User tidak terautentikasi atau tidak memiliki cabang.'], 403);
        }
        $cabangId = $kasir->cabang_id;

        $period = $request->input('period', '7days');
        $startDate = match ($period) {
            'today' => Carbon::today(),
            '7days' => Carbon::now()->subDays(6)->startOfDay(),
            'this_month' => Carbon::now()->startOfMonth(),
            'all' => null, // Tambah opsi 'Semua Waktu'
            default => Carbon::now()->subDays(6)->startOfDay(), // Default ke 7 hari jika tidak cocok
        };

        // Eager load relasi yang dibutuhkan
        $offlineOrders = OfflineOrder::with('items.productVariant') 
            ->where('cabang_id', $cabangId)
            ->where('status', 'selesai') // Pastikan hanya yang selesai
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->latest() // Urutkan terbaru
            ->get();
        
        $onlineOrders = Order::with(['items.variant', 'user']) 
            ->where(fn($q) => $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))
            ->where('status', 'Selesai') // Pastikan hanya yang selesai
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->latest() // Urutkan terbaru
            ->get();
            
        $tradeIns = TradeIn::where('cabang_id', $cabangId)
            ->when($startDate, fn($q) => $q->where('created_at', '>=', $startDate))
            ->get(); // Trade-in mungkin tidak perlu diurutkan berdasarkan tanggal

        $transactions = collect();

        // Proses Offline Orders
        foreach ($offlineOrders as $trx) {
            $firstItem = $trx->items->first();
            $imageUrl = 'https://placehold.co/80x80/eef2ff/4f46e5?text=OFF';
            if ($firstItem && $firstItem->productVariant && $firstItem->productVariant->first_image_url) {
                $imageUrl = $firstItem->productVariant->first_image_url;
            }
            // [+++ TAMBAHAN +++] Subtotal offline sama dengan total
            $subtotal = $trx->total_amount; 

            $transactions->push([
                'id' => $trx->invoice_number,
                'is_offline' => true, // Flag untuk membedakan
                'customer' => $trx->customer_name ?? 'Walk-in',
                'phone' => $trx->customer_phone,
                'email' => $trx->customer_email,
                'date' => $trx->created_at->format('Y-m-d H:i'), // Tambahkan waktu
                'method' => $trx->payment_method,
                'subtotal' => (float) $subtotal, // [+++ TAMBAHAN +++]
                'discount_amount' => 0, // [+++ TAMBAHAN +++] Offline (asumsi) tidak ada diskon
                'voucher_code' => null, // [+++ TAMBAHAN +++]
                'total' => (float) $trx->total_amount,
                'items' => $trx->items->map(fn($item) => [
                    'name' => $item->product_name,
                    'variant_info' => $item->variant_info,
                    'qty' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) $item->subtotal,
                ]),
                'image' => $imageUrl,
            ]);
        }

        // Proses Online Orders
        foreach ($onlineOrders as $trx) {
            $firstItem = $trx->items->first();
            $imageUrl = 'https://placehold.co/80x80/dbeafe/1e40af?text=ON';
            if ($firstItem && $firstItem->variant && $firstItem->variant->first_image_url) {
                $imageUrl = $firstItem->variant->first_image_url;
            }
            // [+++ TAMBAHAN +++] Hitung subtotal online
            $subtotal = $trx->items->sum(fn($item) => $item->price * $item->quantity);

            $transactions->push([
                'id' => $trx->order_number,
                'is_offline' => false, // Flag untuk membedakan
                'customer' => $trx->user->name ?? $trx->receiver_name ?? 'Pelanggan Online',
                'phone' => $trx->phone_number,
                'email' => $trx->user->email ?? '-',
                'date' => $trx->created_at->format('Y-m-d H:i'), // Tambahkan waktu
                'method' => $trx->payment_method,
                'subtotal' => (float) $subtotal, // [+++ TAMBAHAN +++]
                'discount_amount' => (float) $trx->discount_amount, // [+++ TAMBAHAN +++]
                'voucher_code' => $trx->voucher_code, // [+++ TAMBAHAN +++]
                'total' => (float) $trx->total_price,
                'items' => $trx->items->map(fn($item) => [
                    'name' => $item->product_name,
                    'variant_info' => $item->variant_info,
                    'qty' => $item->quantity,
                    'price' => (float) $item->price,
                    'subtotal' => (float) ($item->price * $item->quantity),
                ]),
                'image' => $imageUrl,
            ]);
        }
        
        // Gabungkan dan urutkan berdasarkan tanggal terbaru
        $sortedTransactions = $transactions->sortByDesc(function($trx) {
            // Pastikan format tanggal konsisten untuk sorting
            return Carbon::parse($trx['date'])->timestamp; 
        })->values();

        // Hitung Summary (tidak perlu diubah)
        $totalRevenue = $sortedTransactions->sum('total');
        // ... dst ...

        // Data Grafik (tidak perlu diubah)
        $paymentMethodsChartData = $sortedTransactions->groupBy('method')->map->count();
        $dailySummariesChartData = $sortedTransactions->groupBy(function($trx) {
                                        // Grup berdasarkan tanggal saja (YYYY-MM-DD)
                                        return Carbon::parse($trx['date'])->format('Y-m-d');
                                    })
                                    ->map(function ($dayTransactions, $date) {
                                        return [
                                            'date' => $date,
                                            'totalRevenue' => $dayTransactions->sum('total'),
                                            'transactionCount' => $dayTransactions->count(),
                                        ];
                                    })
                                    ->sortByDesc('date') // Urutkan ringkasan harian
                                    ->values();

        return response()->json([
            'transactions' => $sortedTransactions,
            'summary' => [
                'totalRevenue' => $totalRevenue,
                'totalTransactions' => $sortedTransactions->count(),
                'tradeInCount' => $tradeIns->count(),
            ],
            'charts' => [
                'paymentMethods' => $paymentMethodsChartData,
                'dailySummaries' => $dailySummariesChartData,
            ]
        ]);
    }
}