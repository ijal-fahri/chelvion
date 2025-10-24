<?php

namespace App\Http\Controllers;

use App\Models\Checkout;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Yajra\DataTables\Facades\DataTables; // Import library DataTables

class OrderController extends Controller
{
    /**
     * Menampilkan halaman daftar pesanan (orders) di sisi admin.
     * Halaman ini akan memuat tabel yang datanya diambil melalui AJAX.
     */
    public function index()
    {
        // Untuk halaman admin, kembalikan view admin
        // Cek apakah request datang dari URL admin
        if (request()->is('admin/*')) {
             return view('admin.orders.index');
        }

        // Ini adalah logic lama Anda untuk riwayat pesanan user biasa
        $orders = Checkout::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('orders.index', compact('orders'));
    }

    /**
     * Menyediakan data untuk DataTables di halaman admin orders.
     * Method ini akan dipanggil oleh AJAX dari view.
     */
    public function data(Request $request)
    {
        // Membuat query dasar untuk mengambil checkout beserta relasi user
        $query = Checkout::with('user')->select('checkouts.*');

        // Filter berdasarkan rentang tanggal jika ada input
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [$request->start_date, $request->end_date]);
        }

        // Proses data menggunakan Yajra DataTables
        return DataTables::of($query)
            ->addIndexColumn() // Menambahkan kolom nomor urut (DT_RowIndex)
            ->addColumn('pelanggan', function ($row) { // [PERBAIKAN] Diubah dari 'namapelanggan' ke 'pelanggan'
                // Menambahkan kolom nama customer dari relasi user
                return $row->user->name ?? 'N/A';
            })
            ->editColumn('total', function($row) {
                // Memformat kolom total menjadi format Rupiah
                return 'Rp ' . number_format($row->total, 0, ',', '.');
            })
            ->editColumn('status', function($row) {
                // Memberi label warna berdasarkan status
                if ($row->status == 'paid') {
                    return '<span class="px-2 py-1 font-semibold leading-tight text-green-700 bg-green-100 rounded-full">Paid</span>';
                } else if ($row->status == 'unpaid') {
                    return '<span class="px-2 py-1 font-semibold leading-tight text-yellow-700 bg-yellow-100 rounded-full">Unpaid</span>';
                } else {
                    return '<span class="px-2 py-1 font-semibold leading-tight text-red-700 bg-red-100 rounded-full">Expired</span>';
                }
            })
            ->addColumn('action', function ($row) {
                // Menambahkan kolom aksi (contoh: tombol detail)
                // Sesuaikan route ke detail order admin
                $btn = '<a href="' . route('admin.orders.show', $row->id) . '" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700">Detail</a>';
                return $btn;
            })
            ->rawColumns(['action', 'status']) // Kolom yang mengandung HTML
            ->make(true); // Render response
    }


    /**
     * Menampilkan halaman detail satu pesanan.
     */
    public function show(Checkout $order)
    {
        // Cek apakah request untuk admin atau user biasa
        if (request()->is('admin/*')) {
            // Untuk admin, tidak perlu cek user_id, langsung tampilkan
            $order->load(['items.product', 'items.variant', 'user']);
            return view('admin.orders.show', compact('order'));
        }

        // Logic lama Anda untuk user biasa
        if ($order->user_id !== Auth::id()) {
            abort(404);
        }
        $order->load(['items.product', 'items.variant']);
        return view('orders.show', compact('order'));
    }
}

