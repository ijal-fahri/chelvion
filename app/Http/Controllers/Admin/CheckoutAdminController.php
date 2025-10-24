<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Checkout;
use Illuminate\Http\Request;
use Yajra\DataTables\DataTables;

class CheckoutAdminController extends Controller
{
    /**
     * Menampilkan halaman utama manajemen checkout.
     */
    public function index()
    {
        return view('admin.checkouts.index');
    }

    /**
     * Menyediakan data checkout untuk DataTables.
     */
    public function getData(Request $request)
    {
        // [FIX] Relasi 'items.product' tidak lagi dibutuhkan dan menjadi sumber error.
        // Kita hanya butuh relasi 'user' dan 'items'.
        $query = Checkout::with('user', 'items')->latest();

        return DataTables::of($query)
            ->editColumn('id', function($row) {
                return '#' . $row->id;
            })
            ->addColumn('pelanggan', function ($row) {
                if (!$row->user) {
                    return '<span class="text-red-500">Pengguna Dihapus</span>';
                }
                return '<div class="font-semibold text-gray-800">' . e($row->user->name) . '</div>' .
                       '<div class="text-xs text-gray-500">' . e($row->user->email) . '</div>';
            })
            ->addColumn('detail', function ($row) {
                // [PERBAIKAN UTAMA] Menampilkan detail dari checkout_items yang sudah aman
                $details = '<div class="flex flex-col gap-2">';
                foreach ($row->items as $item) {
                    $details .= '<div class="text-xs p-2 rounded-md bg-gray-50 border">';
                    $details .= '<p class="font-semibold text-gray-700">' . e($item->product_name) . '</p>';
                    $details .= '<p class="text-gray-500">Varian: ' . e($item->variant_info) . '</p>';
                    $details .= '<p class="text-gray-500">Qty: ' . e($item->quantity) . '</p>';
                    $details .= '</div>';
                }
                return $details . '</div>';
            })
            ->addColumn('total', function ($row) {
                return '<span class="font-semibold text-indigo-600">Rp' . number_format($row->total_price, 0, ',', '.') . '</span>';
            })
            ->editColumn('status', function ($row) {
                $statuses = ['Pending', 'Processing', 'Shipped', 'Completed', 'Cancelled'];
                $options = '';
                foreach ($statuses as $status) {
                    $selected = $row->status == $status ? 'selected' : '';
                    $options .= '<option value="' . $status . '" ' . $selected . '>' . $status . '</option>';
                }
                return '<select class="status-select text-xs font-semibold p-2 border border-gray-300 rounded-lg focus:ring-indigo-500 focus:border-indigo-500 w-full" data-id="' . $row->id . '">' . $options . '</select>';
            })
            ->addColumn('action', function ($row) {
                // [FIX] Menggunakan route 'admin.orders.destroy' jika ada, atau 'checkouts' jika tidak.
                // Disarankan untuk membuat route destroy yang konsisten.
                // Untuk saat ini, kita akan menonaktifkan tombol delete untuk mencegah error lebih lanjut.
                $invoiceUrl = route('admin.checkouts.invoice', $row->id);
                return '<a href="' . $invoiceUrl . '" class="p-2 w-9 h-9 flex items-center justify-center bg-blue-500 text-white rounded-lg hover:bg-blue-600 transition shadow" title="Lihat Invoice"><i class="bi bi-receipt"></i></a>';
            })
            ->rawColumns(['pelanggan', 'detail', 'total', 'status', 'action'])
            ->make(true);
    }
    
    /**
     * Memperbarui status pesanan.
     */
    public function updateStatus(Request $request, $id)
    {
        $request->validate(['status' => 'required|string']);
        $checkout = Checkout::findOrFail($id);
        $checkout->status = $request->status;
        $checkout->save();

        return response()->json(['success' => 'Status pesanan #' . $id . ' berhasil diperbarui!']);
    }

    /**
     * Menampilkan invoice (contoh).
     */
    public function invoice($id)
    {
        $checkout = Checkout::with('user', 'items')->findOrFail($id);
        // Anda bisa membuat view khusus untuk invoice di sini
        return "<h1>Invoice untuk Pesanan #{$checkout->id}</h1><p>Pelanggan: {$checkout->user->name}</p>";
    }
}

