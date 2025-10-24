<?php

namespace App\Http\Controllers\Owner;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Cabang;
use App\Models\Checkout;
use App\Models\CheckoutItem;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OwnerDataCabangController extends Controller
{
    public function index()
    {
        $cabangs = Cabang::all();

        // --- STATISTIK GABUNGAN (SEMUA CABANG) DARI TABEL CHECKOUTS ---
        $totalPendapatan = Checkout::sum('total_price');
        $jumlahTransaksi = Checkout::count();
        $orderSelesai = Checkout::where('status', 'Completed')->count();
        // [UPDATE] Menghitung total cabang, bukan pelanggan baru
        $totalCabang = $cabangs->count();

        // --- TREN PENDAPATAN DARI TABEL CHECKOUTS ---
        $revenueTrend = Checkout::select(
            DB::raw("DATE_FORMAT(created_at, '%b') as month"),
            DB::raw("SUM(total_price) as total")
        )
        ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
        ->groupBy('month')
        ->orderByRaw('MIN(created_at)')
        ->get();

        // --- PRODUK TERLARIS DARI TABEL CHECKOUT_ITEMS ---
        $topProducts = CheckoutItem::select('product_name', DB::raw('SUM(quantity) as total_sold'))
            ->groupBy('product_name')
            ->orderBy('total_sold', 'desc')
            ->limit(4)
            ->get();

        // --- KERANGKA DATA UNTUK JAVASCRIPT ---
        $reportData = [
            "all" => [
                'metrics' => [
                    'totalPendapatan' => $totalPendapatan, 'totalPendapatanSub' => "Gabungan semua cabang",
                    'jumlahTransaksi' => $jumlahTransaksi, 'jumlahTransaksiSub' => "Total periode ini",
                    'orderSelesai' => $orderSelesai, 'orderSelesaiSub' => number_format(($orderSelesai / ($jumlahTransaksi > 0 ? $jumlahTransaksi : 1)) * 100, 1) . "% Tingkat konversi",
                    // [UPDATE] Mengganti data pelanggan baru dengan total cabang
                    'totalCabang' => $totalCabang, 'totalCabangSub' => "Jumlah Toko Terdaftar"
                ],
                'trendPendapatan' => [
                    'labels' => $revenueTrend->pluck('month'),
                    'values' => $revenueTrend->pluck('total')->map(fn($val) => round($val / 1000000))
                ],
                'produkTerlaris' => [
                    'labels' => $topProducts->pluck('product_name'),
                    'values' => $topProducts->pluck('total_sold')
                ]
            ]
        ];

        // --- LOOPING UNTUK MENGHITUNG STATISTIK PER CABANG ---
        foreach ($cabangs as $cabang) {
            $branchCheckouts = Checkout::where('cabang_id', $cabang->id);
            
            $branchPendapatan = (clone $branchCheckouts)->sum('total_price');
            $branchTransaksi = (clone $branchCheckouts)->count();
            $branchSelesai = (clone $branchCheckouts)->where('status', 'Completed')->count();
            
            $branchRevenueTrend = (clone $branchCheckouts)
                ->select(DB::raw("DATE_FORMAT(created_at, '%b') as month"), DB::raw("SUM(total_price) as total"))
                ->where('created_at', '>=', now()->subMonths(5)->startOfMonth())
                ->groupBy('month')->orderByRaw('MIN(created_at)')->get();
            
            $branchTopProducts = CheckoutItem::join('checkouts', 'checkout_items.checkout_id', '=', 'checkouts.id')
                ->where('checkouts.cabang_id', $cabang->id)
                ->select('product_name', DB::raw('SUM(quantity) as total_sold'))
                ->groupBy('product_name')
                ->orderBy('total_sold', 'desc')
                ->limit(4)
                ->get();

            $reportData[$cabang->id] = [
                'info' => [
                    'nama' => $cabang->nama_cabang, 
                    'alamat' => $cabang->alamat, // Alamat sekarang adalah link
                    'telepon' => $cabang->whatsapp, 
                    'whatsapp' => preg_replace('/^0/', '62', $cabang->whatsapp),
                    // [UPDATE] gmaps sekarang langsung mengambil dari kolom alamat
                    'gmaps' => $cabang->alamat 
                ],
                'metrics' => [
                    'totalPendapatan' => $branchPendapatan, 'totalPendapatanSub' => "Pendapatan cabang ini",
                    'jumlahTransaksi' => $branchTransaksi, 'jumlahTransaksiSub' => "Total transaksi cabang",
                    'orderSelesai' => $branchSelesai, 'orderSelesaiSub' => number_format(($branchSelesai / ($branchTransaksi > 0 ? $branchTransaksi : 1)) * 100, 1) . "% Tingkat konversi",
                    // 'pelangganBaru' sengaja dihapus untuk data per cabang
                ],
                'trendPendapatan' => [
                    'labels' => $branchRevenueTrend->pluck('month'),
                    'values' => $branchRevenueTrend->pluck('total')->map(fn($val) => round($val / 1000000))
                ],
                'produkTerlaris' => [
                    'labels' => $branchTopProducts->pluck('product_name'),
                    'values' => $branchTopProducts->pluck('total_sold')
                ]
            ];
        }

        return view('owner.datacabang.index', [
            'reportData' => $reportData,
            'semuaCabang' => $cabangs
        ]);
    }

    public function store(Request $request){
        $validator = Validator::make($request->all(), [
            'nama_cabang' => 'required|string|max:255',
            // [UPDATE] Validasi untuk alamat sekarang adalah URL
            'alamat' => 'required|url',
            'whatsapp' => 'required|string|max:20|regex:/^08[0-9]{8,12}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cabang = Cabang::create($request->all());

        return response()->json(['success' => 'Cabang baru berhasil ditambahkan!', 'cabang' => $cabang]);
    }

    public function update(Request $request, $id){
        $validator = Validator::make($request->all(), [
            'nama_cabang' => 'required|string|max:255',
            // [UPDATE] Validasi untuk alamat sekarang adalah URL
            'alamat' => 'required|url',
            'whatsapp' => 'required|string|max:20|regex:/^08[0-9]{8,12}$/',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $cabang = Cabang::find($id);
        if (!$cabang) {
            return response()->json(['error' => 'Data cabang tidak ditemukan.'], 404);
        }

        $cabang->update($request->all());

        return response()->json(['success' => 'Data cabang berhasil diperbarui!', 'cabang' => $cabang]);
    }
    
    public function destroy($id){
        $cabang = Cabang::find($id);
        if (!$cabang) {
            return response()->json(['error' => 'Data cabang tidak ditemukan.'], 404);
        }
        
        $cabang->delete();

        return response()->json(['success' => 'Data cabang berhasil dihapus!']);
    }
}
