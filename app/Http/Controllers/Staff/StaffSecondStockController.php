<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TradeIn;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class StaffSecondStockController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen stok second.
     */
    public function index()
    {
        return view('staff.second.index');
    }

    /**
     * [API] Mengambil data trade-in yang perlu di-QC untuk DataTables.
     */
    public function getData()
    {
        $tradeIns = TradeIn::where('cabang_id', Auth::user()->cabang_id)
            ->where('status', 'perlu_qc') // Hanya tampilkan yang perlu diproses
            ->latest()
            ->get();
            
        // Menambahkan placeholder image untuk konsistensi data
        $tradeIns->transform(function ($item) {
            $item->image = 'https://placehold.co/80x80/f1f5f9/475569?text=QC';
            return $item;
        });

        return response()->json(['data' => $tradeIns]);
    }

    /**
     * [API] Mengambil data summary untuk kartu di bagian atas halaman.
     */
    public function getSummary()
    {
        $cabangId = Auth::user()->cabang_id;
        
        $stockIn = TradeIn::where('cabang_id', $cabangId)->where('status', 'perlu_qc')->get();
        $processed = TradeIn::where('cabang_id', $cabangId)->where('status', 'selesai')->count();
        $stockInValue = $stockIn->sum('cost_price');

        return response()->json([
            'stock_in_count' => $stockIn->count(),
            'processed_count' => $processed,
            'stock_in_value' => $stockInValue,
        ]);
    }

    /**
     * [API] Memproses produk second dan membuatnya menjadi stok 'draft' untuk diminta Admin.
     * Ini dieksekusi saat staf menekan "Kirim & Selesaikan".
     */
    public function submitToAdmin(Request $request, TradeIn $tradeIn)
    {
        // Validasi keamanan: pastikan trade-in ini milik cabang yang benar dan belum diproses.
        if ($tradeIn->cabang_id !== Auth::user()->cabang_id || $tradeIn->status !== 'perlu_qc') {
            return response()->json(['error' => 'Data tidak valid atau sudah diproses.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'staff_notes' => 'nullable|string|max:500',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        DB::beginTransaction();
        try {
            // 1. Tentukan nama produk dan pisahkan RAM/Warna dari specs
            $productName = $tradeIn->product_name;
            $specs = explode('|', $tradeIn->specs);
            $ram = trim($specs[0]);
            $color = trim($specs[1] ?? 'N/A');

            // 2. Cari atau buat produk master dengan status 'Second'
            $product = Product::firstOrCreate(
                ['name' => $productName],
                [
                    'category' => 'Handphone', // Asumsi dari trade-in adalah Handphone
                    'status' => 'Second',      // INI KUNCINYA
                    'description' => 'Deskripsi akan dilengkapi oleh admin.'
                ]
            );

            // 3. Buat varian baru dengan status 'draft'
            // Varian untuk produk second selalu unik (stok=1), jadi kita tidak perlu cek duplikat.
            ProductVariant::create([
                'product_id' => $product->id,
                'cabang_id' => $tradeIn->cabang_id,
                'color' => $color,
                'ram' => $ram,
                'stock' => 1, // Stok untuk produk second selalu 1
                'status' => 'draft', // Dibuat sebagai draft agar bisa diminta Admin
                'price' => $tradeIn->cost_price, // Harga awal bisa diisi harga beli
            ]);

            // 4. Update status trade-in menjadi 'selesai' dan simpan catatan
            $tradeIn->update([
                'status' => 'selesai',
                'staff_notes' => $request->staff_notes
            ]);

            DB::commit();
            return response()->json(['success' => 'Produk second berhasil dibuat sebagai stok draft dan siap diminta oleh Admin.']);

        } catch (\Exception $e) {
            DB::rollBack();
            \Log::error('Error submitting second-hand stock: ' . $e->getMessage());
            return response()->json(['error' => 'Terjadi kesalahan internal saat memproses data.'], 500);
        }
    }
}

