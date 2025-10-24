<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\OfflineOrder;
use App\Models\OfflineOrderItem;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Str; // <-- [PERUBAHAN 1] Tambahkan ini

class KasirTransaksiController extends Controller
{
    public function index()
    {
        $cabangId = Auth::user()->cabang_id;

            $products = Product::where('display_status', 'live') // <-- TAMBAHKAN BARIS INI
            ->whereHas('variants', function ($query) use ($cabangId) {
                $query->where('cabang_id', $cabangId)
                    ->where('status', 'published')
                    ->where('stock', '>', 0);
        })
        ->with(['variants' => function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)
                ->where('status', 'published')
                ->where('stock', '>', 0);
        }])
        ->get()
        ->map(function ($product) {
            if ($product->variants->isEmpty()) {
                return null;
            }

            // Process product images
            $imagePath = $product->image;
            $imageUrls = ['https://placehold.co/400x400/eef2ff/4f46e5?text=N/A'];
            if ($imagePath) {
                $decodedImages = json_decode($imagePath, true);
                if (is_array($decodedImages) && !empty($decodedImages)) {
                    $imageUrls = array_map(function($img) {
                        // Cek jika gambar sudah berupa URL lengkap
                        if (filter_var($img, FILTER_VALIDATE_URL)) {
                            return $img;
                        }
                        // Cek jika gambar sudah memiliki path storage
                        if (strpos($img, 'storage/') === 0) {
                            return asset($img);
                        }
                        return asset('storage/' . $img);
                    }, $decodedImages);
                }
            }

            return [
                'id' => $product->id,
                'name' => $product->name,
                'images' => $imageUrls,
                'master_price' => $product->master_price, // TAMBAHKAN INI
                'display_price' => $product->variants->min('price'),
                'total_stock' => $product->variants->sum('stock'),
                'category' => $product->category,
                'condition' => $product->status,
                'variants' => $product->variants->map(function ($variant) {
                    // Process variant images
                    $variantImagePath = $variant->image;
                    $variantImageUrls = [];
                    
                    if ($variantImagePath) {
                        $decodedVariantImages = json_decode($variantImagePath, true);
                        if (is_array($decodedVariantImages) && !empty($decodedVariantImages)) {
                            $variantImageUrls = array_map(function($img) {
                                // Cek jika gambar sudah berupa URL lengkap
                                if (filter_var($img, FILTER_VALIDATE_URL)) {
                                    return $img;
                                }
                                // Cek jika gambar sudah memiliki path storage
                                if (strpos($img, 'storage/') === 0) {
                                    return asset($img);
                                }
                                return asset('storage/' . $img);
                            }, $decodedVariantImages);
                        } else {
                            // Jika bukan array, mungkin string langsung
                            $variantImageUrls = [asset('storage/' . $variantImagePath)];
                        }
                    }

                    return [
                        'id' => $variant->id,
                        'color' => $variant->color,
                        'ram' => $variant->ram,
                        'price' => $variant->price,
                        'stock' => $variant->stock,
                        'image_urls' => $variantImageUrls,
                        'primary_image_url' => !empty($variantImageUrls) ? $variantImageUrls[0] : null,
                        'has_images' => !empty($variantImageUrls),
                    ];
                })->values(),
            ];
        })
        ->whereNotNull();

        return view('kasir.transaksi.index', compact('products'));
    }

    public function store(Request $request)
    {
        // 1. Validasi data yang masuk
        $validator = Validator::make($request->all(), [
            'customer_name'  => 'required|string|max:255',
            'customer_phone' => 'required|string|max:20',
            'customer_email' => 'nullable|email|max:255',
            'payment_method' => 'required|string',
            'items'          => 'required|array|min:1',
            'items.*.variant.id' => 'required|exists:product_variants,id',
            'items.*.qty'        => 'required|integer|min:1',
        ]);

        if ($validator->fails()) {
            return response()->json(['message' => 'Data tidak valid.', 'errors' => $validator->errors()], 422);
        }

        $kasir = Auth::user();
        $items = $request->input('items');

        // 2. Gunakan DB Transaction untuk memastikan semua proses berhasil
        try {
            DB::beginTransaction();

            // 3. Buat order utama
            $order = OfflineOrder::create([
                // [PERUBAHAN 2] Ganti format invoice number agar sama dengan online order
                'invoice_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                'kasir_id'       => $kasir->id,
                'cabang_id'      => $kasir->cabang_id,
                'customer_name'  => $request->customer_name,
                'customer_phone' => $request->customer_phone,
                'customer_email' => $request->customer_email,
                'total_amount'   => $request->total,
                'payment_method' => $request->payment_method,
                'status'         => 'Selesai',
            ]);

            // 4. Loop setiap item di keranjang
            foreach ($items as $item) {
                $variant = ProductVariant::find($item['variant']['id']);
                
                // Cek ketersediaan stok
                if ($variant->stock < $item['qty']) {
                    throw ValidationException::withMessages([
                        'stock' => 'Stok untuk produk ' . $item['name'] . ' tidak mencukupi.'
                    ]);
                }

                // Buat item order
                OfflineOrderItem::create([
                    'offline_order_id'   => $order->id,
                    'product_variant_id' => $variant->id,
                    'product_name'       => $item['name'],
                    'variant_info'       => $variant->color . ' / ' . $variant->ram,
                    'quantity'           => $item['qty'],
                    'price'              => $item['price'],
                    'subtotal'           => $item['price'] * $item['qty'],
                ]);

                // Kurangi stok produk
                $variant->decrement('stock', $item['qty']);
            }

            // 5. Jika semua berhasil, commit transaksi
            DB::commit();

            return response()->json(['message' => 'Transaksi berhasil disimpan!'], 201);

        } catch (\Exception $e) {
            // 6. Jika ada error, rollback semua perubahan
            DB::rollBack();

            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }
}