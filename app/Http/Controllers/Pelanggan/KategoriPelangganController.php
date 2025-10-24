<?php

namespace App\Http\Controllers\Pelanggan;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Product;

class KategoriPelangganController extends Controller
{
    public function showCategory(Request $request) 
    {
        $brandFilter = $request->input('brand');
        $typeFilter = $request->input('type'); 
        $sortFilter = $request->input('sort', 'newest');
        $searchQuery = $request->input('search'); // Tambahkan parameter search

        $query = Product::query()
            ->where('display_status', 'live') 
            ->whereHas('publishedVariants', function ($q) {
                $q->where('stock', '>', 0);
            });

        // Filter Search (prioritas tertinggi)
        if ($searchQuery) {
            $query->where(function($q) use ($searchQuery) {
                $q->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($searchQuery) . '%'])
                  ->orWhereRaw('LOWER(description) LIKE ?', ['%' . strtolower($searchQuery) . '%'])
                  ->orWhereRaw('LOWER(category) LIKE ?', ['%' . strtolower($searchQuery) . '%']);
            });
        }

        // Filter Brand
        if ($brandFilter) {
            $query->whereRaw('LOWER(name) LIKE ?', ['%' . strtolower($brandFilter) . '%']);
        }

        // Filter Type/Category
        if ($typeFilter) {
            $query->where('category', $typeFilter);
        }

        // Sorting
        switch ($sortFilter) {
            case 'price-low':
                $query->orderBy('master_price', 'asc'); 
                break;
            case 'price-high':
                $query->orderBy('master_price', 'desc'); 
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $products = $query->paginate(12)->withQueryString();

        // Data untuk Navbar
        $orderCount = 0;
        $cartCount = 0;
        $wishlistCount = 0;
        
        if (auth()->check()) {
            $user = auth()->user();
            // Uncomment jika sudah ada relasi:
            // $cartCount = $user->cartItems()->count();
            // $orderCount = $user->orders()->whereIn('status', ['pending', 'processing'])->count();
        }

        return view('pelanggan.kategori.index', [
            'products' => $products,
            'brandFilter' => $brandFilter, 
            'typeFilter' => $typeFilter,   
            'sortFilter' => $sortFilter,
            'searchQuery' => $searchQuery, // Kirim ke view
            'orderCount' => $orderCount,
            'cartCount' => $cartCount,
            'wishlistCount' => $wishlistCount
        ]);
    }
}