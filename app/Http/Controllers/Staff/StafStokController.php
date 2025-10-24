<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class StafStokController extends Controller
{
    /**
     * Menampilkan halaman manajemen stok untuk Staf Gudang.
     */
    public function index()
    {
        $cabangId = Auth::user()->cabang_id;
        if (!$cabangId) {
            return redirect()->back()->with('error', 'Akun Anda tidak terhubung dengan cabang manapun.');
        }

        // Mengambil produk dengan relasi varian yang sudah dipisah (draft & published)
        $products = Product::whereHas('variants', function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId);
        })->with([
            'draftVariants' => function ($query) use ($cabangId) {
                $query->where('cabang_id', $cabangId);
            },
            'publishedVariants' => function ($query) use ($cabangId) {
                $query->where('cabang_id', $cabangId);
            }
        ])->orderBy('name')->get();

        // Menambahkan URL gambar utama ke produk untuk modal detail
        $products->transform(function ($product) {
            $imagePaths = json_decode($product->image, true);
            $imageUrl = (is_array($imagePaths) && !empty($imagePaths[0])) 
                ? asset('storage/' . $imagePaths[0]) 
                : null;
            $product->image_url = $imageUrl;
            return $product;
        });

        // Hitung statistik HANYA berdasarkan stok 'draft' yang dikelola staf
        $draftVariantsInBranch = ProductVariant::where('cabang_id', $cabangId)->where('status', 'draft')->get();
        $allVariantsInBranch = ProductVariant::where('cabang_id', $cabangId)->get();

        $summary = [
            'totalProduk' => $products->count(),
            'totalVarian' => $allVariantsInBranch->count(),
            'stokMenipis' => $draftVariantsInBranch->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
            'stokHabis' => $draftVariantsInBranch->where('stock', 0)->count(),
        ];

        // Produk untuk dropdown tambah stok
        $allProducts = Product::orderBy('name')->get();

        return view('staff.manage.index', compact('products', 'allProducts', 'summary'));
    }

    /**
     * Menyimpan data stok baru yang diinput oleh Staf Gudang.
     */
    public function store(Request $request)
    {
        $variants = $request->input('variants', []);
        $filteredVariants = array_filter($variants, function($variant) {
            return !empty($variant['color']) && isset($variant['stock']);
        });
        
        $request->merge(['variants' => $filteredVariants]);

        $validator = Validator::make($request->all(), [
            'product_type' => 'required|in:handphone,aksesori',
            'product_selection' => 'required|in:existing,new',
            'product_id' => 'required_if:product_selection,existing|nullable|exists:products,id',
            'new_product_name' => 'required_if:product_selection,new|nullable|string|max:255|unique:products,name',
            'new_product_category' => 'required_if:product_selection,new|nullable|in:Handphone,Aksesori',
            'variants' => 'required|array|min:1',
            'variants.*.color' => 'required|string|max:255',
            'variants.*.ram' => 'required_if:product_type,handphone|nullable|string|max:255',
            'variants.*.stock' => 'required|integer|min:1',
        ], [
            'variants.*.ram.required_if' => 'Field RAM/ROM wajib diisi untuk produk Handphone.',
            'variants.min' => 'Minimal harus ada satu varian yang diisi.',
            'variants.required' => 'Minimal harus ada satu varian yang diisi.',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cabangId = Auth::user()->cabang_id;

        DB::beginTransaction();
        try {
            $product = null;
            
            if ($request->product_selection === 'new') {
                $product = Product::create([
                    'name' => $request->new_product_name,
                    'category' => $request->new_product_category,
                    'status' => 'Baru', // Default status untuk produk baru
                    'display_status' => 'draft',
                    'description' => '', // [PERBAIKAN] Dikosongkan, tidak diisi teks default
                ]);
            } else {
                $product = Product::find($request->product_id);
            }

            if (!$product) {
                throw new \Exception("Produk tidak ditemukan.");
            }

            foreach ($request->variants as $variantData) {
                $ram = ($product->category === 'Handphone') ? ($variantData['ram'] ?? null) : null;

                $variant = ProductVariant::firstOrNew([
                    'product_id' => $product->id,
                    'cabang_id' => $cabangId,
                    'color' => $variantData['color'],
                    'ram' => $ram,
                    'status' => 'draft'
                ]);

                $variant->stock = ($variant->exists ? $variant->stock : 0) + $variantData['stock'];
                $variant->save();
            }

            DB::commit();
            return response()->json(['success' => 'Stok berhasil ditambahkan!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing stock: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal menyimpan stok: ' . $e->getMessage()], 500);
        }
    }

    /**
     * Mengambil data varian untuk modal edit.
     */
    public function edit(Product $product)
    {
        $cabangId = Auth::user()->cabang_id;
        $variants = $product->draftVariants()->where('cabang_id', $cabangId)->get();
        return response()->json(['variants' => $variants]);
    }

    /**
     * Mengupdate stok satu varian spesifik.
     */
    public function update(Request $request, ProductVariant $variant)
    {
        if ($variant->cabang_id !== Auth::user()->cabang_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        if ($variant->status !== 'draft') {
            return response()->json(['error' => 'Hanya stok draft yang bisa diubah.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'stock' => 'required|integer|min:0',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $variant->update(['stock' => $request->stock]);

        return response()->json(['success' => 'Stok varian berhasil diupdate.']);
    }

    /**
     * Menghapus semua varian dari satu produk di cabang tersebut.
     */
    public function destroy(Product $product)
    {
        $cabangId = Auth::user()->cabang_id;
        $deletedCount = $product->variants()->where('cabang_id', $cabangId)->delete();

        if ($deletedCount > 0) {
            return response()->json(['success' => 'Produk dan semua variannya telah dihapus dari cabang Anda.']);
        }
        return response()->json(['error' => 'Tidak ada varian untuk dihapus.'], 404);
    }

    /**
     * Menghapus satu varian spesifik.
     */
    public function destroyVariant(ProductVariant $variant)
    {
        if ($variant->cabang_id !== Auth::user()->cabang_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        $variant->delete();
        return response()->json(['success' => 'Varian berhasil dihapus.']);
    }
}

