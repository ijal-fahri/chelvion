<?php

namespace App\Http\Controllers\Kasir;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\TradeIn;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class KasirTradeController extends Controller
{
    /**
     * Menampilkan halaman utama Tukar Tambah untuk Kasir.
     */
    public function index()
    {
        // Data 'brands' dan 'qcItems' dikirim ke view agar dinamis
        $brands = ['Apple', 'Samsung', 'Xiaomi', 'Oppo', 'Vivo', 'Realme', 'Infinix'];
        $qcItems = [ 'Fisik', 'Layar', 'Baterai', 'Pengisian Daya', 'Kamera Depan', 'Kamera Belakang', 'Speaker', 'Mikrofon', 'Konektivitas', 'Sensor', 'Tombol' ];

        return view('kasir.kualitas.index', compact('brands', 'qcItems'));
    }

    /**
     * [API] Mengambil data produk 'published' yang tersedia untuk dibeli.
     */
    public function getAvailableProducts()
    {
        $cabangId = Auth::user()->cabang_id;
        $products = Product::where('category', 'Handphone') // <--- TAMBAHKAN BARIS INI
            ->whereHas('variants', function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'published')->where('stock', '>', 0);
        })
        ->with(['variants' => function ($query) use ($cabangId) {
            $query->where('cabang_id', $cabangId)->where('status', 'published')->where('stock', '>', 0);
        }])
        ->get()
        ->map(function ($product) {
            // Mengambil harga terendah dari varian yang tersedia
            $price = $product->variants->min('price');
            // Menghitung total stok dari semua varian
            $totalStock = $product->variants->sum('stock');
            // Mengambil gambar utama produk
            $imagePaths = json_decode($product->image, true);
            $imageUrl = (is_array($imagePaths) && !empty($imagePaths)) ? asset('storage/' . $imagePaths[0]) : 'https://placehold.co/80x80/eef2ff/4f46e5?text=N/A';
            
            return [
                'id' => $product->id,
                'name' => $product->name,
                'image' => $imageUrl,
                'price' => $price,
                'description' => 'Stok: ' . $totalStock,
                'variants' => [
                    'storage' => $product->variants->pluck('ram')->unique()->values(),
                    'colors' => $product->variants->pluck('color')->unique()->values(),
                ]
            ];
        });

        return response()->json(['data' => $products]);
    }

    /**
     * [API] Mengambil data untuk chart transaksi.
     */
    public function getChartData()
    {
        $cabangId = Auth::user()->cabang_id;
        $sevenDaysAgo = now()->subDays(6)->startOfDay();

        $transactionCounts = TradeIn::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('cabang_id', $cabangId)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()->pluck('count', 'date');

        $transactionValues = TradeIn::select(DB::raw('DATE(created_at) as date'), DB::raw('sum(cost_price) as total_value'))
            ->where('cabang_id', $cabangId)
            ->where('created_at', '>=', $sevenDaysAgo)
            ->groupBy('date')
            ->orderBy('date', 'asc')
            ->get()->pluck('total_value', 'date');

        $labels = [];
        $countData = [];
        $valueData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = now()->subDays($i);
            $dateString = $date->format('Y-m-d');
            $labels[] = $i === 0 ? 'Hari Ini' : $date->isoFormat('dddd');
            
            $countData[] = $transactionCounts[$dateString] ?? 0;
            $valueData[] = $transactionValues[$dateString] ?? 0;
        }

        return response()->json([
            'labels' => $labels,
            'count_data' => $countData,
            'value_data' => $valueData,
        ]);
    }

    /**
     * [API] Menyimpan data transaksi tukar tambah baru.
     */
    public function store(Request $request)
    {
        // [DIUBAH] Tambah validasi untuk produk baru
        $validator = Validator::make($request->all(), [
            // Validasi HP Lama
            'product_name' => 'required|string|max:255',
            'specs' => 'required|string|max:255',
            'cost_price' => 'required|integer|min:0',
            'completeness' => 'required|string|max:255',
            'condition' => 'required|string|max:255',
            'qc_details' => 'required|json',
            // Validasi HP Baru
            'new_product_id' => 'required|exists:products,id',
            'new_product_storage' => 'required|string',
            'new_product_color' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            $kasir = Auth::user();
            if (!$kasir->cabang_id) {
                throw new \Exception("Akun kasir tidak terhubung dengan cabang manapun.");
            }

            // [BARU] Cari varian produk baru yang akan dibeli
            $newProductVariant = ProductVariant::where('product_id', $request->new_product_id)
                ->where('ram', $request->new_product_storage)
                ->where('color', $request->new_product_color)
                ->where('cabang_id', $kasir->cabang_id)
                ->where('status', 'published') // <-- TAMBAHKAN BARIS INI
                ->first();

            // [BARU] Cek apakah varian ditemukan dan stoknya ada
            if (!$newProductVariant) {
                throw new \Exception("Varian produk baru yang dipilih tidak ditemukan.");
            }
            if ($newProductVariant->stock < 1) {
                throw new \Exception("Stok untuk varian produk baru yang dipilih sudah habis.");
            }

            // [BARU] Kurangi stok produk baru
            $newProductVariant->decrement('stock');

            // [DIUBAH] Simpan transaksi TradeIn dengan ID produk baru
            TradeIn::create([
                'cabang_id' => $kasir->cabang_id,
                'new_product_variant_id' => $newProductVariant->id, // <- Simpan ID varian baru
                'product_name' => $request->product_name,
                'specs' => $request->specs,
                'cost_price' => $request->cost_price,
                'completeness' => $request->completeness,
                'condition' => $request->condition,
                'qc_details' => json_decode($request->qc_details, true),
                'status' => 'perlu_qc',
            ]);

            DB::commit();
            return response()->json(['success' => 'Transaksi tukar tambah berhasil! Stok produk baru telah diperbarui.'], 201);
            
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing trade-in transaction: ' . $e->getMessage(), [
                'file' => $e->getFile(), 'line' => $e->getLine(), 'request' => $request->all()
            ]);
            // [DIUBAH] Kirim pesan error yang lebih spesifik ke frontend
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}