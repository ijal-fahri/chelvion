<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Checkout;
use Carbon\Carbon;
use Yajra\DataTables\DataTables; // <--- PASTIKAN BARIS INI ADA

class AdminOrderController extends Controller
{
    /**
     * Menampilkan halaman Riwayat Orderan beserta statistik.
     */
    public function index()
    {
        // Query dasar untuk orderan yang sudah selesai (dihitung sebagai revenue)
        $completedOrders = Checkout::whereIn('status', ['selesai', 'sudah diterima']);

        // Menghitung total pendapatan untuk kartu statistik
        $totalRevenue = (clone $completedOrders)->sum('total_price');
        $totalHarian = (clone $completedOrders)->whereDate('created_at', Carbon::today())->sum('total_price');
        $totalMingguan = (clone $completedOrders)->whereBetween('created_at', [Carbon::now()->startOfWeek(), Carbon::now()->endOfWeek()])->sum('total_price');
        $totalBulanan = (clone $completedOrders)->whereMonth('created_at', Carbon::now()->month)->sum('total_price');
        $totalTahunan = (clone $completedOrders)->whereYear('created_at', Carbon::now()->year)->sum('total_price');

        // Mengirim hanya data statistik ke view, karena tabel akan diisi via AJAX
        return view('admin.orders.index', compact(
            'totalRevenue', 'totalHarian', 'totalMingguan', 'totalBulanan', 'totalTahunan'
        ));
    }

    /**
     * [FUNGSI BARU] Menyediakan data untuk DataTables di halaman Riwayat Orderan.
     */
    public function getData(Request $request)
    {
        $orders = Checkout::with('items.product')->whereIn('status', ['selesai', 'sudah diterima'])->latest();

        return DataTables::of($orders)
            ->editColumn('id', function($row){
                return '#' . $row->id;
            })
            ->addColumn('pelanggan', function($row){
                return e($row->receiver_name) . '<br><small class="text-gray-500">' . e($row->phone_number) . '</small>';
            })
            ->editColumn('total_price', function($row){
                return 'Rp' . number_format($row->total_price, 0, ',', '.');
            })
            ->addColumn('produk', function($row){
                $html = '<div class="flex flex-col gap-3">';
                foreach ($row->items as $item) {
                    $imageUrl = asset('images/' . $item->product->image);
                    $html .= '
                    <div class="flex items-center gap-3">
                        <img src="'.$imageUrl.'" class="w-14 h-14 object-cover rounded-lg flex-shrink-0">
                        <div>
                            <div class="font-semibold text-gray-800">'.e($item->product->name).'</div>
                            <div class="text-xs text-gray-500">
                                <span>'.e($item->color).' / '.e($item->ram).'GB</span> | <span>Qty: '.e($item->quantity).'</span>
                            </div>
                        </div>
                    </div>';
                }
                $html .= '</div>';
                return $html;
            })
            ->editColumn('status', function($row){
                $status = e(ucfirst($row->status));
                return '<span class="px-3 py-1.5 text-xs font-semibold rounded-full bg-green-100 text-green-800">'.$status.'</span>';
            })
            ->editColumn('created_at', function ($row) {
                // Menggunakan Carbon untuk format tanggal yang lebih baik
                return Carbon::parse($row->created_at)->translatedFormat('d F Y');
            })
            ->rawColumns(['pelanggan', 'produk', 'status'])
            ->make(true);
    }
}