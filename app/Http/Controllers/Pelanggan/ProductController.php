<?php

namespace App\Http\Controllers\Pelanggan;

use App\Models\Product;
use App\Models\Review; // <-- DITAMBAHKAN
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\DataTables;
use App\Http\Controllers\Controller;

class ProductController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen produk.
     *
     * @return \Illuminate\View\View
     */
    public function index()
    {
        return view('admin.products.index');
    }

    /**
     * Menyediakan data produk untuk DataTables.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     * @throws \Exception
     */
    public function getProductsData(Request $request)
    {
        $query = Product::with('variants')->latest();

        if ($request->filled('category')) {
            $query->where('category', $request->category);
        }

        return DataTables::of($query)
            ->addIndexColumn()
            ->editColumn('price', fn($row) => 'Rp' . number_format($row->price, 0, ',', '.'))
            ->addColumn('category', function($row) {
                $colors = [
                    'HP' => 'bg-blue-100 text-blue-800', 'Tab' => 'bg-purple-100 text-purple-800',
                    'Laptop' => 'bg-green-100 text-green-800', 'Accessories' => 'bg-gray-100 text-gray-800',
                ];
                $color = $colors[$row->category] ?? 'bg-gray-100 text-gray-800';
                return '<span class="text-xs font-medium px-2.5 py-1 rounded-full ' . $color . '">' . e($row->category) . '</span>';
            })
            ->addColumn('variants', function($row) {
                if($row->variants->isEmpty()){
                    return '<span class="text-xs text-gray-500">Tidak ada varian</span>';
                }
                $variantHtml = '<div class="flex flex-col gap-1.5">';
                foreach($row->variants as $variant) {
                    $stockClass = $variant->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800';
                    $variantHtml .= '<span class="text-xs font-medium ' . $stockClass . ' px-2 py-1 rounded-full whitespace-nowrap">' . e($variant->color) . ' / ' . e($variant->ram) . ' - Stok: ' . e($variant->stock) . '</span>';
                }
                return $variantHtml . '</div>';
            })
            ->editColumn('image', function($row){
                return '<img src="' . asset('storage/products/' . $row->image) . '" alt="'.e($row->name).'" class="w-14 h-14 object-cover rounded-lg shadow-md">';
            })
            ->addColumn('action', function($row){
                $showUrl = route('admin.products.show', $row->id);
                $deleteUrl = route('admin.products.destroy', $row->id);
                return '
                <div class="flex gap-2 justify-center">
                    <button class="p-2 w-9 h-9 flex items-center justify-center bg-amber-500 text-white rounded-lg hover:bg-amber-600 transition shadow edit-btn" data-url="'.$showUrl.'">
                        <i class="bi bi-pencil-fill"></i>
                    </button>
                    <form action="'.$deleteUrl.'" method="POST" class="delete-form">
                        '.csrf_field().method_field('DELETE').'
                        <button type="button" class="p-2 w-9 h-9 flex items-center justify-center bg-red-600 text-white rounded-lg hover:bg-red-700 transition shadow delete-btn" data-product="'.e($row->name).'">
                            <i class="bi bi-trash-fill"></i>
                        </button>
                    </form>
                </div>';
            })
            ->rawColumns(['category', 'variants', 'image', 'action'])
            ->make(true);
    }
    
    /**
     * Menyimpan produk baru ke database.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:HP,Tab,Laptop,Accessories',
            'description' => 'required|string', 'image' => 'required|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants' => 'required|array|min:1', 'variants.*.color' => 'required|string|max:100',
            'variants.*.ram' => 'required|string|max:100', 'variants.*.stock' => 'required|integer|min:0',
        ]);

        try {
            DB::transaction(function () use ($validated, $request) {
                $imageName = time() . '.' . $request->image->extension();
                $request->image->storeAs('public/products', $imageName);

                $product = Product::create([
                    'name' => $validated['name'], 'price' => $validated['price'],
                    'category' => $validated['category'], 'description' => $validated['description'],
                    'image' => $imageName,
                ]);

                $product->variants()->createMany($validated['variants']);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menambahkan produk: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk baru berhasil ditambahkan!');
    }

    /**
     * [ADMIN] Mengambil data produk spesifik sebagai JSON untuk modal edit.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Product $product)
    {
        $product->load('variants');
        $product->image_url = asset('storage/products/' . $product->image);
        return response()->json($product);
    }

    /**
     * [PENGGUNA] Menampilkan halaman detail produk untuk pengunjung.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\View\View
     */
    public function showDetail(Product $product)
    {
        // Muat relasi varian yang statusnya 'published' dan stoknya ada
        $product->load(['variants' => function ($query) {
            $query->where('status', 'published')->where('stock', '>', 0);
        }]);

        // [MODIFIKASI] Jika master_price kosong, ambil harga termurah dari varian sebagai harga default
        if (is_null($product->master_price)) {
            // Pastikan ada varian sebelum mencoba mengambil harga
            if ($product->variants->isNotEmpty()) {
                $product->master_price = $product->variants->min('price');
            } else {
                // Jika tidak ada varian sama sekali, beri harga 0 agar tidak error
                $product->master_price = 0;
            }
        }

        // Proses data varian untuk menambahkan image_urls
        $product->variants->transform(function ($variant) {
            // [MODIFIKASI] Kita gunakan accessor 'first_image_url' yang sudah ada di model ProductVariant
            $imageUrl = $variant->first_image_url;

            // JavaScript di halaman detail mengharapkan format array, jadi kita bungkus dalam array
            $variant->image_urls = $imageUrl ? [$imageUrl] : [];
            
            return $variant;
        });


        if ($product->variants->isEmpty()) {
            abort(404, 'Produk tidak tersedia saat ini.');
        }


        // Kumpulkan semua gambar dari produk master dan semua variannya
        $allImages = collect();
        $masterImages = json_decode($product->image, true);
        if (is_array($masterImages)) {
            foreach ($masterImages as $img) {
                $allImages->push(['url' => asset('storage/' . $img), 'variant_id' => null]);
            }
        } elseif (is_string($product->image)) {
            $allImages->push(['url' => asset('storage/' . $product->image), 'variant_id' => null]);
        }

        // Tambahkan gambar dari setiap varian, dengan menandai asalnya
        foreach ($product->variants as $variant) {
            foreach ($variant->image_urls as $imageUrl) {
                if (!$allImages->contains('url', $imageUrl)) {
                    $allImages->push([
                        'url' => $imageUrl,
                        'variant_id' => $variant->id,
                        'color' => $variant->color
                    ]);
                }
            }
        }

        if ($allImages->isEmpty()) {
            $allImages->push(['url' => 'https://placehold.co/600x600/eef2ff/4f46e5?text=N/A', 'variant_id' => null]);
        }


        // Logika Rekomendasi (tidak berubah)
        $limit = 4;
        $recommendedProducts = Product::where('category', $product->category)
            ->where('id', '!=', $product->id)
            ->whereHas('variants', function ($query) {
                $query->where('status', 'published')->where('stock', '>', 0);
            })
            ->inRandomOrder()
            ->take($limit)
            ->get();
                
        $needed = $limit - $recommendedProducts->count();
        if ($needed > 0) {
            $excludeIds = $recommendedProducts->pluck('id')->push($product->id)->all();
            $fillerProducts = Product::whereNotIn('id', $excludeIds)
                ->whereHas('variants', function ($query) {
                    $query->where('status', 'published')->where('stock', '>', 0);
                })
                ->inRandomOrder()
                ->take($needed)
                ->get();
            $recommendedProducts = $recommendedProducts->merge($fillerProducts);
        }
            
        
        // --- [INI YANG DIUBAH] ---
        // Ambil ulasan untuk produk ini, urutkan dari yang terbaru
        // Eager load 'user' untuk mendapatkan nama pemberi ulasan
        // Gunakan pagination (misal: 5 ulasan per halaman)
        $reviews = Review::where('product_id', $product->id)
                         ->with('user')
                         ->latest()
                         ->paginate(5, ['*'], 'page_reviews'); // 'page_reviews' agar tidak konflik jika ada pagination lain
        // --- [AKHIR BLOK PERUBAHAN] ---


        // --- [TAMBAHKAN INI] ---
        // Definisikan variabel default untuk navbar agar tidak error jika pengguna belum login
        $orderCount = 0;
        $cartCount = 0;

        // Jika Anda memiliki logika untuk menghitung pesanan dan keranjang, letakkan di sini.
        // Contoh (membutuhkan model Order dan Cart):
        // if (auth()->check()) {
        //     $user = auth()->user();
        //     $orderCount = $user->orders()->whereIn('status', ['pending', 'processing'])->count();
        //     $cartCount = $user->cartItems()->count();
        // }
        // ------------------------
            
        // --- [INI YANG DIUBAH] ---
        // Tambahkan $reviews ke compact()
        return view('pelanggan.detail', compact(
            'product', 
            'allImages', 
            'recommendedProducts', 
            'orderCount', 
            'cartCount',
            'reviews' // <-- DITAMBAHKAN
        ));
        // --- [AKHIR BLOK PERUBAHAN] ---
    }
    
    /**
     * Memperbarui data produk di database.
     *
     * @param \Illuminate\Http\Request $request
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255', 'price' => 'required|numeric|min:0',
            'category' => 'required|string|in:HP,Tab,Laptop,Accessories',
            'description' => 'required|string', 'image' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048',
            'variants' => 'required|array|min:1', 'variants.*.color' => 'required|string|max:100',
            'variants.*.ram' => 'required|string|max:100', 'variants.*.stock' => 'required|integer|min:0',
        ]);
        
        try {
            DB::transaction(function () use ($validated, $request, $product) {
                $imageName = $product->image;
                if ($request->hasFile('image')) {
                    if ($product->image && Storage::exists('public/products/' . $product->image)) {
                        Storage::delete('public/products/' . $product->image);
                    }
                    $imageName = time() . '.' . $request->image->extension();
                    $request->image->storeAs('public/products', $imageName);
                }

                $product->update([
                    'name' => $validated['name'], 'price' => $validated['price'],
                    'category' => $validated['category'], 'description' => $validated['description'],
                    'image' => $imageName,
                ]);

                $product->variants()->delete();
                $product->variants()->createMany($validated['variants']);
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal memperbarui produk: ' . $e->getMessage())->withInput();
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil diperbarui!');
    }

    /**
     * Menghapus produk dari database.
     *
     * @param \App\Models\Product $product
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Product $product)
    {
        try {
            DB::transaction(function () use ($product) {
                if ($product->image && Storage::exists('public/products/' . $product->image)) {
                    Storage::delete('public/products/' . $product->image);
                }
                
                $product->delete();
            });
        } catch (\Exception $e) {
            return back()->with('error', 'Gagal menghapus produk: ' . $e->getMessage());
        }

        return redirect()->route('admin.products.index')->with('success', 'Produk berhasil dihapus!');
    }
}