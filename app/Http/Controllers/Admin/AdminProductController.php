<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\StockRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class AdminProductController extends Controller
{
    /**
     * Menampilkan halaman manajemen produk untuk Admin Cabang.
     */
    public function index()
    {
        return view('admin.products.index');
    }

    /**
     * API untuk mendapatkan data produk dalam format JSON
     */
    public function getProductsData()
    {
        $admin = Auth::user();
        $cabangId = $admin->cabang_id;

        $products = Product::whereHas('variants', function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'published');
        })
        ->with(['variants' => function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'published');
        }])
        ->orderBy('name')
        ->get()
        ->map(function($product) {
            $decodedImages = json_decode($product->image, true);
            $imageUrls = [];
            if (is_array($decodedImages) && !empty($decodedImages)) {
                foreach ($decodedImages as $path) {
                    $imageUrls[] = asset('storage/' . $path);
                }
            } else {
                if (!empty($product->image) && !is_array(json_decode($product->image))) {
                     $imageUrls[] = asset('storage/' . $product->image);
                } else {
                     $imageUrls[] = 'https://placehold.co/300x300/e2e8f0/64748b?text=No+Image';
                }
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'category' => $product->category,
                'condition' => $product->status,
                'display_status' => $product->display_status,
                'description' => $product->description,
                'master_price' => $product->master_price,
                'master_images' => $imageUrls,
                'variants' => $product->variants->map(function($variant) {
                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'ram' => $variant->ram,
                        'stock' => $variant->stock,
                        'price' => $variant->price,
                        'image' => $variant->image ? asset('storage/' . $variant->image) : 'https://placehold.co/80x80/e2e8f0/64748b?text=Variant'
                    ];
                })->toArray()
            ];
        });

        return response()->json($products);
    }

    /**
     * Method untuk halaman produk baru yang di-approve
     */
    public function newApprovedProducts()
    {
        $admin = Auth::user();
        $cabangId = $admin->cabang_id;

        $recentApprovedRequests = StockRequest::where('cabang_id', $cabangId)
            ->where('status', 'approved')
            ->where('updated_at', '>=', now()->subDay())
            ->with(['productVariant.product'])
            ->get();

        $newProducts = [];
        foreach ($recentApprovedRequests as $request) {
            if ($request->productVariant && $request->productVariant->product) {
                $product = $request->productVariant->product;
                $variant = $request->productVariant;
                
                if (!isset($newProducts[$product->id])) {
                    $newProducts[$product->id] = [
                        'product' => $product,
                        'variants' => [],
                        'total_added' => 0
                    ];
                }
                
                $newProducts[$product->id]['variants'][] = [
                    'variant' => $variant,
                    'added_stock' => $request->quantity,
                    'approved_at' => $request->updated_at
                ];
                $newProducts[$product->id]['total_added'] += $request->quantity;
            }
        }

        return view('admin.products.new-approved', compact('newProducts'));
    }

    /**
     * API untuk mendapatkan produk baru
     */
    public function getNewProducts()
    {
        $admin = Auth::user();
        $cabangId = $admin->cabang_id;

        $newProducts = ProductVariant::where('cabang_id', $cabangId)
            ->where('status', 'published')
            ->where('created_at', '>=', now()->subDays(7))
            ->with(['product'])
            ->get()
            ->groupBy('product_id');

        return response()->json($newProducts);
    }

    /**
     * Mengambil data produk spesifik untuk modal edit.
     */
    public function edit(Product $product)
    {
        $cabangId = Auth::user()->cabang_id;
        
        $product->load(['variants' => function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'published');
        }]);

        if ($product->variants->isEmpty()) {
            abort(404, 'Produk tidak ditemukan di cabang Anda.');
        }

        $decodedImages = json_decode($product->image, true);
        $imageUrls = [];
        if (is_array($decodedImages)) {
            foreach ($decodedImages as $path) {
                $imageUrls[] = asset('storage/' . $path);
            }
        } elseif (!empty($product->image)) {
            $imageUrls[] = asset('storage/' . $product->image);
        }

        $formattedProduct = [
            'id' => $product->id,
            'name' => $product->name,
            'category' => $product->category,
            'status' => $product->status,
            'description' => $product->description,
            'master_price' => $product->master_price,
            'master_images' => $imageUrls,
            'variants' => $product->variants->map(function($variant) {
                return [
                    'id' => $variant->id,
                    'color' => $variant->color,
                    'ram' => $variant->ram,
                    'price' => $variant->price,
                    'stock' => $variant->stock,
                    'image' => $variant->image ? asset('storage/' . $variant->image) : null
                ];
            })
        ];

        return response()->json($formattedProduct);
    }

    /**
     * Mengupdate data produk dan variannya.
     */
    public function update(Request $request, Product $product)
    {
        $cabangId = Auth::user()->cabang_id;

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'category' => 'required|string',
            'description' => 'nullable|string',
            'master_price' => 'required|numeric|min:0',
            'master_images' => 'nullable|array',
            'master_images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
            'variants' => 'required|array|min:1',
            'variants.*.id' => 'sometimes|integer',
            'variants.*.price' => 'required|numeric|min:0',
            'variants.*.image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $productData = $request->only(['name', 'category', 'description', 'master_price']);
            
            $currentImagePaths = [];
            if ($request->has('existing_images_json')) {
                $existingImageUrls = json_decode($request->input('existing_images_json'), true) ?? [];
                $baseStorageUrl = asset('storage') . '/';
                foreach ($existingImageUrls as $url) {
                    if (strpos($url, $baseStorageUrl) === 0) {
                        $currentImagePaths[] = substr($url, strlen($baseStorageUrl));
                    }
                }
            }
            $oldImagePaths = json_decode($product->image, true) ?? [];
            if (is_array($oldImagePaths)) {
                $imagesToDelete = array_diff($oldImagePaths, $currentImagePaths);
                if (!empty($imagesToDelete)) {
                    Storage::disk('public')->delete($imagesToDelete);
                }
            }
            
            if ($request->hasFile('master_images')) {
                foreach ($request->file('master_images') as $file) {
                    $path = $file->store('products', 'public');
                    $currentImagePaths[] = $path;
                }
            }
            $productData['image'] = json_encode(array_values($currentImagePaths));
            
            $product->update($productData);

            foreach ($request->variants as $index => $variantData) {
                if (isset($variantData['id']) && $variantData['id'] > 0) {
                    $variant = ProductVariant::find($variantData['id']);
                    if ($variant && $variant->cabang_id === $cabangId) {
                        $updateData = ['price' => $variantData['price']];
                        if ($request->hasFile("variants.{$index}.image")) {
                            if ($variant->image) {
                                Storage::disk('public')->delete($variant->image);
                            }
                            $updateData['image'] = $request->file("variants.{$index}.image")->store('product_variants', 'public');
                        }
                        $variant->update($updateData);
                    }
                }
            }

            DB::commit();
            return response()->json(['success' => 'Produk berhasil diperbarui!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Product Update Error:', ['error' => $e->getMessage()]);
            return response()->json(['error' => 'Gagal memperbarui produk: ' . $e->getMessage()], 500);
        }
    }
    
    /**
     * [DIUBAH] Menambahkan validasi kelengkapan data sebelum mengubah status.
     */
    public function toggleDisplayStatus(Product $product)
    {
        try {
            $cabangId = Auth::user()->cabang_id;
            if (!$product->variants()->where('cabang_id', $cabangId)->exists()) {
                return response()->json(['error' => 'Akses ditolak.'], 403);
            }

            // Validasi hanya saat akan mengubah status menjadi 'live'
            if ($product->display_status === 'draft') {
                $imagePaths = json_decode($product->image, true);
                $hasImage = !empty($imagePaths) && $imagePaths[0] !== null;
    
                if (!$product->master_price || $product->master_price <= 0) {
                    return response()->json(['error' => 'Tidak dapat menampilkan produk. Harap atur Harga Jual Utama terlebih dahulu.'], 422);
                }
                if (empty($product->description) || $product->description === 'Deskripsi akan dilengkapi oleh admin') {
                    return response()->json(['error' => 'Tidak dapat menampilkan produk. Harap lengkapi Deskripsi terlebih dahulu.'], 422);
                }
                if (!$hasImage) {
                    return response()->json(['error' => 'Tidak dapat menampilkan produk. Harap unggah minimal satu foto produk.'], 422);
                }
            }

            $product->display_status = ($product->display_status === 'draft') ? 'live' : 'draft';
            $product->save();

            $message = $product->display_status === 'live' ? 'Produk berhasil ditampilkan di toko.' : 'Produk berhasil disembunyikan dari toko.';
            
            return response()->json(['success' => $message, 'new_status' => $product->display_status]);

        } catch (\Exception $e) {
            Log::error('Error toggling display status: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal mengubah status tampil produk.'], 500);
        }
    }

    /**
     * Menghapus produk.
     */
    public function destroy(Product $product)
    {
        $cabangId = Auth::user()->cabang_id;
        $variantsToDelete = $product->variants()->where('cabang_id', $cabangId)->where('status', 'published')->get();
        if($variantsToDelete->isEmpty()){
             return response()->json(['error' => 'Produk tidak ditemukan di cabang ini.'], 404);
        }

        DB::beginTransaction();
        try {
            $imagePaths = json_decode($product->image, true);
            if (is_array($imagePaths) && !empty($imagePaths)) {
                Storage::disk('public')->delete($imagePaths);
            }

            foreach ($variantsToDelete as $variant) {
                if ($variant->image) {
                    Storage::disk('public')->delete($variant->image);
                }
                $variant->delete();
            }
            
            if ($product->variants()->count() === 0) {
                $product->delete();
            }

            DB::commit();
            return response()->json(['success' => 'Produk dan semua variannya telah dihapus dari cabang Anda.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => 'Gagal menghapus produk: ' . $e->getMessage()], 500);
        }
    }

    /**
     * API untuk mendapatkan summary produk
     */
    public function getSummary()
    {
        $admin = Auth::user();
        $cabangId = $admin->cabang_id;
        $variantsInBranch = ProductVariant::where('cabang_id', $cabangId)->where('status', 'published')->get();
        $summary = [
            'totalProduk' => Product::whereHas('variants', function ($query) use ($cabangId) {
                $query->where('cabang_id', $cabangId)->where('status', 'published');
            })->count(),
            'totalVarian' => $variantsInBranch->count(),
            'stokMenipis' => $variantsInBranch->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'stokHabis' => $variantsInBranch->where('stock', 0)->count(),
        ];
        return response()->json($summary);
    }
}
