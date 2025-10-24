<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockRequest;
use App\Models\TradeIn;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffDashboardController extends Controller
{
    /**
     * Menampilkan data dinamis untuk dashboard Staf Gudang.
     */
    public function index()
    {
        $cabangId = Auth::user()->cabang_id;

        // --- DATA UNTUK KARTU STATISTIK ---
        $summary = [
            'totalStok' => ProductVariant::where('cabang_id', $cabangId)->sum('stock'),
            'barangMasukHariIni' => TradeIn::where('cabang_id', $cabangId)->where('status', 'selesai')->whereDate('updated_at', today())->count(),
            'barangKeluarHariIni' => StockRequest::where('cabang_id', $cabangId)->where('status', 'approved')->whereDate('updated_at', today())->sum('quantity'),
            'menungguQC' => TradeIn::where('cabang_id', $cabangId)->where('status', 'perlu_qc')->count(),
        ];

        // --- DATA UNTUK GRAFIK AKTIVITAS STOK (7 HARI) ---
        $barangMasuk = TradeIn::select(DB::raw('DATE(updated_at) as date'), DB::raw('count(*) as count'))
            ->where('cabang_id', $cabangId)
            ->where('status', 'selesai')
            ->where('updated_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')->pluck('count', 'date');

        $barangKeluar = StockRequest::select(DB::raw('DATE(updated_at) as date'), DB::raw('sum(quantity) as count'))
            ->where('cabang_id', $cabangId)
            ->where('status', 'approved')
            ->where('updated_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')->pluck('count', 'date');
        
        $stockActivityData = ['labels' => [], 'in' => [], 'out' => []];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $stockActivityData['labels'][] = $date->isoFormat('ddd'); // Format hari: Sen, Sel, Rab
            $stockActivityData['in'][] = $barangMasuk[$dateString] ?? 0;
            $stockActivityData['out'][] = $barangKeluar[$dateString] ?? 0;
        }

        // --- DATA UNTUK GRAFIK KOMPOSISI STOK ---
        $stockComposition = Product::select('products.category', DB::raw('SUM(product_variants.stock) as total_stock'))
            ->join('product_variants', 'products.id', '=', 'product_variants.product_id')
            ->where('product_variants.cabang_id', $cabangId)
            ->groupBy('products.category')
            ->pluck('total_stock', 'category');

        $stockCompositionData = [
            'labels' => $stockComposition->keys(),
            'values' => $stockComposition->values(),
        ];

        // --- DATA UNTUK TABEL PRODUK TERBARU ---
        $latestProducts = Product::whereHas('variants', function($q) use ($cabangId) {
            $q->where('cabang_id', $cabangId);
        })
        ->with(['variants' => function($q) use ($cabangId) {
            $q->where('cabang_id', $cabangId);
        }])
        ->latest('updated_at') // Urutkan berdasarkan produk yang baru diupdate
        ->take(5) // Ambil 5 produk teratas
        ->get()
        ->map(function($p) {
            $p->total_stock = $p->variants->sum('stock');
            return $p;
        });

        return view('staff.dashboard.index', compact(
            'summary', 
            'stockActivityData', 
            'stockCompositionData',
            'latestProducts'
        ));
    }
}
