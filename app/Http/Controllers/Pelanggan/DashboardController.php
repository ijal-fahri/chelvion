<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\Voucher;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB; 

class DashboardController extends Controller
{
    public function index()
    {
        // Ambil produk
        $popularProducts = $this->getPopularProducts(8);
        $newProducts = $this->getPublishedProducts('Baru', 8);
        $secondProducts = $this->getPublishedProducts('Second', 8);

        // Query voucher yang masih aktif dan tersedia
        $vouchers = Voucher::where(function ($query) {
                // Stok masih ada ATAU stok unlimited (NULL)
                $query->where('stock', '>', DB::raw('times_used'))
                      ->orWhereNull('stock');
            })
            ->where(function ($query) {
                // Belum expired ATAU tidak ada expiry date
                $query->where('expiry_date', '>=', now())
                      ->orWhereNull('expiry_date');
            })
            ->orderBy('created_at', 'desc') // Voucher terbaru dulu
            ->get(); // Ambil SEMUA voucher yang memenuhi kriteria

        // Data untuk navbar
        $orderCount = 0;
        $cartCount = 0;
        $wishlistCount = 0;
        
        if (auth()->check()) {
            // Uncomment jika sudah ada relasi:
            // $cartCount = auth()->user()->cartItems()->count();
            // $orderCount = auth()->user()->orders()->whereIn('status', ['pending', 'processing'])->count();
        }

        return view('pelanggan.dashboard', compact(
            'popularProducts', 
            'newProducts', 
            'secondProducts', 
            'vouchers',
            'orderCount',
            'cartCount',
            'wishlistCount'
        ));
    }

    private function getPopularProducts(int $limit)
    {
        $popularProductIds = DB::table('offline_order_items')
            ->select('product_variants.product_id', DB::raw('SUM(offline_order_items.quantity) as total_sold'))
            ->join('product_variants', 'offline_order_items.product_variant_id', '=', 'product_variants.id')
            ->groupBy('product_variants.product_id')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->pluck('product_variants.product_id');

        if ($popularProductIds->isEmpty()) {
            return $this->getPublishedProducts('Baru', $limit);
        }

        $products = Product::whereIn('id', $popularProductIds)
            ->where(function ($query) {
                $query->where('display_status', 'live') 
                    ->whereNotNull('master_price')
                    ->whereNotNull('image')
                    ->whereHas('variants', function ($subQuery) {
                        $subQuery->where('status', 'published')->where('stock', '>', 0);
                    });
            })
            ->with(['variants' => function ($query) {
                $query->where('status', 'published')->where('stock', '>', 0);
            }])
            ->orderByRaw('FIELD(id, ' . $popularProductIds->implode(',') . ')')
            ->get();
            
        return $this->processProductCollection($products);
    }

    private function getPublishedProducts(string $condition, int $limit): \Illuminate\Support\Collection
    {
        $products = Product::where('status', $condition)
            ->where('display_status', 'live') 
            ->whereNotNull('master_price')
            ->whereNotNull('image')
            ->whereHas('variants', function ($query) {
                $query->where('status', 'published')->where('stock', '>', 0);
            })
            ->with(['variants' => function ($query) {
                $query->where('status', 'published')->where('stock', '>', 0);
            }])
            ->latest()
            ->take($limit)
            ->get();

        return $this->processProductCollection($products);
    }

    private function processProductCollection(Collection $products): \Illuminate\Support\Collection
    {
        return $products->map(function ($product) {
            if ($product->variants->isEmpty()) {
                return null;
            }

            $imagePath = $product->image;
            $imageUrls = ['https://placehold.co/400x400/eef2ff/4f46e5?text=N/A'];
            
            if ($imagePath) {
                $decodedImages = json_decode($imagePath, true);
                if (is_array($decodedImages) && !empty($decodedImages)) {
                    $imageUrls = array_map(function($img) {
                        if (filter_var($img, FILTER_VALIDATE_URL)) return $img;
                        if (strpos($img, 'storage/') === 0) return asset($img);
                        return asset('storage/' . $img);
                    }, $decodedImages);
                }
                elseif (is_string($imagePath)) {
                    $imageUrls = [asset('storage/' . $imagePath)];
                }
            }

            return [
                'id'            => $product->id,
                'name'          => $product->name,
                'images'        => $imageUrls,
                'display_price' => $product->master_price ?: $product->variants->min('price'),
                'master_price'  => $product->master_price,
                'total_stock'   => $product->variants->sum('stock'),
                'category'      => $product->category,
                'condition'     => $product->status,
            ];
        })->whereNotNull();
    }
}