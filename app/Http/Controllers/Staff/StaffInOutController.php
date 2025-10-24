<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\StockRequest;
use App\Models\TradeIn;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class StaffInOutController extends Controller
{
    /**
     * Menampilkan halaman utama monitoring logistik.
     */
    public function index()
    {
        return view('staff.inout.index');
    }

    /**
     * [API] Mengambil semua data logistik (masuk, keluar, permintaan) dan ringkasan.
     */
    public function getData()
    {
        try {
            $cabangId = Auth::user()->cabang_id;
            $logData = collect();

            // 1. Ambil data "Keluar" (Permintaan yang disetujui) dan "Permintaan" (yang masih pending)
            $stockRequests = StockRequest::where('cabang_id', $cabangId)
                ->with(['productVariant.product', 'admin'])
                ->get();

            foreach ($stockRequests as $request) {
                $type = 'permintaan';
                if ($request->status === 'approved') {
                    $type = 'keluar';
                } elseif ($request->status === 'rejected') {
                    $type = 'ditolak'; // Tipe internal agar bisa disaring
                }

                $logData->push([
                    'id' => 'req-' . $request->id,
                    'type' => $type,
                    'date' => $request->updated_at,
                    'product_name' => $request->productVariant->product->name ?? 'Produk Dihapus',
                    'variant_name' => $request->productVariant ? ($request->productVariant->color . ($request->productVariant->ram ? ' / ' . $request->productVariant->ram : '')) : 'Varian Dihapus',
                    'category' => $request->productVariant->product->category ?? 'N/A',
                    'quantity' => $request->quantity,
                    'notes' => $request->notes ?: 'Permintaan dari ' . ($request->admin->name ?? 'Admin'),
                    'related_person' => $request->admin->name ?? 'Admin',
                ]);
            }

            // 2. Ambil data "Masuk" dari Tukar Tambah yang sudah selesai QC
            $tradeIns = TradeIn::where('cabang_id', $cabangId)
                ->where('status', 'selesai')
                ->get();
            
            foreach ($tradeIns as $trade) {
                $logData->push([
                    'id' => 'ti-' . $trade->id,
                    'type' => 'masuk',
                    'date' => $trade->updated_at,
                    'product_name' => $trade->product_name,
                    'variant_name' => $trade->specs,
                    'category' => 'Handphone',
                    'quantity' => 1,
                    'notes' => 'Hasil Tukar Tambah',
                    'related_person' => 'Kasir (ID: ' . $trade->kasir_id . ')',
                ]);
            }

            // [PERBAIKAN] Urutkan data dengan 'permintaan' (pending) di paling atas
            $sortedLogs = $logData->sortBy(function ($log) {
                // Memberi prioritas pada 'permintaan'
                return $log['type'] === 'permintaan' ? 0 : 1;
            })->sortByDesc('date')->values()->all();

            // 4. Hitung data untuk kartu ringkasan
            $today = now()->startOfDay();
            $summary = [
                'masuk_hari_ini' => $logData->where('type', 'masuk')->where('date', '>=', $today)->sum('quantity'),
                'keluar_hari_ini' => $logData->where('type', 'keluar')->where('date', '>=', $today)->sum('quantity'),
                'stok_kritis' => ProductVariant::where('cabang_id', $cabangId)->where('status', 'draft')->where('stock', '>', 0)->where('stock', '<=', 5)->count(),
                'perlu_diproses' => $stockRequests->where('status', 'pending')->count(),
            ];

            return response()->json([
                'logs' => $sortedLogs,
                'summary' => $summary,
            ]);

        } catch (\Exception $e) {
            Log::error('Error getting in-out data: ' . $e->getMessage());
            return response()->json(['error' => 'Gagal memuat data logistik.'], 500);
        }
    }
}

