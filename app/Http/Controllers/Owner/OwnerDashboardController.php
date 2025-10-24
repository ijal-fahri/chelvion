<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OwnerDashboardController extends Controller
{
    /**
     * Menampilkan halaman dashboard owner dengan data ringkasan.
     */
    public function index()
    {
        // --- STATISTIK KARTU ---
        $revenue30Days = Checkout::where('status', 'paid')
            ->where('created_at', '>=', now()->subDays(30))
            ->sum('total_price');

        $estimatedProfit = $revenue30Days * 0.30; 

        $customerCount = User::where('usertype', 'user')->count();
        $newCustomerCount = User::where('usertype', 'user')->where('created_at', '>=', now()->subDays(30))->count();

        $productsSoldCount = DB::table('checkout_items')
            ->join('checkouts', 'checkout_items.checkout_id', '=', 'checkouts.id')
            ->where('checkouts.status', 'paid')
            ->where('checkouts.created_at', '>=', now()->subDays(30))
            ->sum('checkout_items.quantity');

        // --- DATA GRAFIK PENDAPATAN BULANAN ---
        $revenueByMonth = Checkout::select(
                DB::raw('SUM(total_price) as revenue'),
                DB::raw("DATE_FORMAT(created_at, '%b') as month_name"),
                DB::raw("DATE_FORMAT(created_at, '%Y-%m') as month_sort")
            )
            ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
            ->where('status', 'paid')
            ->groupBy('month_name', 'month_sort')
            ->orderBy('month_sort', 'ASC')
            ->get();
        
        $monthlyRevenueChartData = [
            'labels' => $revenueByMonth->pluck('month_name'),
            'values' => $revenueByMonth->pluck('revenue'),
        ];

        // --- DATA TRANSAKSI BERNILAI TINGGI ---
        $highValueTransactions = Checkout::with('user')
            ->where('status', 'paid')
            ->orderBy('total_price', 'DESC')
            ->take(3)
            ->get();

        // --- DATA GRAFIK KATEGORI TERLARIS ---
        $categorySales = Product::select('products.category', DB::raw('SUM(checkout_items.quantity) as total_sold'))
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->join('checkout_items', 'product_variants.id', '=', 'checkout_items.product_variant_id')
            ->join('checkouts', 'checkout_items.checkout_id', '=', 'checkouts.id')
            ->where('checkouts.status', 'paid')
            ->groupBy('products.category')
            ->orderByDesc('total_sold')
            ->get();

        $categoryPieChartData = [
            'labels' => $categorySales->pluck('category'),
            'values' => $categorySales->pluck('total_sold'),
        ];

        // Mengirim semua data ke view
        return view('owner.dashboard.index', compact(
            'revenue30Days',
            'estimatedProfit',
            'customerCount',
            'newCustomerCount',
            'productsSoldCount',
            'monthlyRevenueChartData',
            'highValueTransactions',
            'categoryPieChartData'
        ));
    }
}

