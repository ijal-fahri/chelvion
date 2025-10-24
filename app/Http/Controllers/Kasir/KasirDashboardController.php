<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItem;
use App\Models\Order;          // <-- Model untuk pesanan online
use App\Models\OrderItem;       // <-- Model untuk item pesanan online
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class KasirDashboardController extends Controller
{
    public function index()
    {
        $cabangId = Auth::user()->cabang_id;

        if (!$cabangId) {
            // Handle jika kasir tidak punya cabang
            return view('kasir.dashboard.index', [
                'todaysRevenue' => 0, 'todaysTransactions' => 0, 'todaysProductsSold' => 0,
                'chartLabels' => [], 'chartData' => [], 'paymentMethodData' => collect(),
                'topProducts' => [], 'recentTransactions' => [],
            ]);
        }

        // === 1. STATISTIK KARTU UTAMA (GABUNGAN) ===
        // Tidak perlu diubah, sudah benar

        $offlineRevenue = OfflineOrder::where('cabang_id', $cabangId)->whereDate('created_at', Carbon::today())->sum('total_amount');
        $onlineRevenue = Order::where(fn($q) => $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))
            ->whereDate('created_at', Carbon::today())->where('status', 'Selesai')->sum('total_price');
        $todaysRevenue = $offlineRevenue + $onlineRevenue;

        $offlineTransactions = OfflineOrder::where('cabang_id', $cabangId)->whereDate('created_at', Carbon::today())->count();
        $onlineTransactions = Order::where(fn($q) => $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))
            ->whereDate('created_at', Carbon::today())->count();
        $todaysTransactions = $offlineTransactions + $onlineTransactions;

        $offlineProductsSold = OfflineOrderItem::whereHas('order', fn($q) => $q->where('cabang_id', $cabangId)->whereDate('created_at', Carbon::today()))->sum('quantity');
        $onlineProductsSold = OrderItem::whereHas('order', fn($q) => $q->where(fn($sq) => $sq->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))->whereDate('created_at', Carbon::today()))->sum('quantity');
        $todaysProductsSold = $offlineProductsSold + $onlineProductsSold;

        // === 2. DATA GRAFIK TREN PENJUALAN 7 HARI (GABUNGAN) ===
        // Tidak perlu diubah, sudah benar

        $offlineSales = OfflineOrder::where('cabang_id', $cabangId)
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())
            ->groupBy('date') ->orderBy('date', 'asc')
            ->get([DB::raw('DATE(created_at) as date'), DB::raw('sum(total_amount) as total')]) ->pluck('total', 'date');
        $onlineSales = Order::where(fn($q) => $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))
            ->where('created_at', '>=', Carbon::now()->subDays(6)->startOfDay())->where('status', 'Selesai')
            ->groupBy('date')->orderBy('date', 'asc')
            ->get([DB::raw('DATE(created_at) as date'), DB::raw('sum(total_price) as total')]) ->pluck('total', 'date');
        $mergedSales = $offlineSales->toArray();
        foreach ($onlineSales as $date => $total) { $mergedSales[$date] = ($mergedSales[$date] ?? 0) + $total; }
        $chartLabels = []; $chartData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i); $dateString = $date->format('Y-m-d');
            $chartLabels[] = $i === 0 ? 'Hari Ini' : $date->isoFormat('D MMM');
            $chartData[] = $mergedSales[$dateString] ?? 0;
        }

        // === 3. DATA GRAFIK METODE PEMBAYARAN HARI INI (GABUNGAN) ===
        // Tidak perlu diubah, sudah benar

        $offlinePayments = OfflineOrder::where('cabang_id', $cabangId)->whereDate('created_at', Carbon::today())
            ->groupBy('payment_method')->select('payment_method', DB::raw('count(*) as count'))->pluck('count', 'payment_method');
        $onlinePayments = Order::where(fn($q) => $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))
            ->whereDate('created_at', Carbon::today())->groupBy('payment_method')->select('payment_method', DB::raw('count(*) as count'))->pluck('count', 'payment_method');
        $paymentMethodData = $offlinePayments;
        foreach ($onlinePayments as $method => $count) { $paymentMethodData[$method] = ($paymentMethodData[$method] ?? 0) + $count; }


        // === 4. DATA PRODUK TERLARIS HARI INI (GABUNGAN) ===
        // Tidak perlu diubah, sudah benar

        $offlineTop = OfflineOrderItem::whereHas('order', fn($q) => $q->where('cabang_id', $cabangId)->whereDate('created_at', Carbon::today()))
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'))->groupBy('product_name')->pluck('total_sold', 'product_name');
        $onlineTop = OrderItem::whereHas('order', fn($q) => $q->where(fn($sq) => $sq->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))->whereDate('created_at', Carbon::today()))
            ->select('product_name', DB::raw('SUM(quantity) as total_sold'))->groupBy('product_name')->pluck('total_sold', 'product_name');
        $mergedTopProducts = $offlineTop;
        foreach ($onlineTop as $name => $count) { $mergedTopProducts[$name] = ($mergedTopProducts[$name] ?? 0) + $count; }
        $mergedTopProducts = $mergedTopProducts->sortDesc()->take(5);
        $topProducts = $mergedTopProducts->map(fn ($total, $name) => (object)['product_name' => $name, 'total_sold' => $total])->values();


        // === 5. DATA TRANSAKSI TERAKHIR (GABUNGAN) ===
        // [+++ PERUBAHAN DI SINI +++]
        
        $recentOffline = OfflineOrder::where('cabang_id', $cabangId)->with('items')->latest()->limit(5)->get();
        $recentOnline = Order::where(fn($q) => $q->where('shipping_cabang_id', $cabangId)->orWhere('pickup_cabang_id', $cabangId))
                        ->with(['items', 'user'])->latest()->limit(5)->get();

        $mergedTransactions = collect();
        
        // Proses Offline
        foreach($recentOffline as $trx) {
            $subtotal = $trx->total_amount; // Subtotal offline = total
            $mergedTransactions->push([
                'id' => $trx->id, // Gunakan ID asli untuk detail
                'is_offline' => true,
                'invoice_number' => $trx->invoice_number,
                'customer_name' => $trx->customer_name ?? 'Walk-in',
                'customer_phone' => $trx->customer_phone,
                'customer_email' => $trx->customer_email,
                'created_at' => $trx->created_at,
                'subtotal' => (float) $subtotal, // [+++ TAMBAHAN +++]
                'discount_amount' => 0, // [+++ TAMBAHAN +++]
                'voucher_code' => null, // [+++ TAMBAHAN +++]
                'total_amount' => (float) $trx->total_amount, // Nama field total
                'payment_method' => $trx->payment_method,
                'items' => $trx->items->map(fn($item) => [
                    'product_name' => $item->product_name,
                    'variant_info' => $item->variant_info,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) $item->subtotal,
                ])
            ]);
        }
        
        // Proses Online
        foreach($recentOnline as $trx) {
            $subtotal = $trx->items->sum(fn($item) => $item->price * $item->quantity); // Hitung subtotal
            $mergedTransactions->push([
                'id' => $trx->id, // Gunakan ID asli untuk detail
                'is_offline' => false,
                'invoice_number' => $trx->order_number,
                'customer_name' => $trx->user->name ?? $trx->receiver_name ?? 'Online',
                'customer_phone' => $trx->phone_number,
                'customer_email' => $trx->user->email ?? '-',
                'created_at' => $trx->created_at,
                'subtotal' => (float) $subtotal, // [+++ TAMBAHAN +++]
                'discount_amount' => (float) $trx->discount_amount, // [+++ TAMBAHAN +++]
                'voucher_code' => $trx->voucher_code, // [+++ TAMBAHAN +++]
                'total_amount' => (float) $trx->total_price, // Nama field total (samakan)
                'payment_method' => $trx->payment_method,
                'items' => $trx->items->map(fn($item) => [
                    'product_name' => $item->product_name,
                    'variant_info' => $item->variant_info,
                    'quantity' => $item->quantity,
                    'subtotal' => (float) ($item->price * $item->quantity),
                ])
            ]);
        }
        
        // Urutkan gabungan dan ambil 5 terbaru
        $recentTransactions = $mergedTransactions->sortByDesc('created_at')->take(5)->values();
        // [+++ AKHIR PERUBAHAN +++]

        return view('kasir.dashboard.index', compact(
            'todaysRevenue', 'todaysTransactions', 'todaysProductsSold',
            'chartLabels', 'chartData', 'paymentMethodData',
            'topProducts', 'recentTransactions'
        ));
    }
}