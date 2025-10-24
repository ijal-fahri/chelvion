<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockRequest;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class StaffRequestController extends Controller
{
    /**
     * Menampilkan halaman permintaan masuk untuk Staf Gudang.
     */
    public function index()
    {
        $staff = Auth::user();
        $cabangId = $staff->cabang_id;

        // [DIUBAH] Mengurutkan berdasarkan status 'pending' terlebih dahulu, lalu tanggal terbaru.
        $requests = StockRequest::where('cabang_id', $cabangId)
            ->with(['admin', 'productVariant.product'])
            ->orderByRaw("CASE WHEN status = 'pending' THEN 0 ELSE 1 END ASC, created_at DESC")
            ->get();

        $summary = [
            'total' => $requests->count(),
            'pending' => $requests->where('status', 'pending')->count(),
            'approved' => $requests->where('status', 'approved')->count(),
            'rejected' => $requests->where('status', 'rejected')->count(),
        ];
        
        $trend = StockRequest::select(DB::raw('DATE(created_at) as date'), DB::raw('count(*) as count'))
            ->where('cabang_id', $cabangId)
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

        return view('staff.request.index', compact('requests', 'summary', 'trendData'));
    }
    
    /**
     * Mengambil detail satu permintaan untuk modal.
     */
    public function show(StockRequest $stockRequest)
    {
        if ($stockRequest->cabang_id !== Auth::user()->cabang_id) {
            abort(403, 'Akses ditolak.');
        }

        $stockRequest->load(['admin', 'productVariant.product']);

        // [BARU] Menambahkan URL gambar yang sudah diproses untuk modal detail
        if ($stockRequest->productVariant) {
            $imageUrl = 'https://placehold.co/80x80/eef2ff/4f46e5?text=N/A'; // Default
            if ($stockRequest->productVariant->image) {
                $imageUrl = asset('storage/' . $stockRequest->productVariant->image);
            } elseif ($stockRequest->productVariant->product && $stockRequest->productVariant->product->image) {
                $productImages = json_decode($stockRequest->productVariant->product->image, true);
                if (is_array($productImages) && !empty($productImages[0])) {
                    $imageUrl = asset('storage/' . $productImages[0]);
                }
            }
            $stockRequest->productVariant->display_image_url = $imageUrl;
        }

        return response()->json($stockRequest);
    }

    /**
     * Memproses aksi (approve/reject) dari Staf Gudang.
     */
    public function update(Request $request, StockRequest $stockRequest)
    {
        $staff = Auth::user();
        $request->validate([
            'status' => 'required|in:approved,rejected',
            'staff_notes' => 'nullable|string|max:500',
        ]);
        
        if ($stockRequest->cabang_id !== $staff->cabang_id) {
            return response()->json(['error' => 'Akses ditolak.'], 403);
        }

        if ($stockRequest->status !== 'pending') {
            return response()->json(['error' => 'Permintaan ini sudah diproses.'], 422);
        }
        
        DB::beginTransaction();
        try {
            if ($request->status === 'approved') {
                $variant = $stockRequest->productVariant;

                if ($variant->stock < $stockRequest->quantity) {
                    throw new \Exception('Stok draft tidak mencukupi untuk disetujui (sisa: '.$variant->stock.').');
                }

                $variant->decrement('stock', $stockRequest->quantity);

                $publishedVariant = ProductVariant::firstOrNew([
                    'product_id' => $variant->product_id,
                    'cabang_id' => $variant->cabang_id,
                    'color' => $variant->color,
                    'ram' => $variant->ram,
                    'status' => 'published',
                ]);

                if (!$publishedVariant->exists) {
                    $publishedVariant->price = $variant->price; 
                    $publishedVariant->image = $variant->image; 
                }

                $publishedVariant->stock = ($publishedVariant->exists ? $publishedVariant->stock : 0) + $stockRequest->quantity;
                $publishedVariant->save();
            }

            $stockRequest->update([
                'status' => $request->status,
                'staff_id' => $staff->id,
                'staff_notes' => $request->staff_notes,
            ]);

            DB::commit();
            return response()->json(['success' => 'Permintaan berhasil di-' . $request->status . '.']);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
