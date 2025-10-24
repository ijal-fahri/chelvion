<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminRequestController extends Controller
{
    /**
     * Menampilkan halaman dashboard permintaan stok untuk Admin.
     */
    public function index()
    {
        $admin = Auth::user();
        $cabangId = $admin->cabang_id;

        // [DIUBAH] Mengurutkan berdasarkan status 'pending' terlebih dahulu, lalu tanggal terbaru
        $requests = StockRequest::where('admin_id', $admin->id)
            ->with(['productVariant.product', 'staff']) 
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC, created_at DESC")
            ->get();

        // [DIUBAH] Menambahkan URL gambar yang sudah diproses ke setiap item request
        $requests->transform(function ($request) {
            if ($request->productVariant) {
                $imageUrl = 'https://placehold.co/80x80/eef2ff/4f46e5?text=N/A'; // Default
                
                // Prioritaskan gambar varian
                if ($request->productVariant->image) {
                    $imageUrl = asset('storage/' . $request->productVariant->image);
                } 
                // Jika tidak ada, gunakan gambar utama produk
                elseif ($request->productVariant->product && $request->productVariant->product->image) {
                    $productImages = json_decode($request->productVariant->product->image, true);
                    if (is_array($productImages) && !empty($productImages[0])) {
                        $imageUrl = asset('storage/' . $productImages[0]);
                    }
                }
                
                // Tambahkan properti baru ke objek varian untuk digunakan di view
                $request->productVariant->display_image_url = $imageUrl;
            }
            return $request;
        });

        // [DIUBAH] Menambahkan URL gambar yang sudah diproses ke produk yang tersedia untuk dropdown
        $availableProducts = Product::whereHas('variants', function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'draft')->where('stock', '>', 0);
        })->with(['variants' => function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'draft')->where('stock', '>', 0);
        }])
        ->orderBy('name')
        ->get()
        ->map(function($product) {
            $imagePaths = json_decode($product->image, true);
            $imageUrl = (is_array($imagePaths) && !empty($imagePaths[0])) ? asset('storage/' . $imagePaths[0]) : 'https://placehold.co/80x80/eef2ff/4f46e5?text=N/A';

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'status' => $product->status,
                'image' => $imageUrl, // Kirim URL yang sudah jadi
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'ram' => $variant->ram,
                        'stock' => $variant->stock,
                        'image' => $variant->image ? asset('storage/' . $variant->image) : null
                    ];
                })
            ];
        });

        $summary = [
            'total' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
        ];
        
        $trend = StockRequest::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('admin_id', $admin->id)
            ->where('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()
            ->pluck('count', 'date');

        $trendData = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i)->format('Y-m-d');
            $trendData['labels'][] = $date;
            $trendData['data'][] = $trend[$date] ?? 0;
        }

        return view('admin.request.index', [
            'requests' => $requests,
            'availableProducts' => $availableProducts,
            'summary' => $summary,
            'trendData' => $trendData,
        ]);
    }

    /**
     * Menyimpan permintaan stok baru.
     */
    public function store(Request $request)
    {
        $request->validate([
            'variant_id' => 'required|exists:product_variants,id',
            'quantity' => 'required|integer|min:1',
            'notes' => 'nullable|string|max:500',
        ]);

        $admin = Auth::user();
        $variant = ProductVariant::find($request->variant_id);

        if (!$variant || $variant->cabang_id !== $admin->cabang_id || $variant->status !== 'draft') {
            return response()->json(['error' => 'Varian produk tidak ditemukan atau tidak tersedia untuk diminta.'], 404);
        }

        if ($variant->stock < $request->quantity) {
            return response()->json(['error' => 'Jumlah permintaan melebihi stok draft yang tersedia (' . $variant->stock . ' unit).'], 422);
        }
        
        StockRequest::create([
            'cabang_id' => $admin->cabang_id,
            'admin_id' => $admin->id,
            'product_variant_id' => $request->variant_id,
            'quantity' => $request->quantity,
            'notes' => $request->notes,
            'status' => 'pending',
        ]);

        return response()->json(['success' => 'Permintaan stok berhasil dikirim dan menunggu persetujuan.']);
    }

    /**
     * Menampilkan detail permintaan (untuk modal via AJAX).
     */
    public function show(StockRequest $stockRequest)
    {
        if ($stockRequest->admin_id !== Auth::id()) {
            abort(403, 'Akses ditolak.');
        }

        $stockRequest->load(['productVariant.product', 'staff']);
        return response()->json($stockRequest);
    }
}

