<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cabang;
use App\Models\Voucher; // <-- [TAMBAH] Impor model Voucher
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CheckoutController extends Controller
{
    public function create(Request $request)
    {
        // ... (Logika $selectedIdsString, $selectedIds, $carts tetap sama) ...
        $selectedIdsString = $request->input('selected_ids');
        if (!$selectedIdsString) {
            return redirect()->route('cart.show')->with('error', 'Anda belum memilih item untuk di-checkout.');
        }
        $selectedIds = explode(',', $selectedIdsString);
        $carts = Cart::with(['product', 'variant'])
            ->where('user_id', Auth::id())
            ->whereIn('id', $selectedIds)
            ->get();
        if ($carts->isEmpty()) {
            return redirect()->route('cart.show')->with('error', 'Item yang dipilih tidak valid atau keranjang kosong!');
        }
        // ... (Logika $grandTotal dan $cabangs tetap sama) ...
        $grandTotal = $carts->sum(fn($item) => ($item->variant->price ?? $item->product->price) * $item->quantity);
        $cabangs = Cabang::all();


        // --- [PERBAIKAN 1: Logika Pengambilan Voucher] ---
        // Kita ubah query untuk mengizinkan nilai NULL (unlimited/selamanya)
        $vouchers = Voucher::where(function ($query) {
                                // Kondisi Stok: (Stok > 0 ATAU Stok adalah NULL (unlimited))
                                $query->where('stock', '>', 0)
                                      ->orWhereNull('stock');
                            })
                            ->where(function ($query) {
                                // Kondisi Tanggal: (Tanggal >= hari ini ATAU Tanggal adalah NULL (non-expiring))
                                $query->where('expiry_date', '>=', now()->toDateString())
                                      ->orWhereNull('expiry_date');
                            })
                            ->get();
        // --- [AKHIR PERBAIKAN 1] ---

        // Tambahkan $vouchers ke compact()
        return view('pelanggan.checkout.create', compact('carts', 'grandTotal', 'cabangs', 'vouchers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'delivery_method' => 'required|in:antar,ambil',
            'payment_method' => 'required|string',
            'voucher_code' => 'nullable|string|max:50', 
            'shipping_cabang_id' => ['required_if:delivery_method,antar', 'nullable', 'exists:cabangs,id'],
            'receiver_name' => 'required_if:delivery_method,antar|nullable|string|max:255',
            'phone_number' => 'required_if:delivery_method,antar|nullable|string|max:20',
            'full_address' => 'required_if:delivery_method,antar|nullable|string',
            'kecamatan' => 'required_if:delivery_method,antar|nullable|string',
            'city' => 'required_if:delivery_method,antar|nullable|string',
            'province' => 'required_if:delivery_method,antar|nullable|string',
            'pickup_cabang_id' => ['required_if:delivery_method,ambil', 'nullable', 'exists:cabangs,id'],
        ]);

        try {
            $order = DB::transaction(function () use ($validated, $request) {
                $user = Auth::user();
                
                $request->validate(['cart_ids' => 'required|string']);
                $selectedIds = explode(',', $request->cart_ids);

                $carts = Cart::with(['product', 'variant'])
                        ->where('user_id', $user->id)
                        ->whereIn('id', $selectedIds) 
                        ->get();

                if ($carts->isEmpty()) {
                    throw new \Exception("Keranjang kosong, tidak bisa checkout.");
                }

                foreach ($carts as $item) {
                    if ($item->variant->stock < $item->quantity) {
                        throw new \Exception("Stok untuk '{$item->product->name}' tidak mencukupi.");
                    }
                }

                $grandTotal = $carts->sum(fn($item) => ($item->variant->price ?? $item->product->price) * $item->quantity);
                
                $discountAmount = 0;
                $voucherCode = $validated['voucher_code'] ?? null;
                $voucher = null;

                if ($voucherCode) {
                    $selectedCabangId = $validated['shipping_cabang_id'] ?? $validated['pickup_cabang_id'];

                    if (!$selectedCabangId) {
                         throw new \Exception("Cabang pengiriman atau pengambilan harus dipilih untuk menggunakan voucher.");
                    }

                    // --- [PERBAIKAN 2: Logika Validasi Voucher di Backend] ---
                    // Query ini juga harus diubah agar sama dengan di method create()
                    $voucher = Voucher::where('code', $voucherCode)
                        ->where(function ($query) {
                            // Stok > 0 ATAU stok adalah NULL
                            $query->where('stock', '>', 0)
                                  ->orWhereNull('stock');
                        })
                        ->where(function ($query) {
                            // Tanggal >= hari ini ATAU tanggal adalah NULL
                            $query->where('expiry_date', '>=', now()->toDateString())
                                  ->orWhereNull('expiry_date');
                        })
                        ->where(function ($query) use ($selectedCabangId) {
                            // Voucher global (cabang_id IS NULL) ATAU voucher khusus cabang itu
                            $query->whereNull('cabang_id') 
                                  ->orWhere('cabang_id', $selectedCabangId);
                        })
                        ->first();
                    // --- [AKHIR PERBAIKAN 2] ---
                    
                    if (!$voucher) {
                        throw new \Exception("Voucher '{$voucherCode}' tidak valid, kadaluwarsa, atau tidak berlaku di cabang ini.");
                    }

                    // 2. Cek minimal pembelian
                    if ($voucher->min_purchase > 0 && $grandTotal < $voucher->min_purchase) {
                        throw new \Exception("Total belanja (Rp " . number_format($grandTotal) . ") tidak mencukupi untuk minimal pembelian voucher (Rp " . number_format($voucher->min_purchase) . ").");
                    }

                    // 3. Hitung diskon berdasarkan tipe (KALKULASI ULANG DI BACKEND)
                    if ($voucher->type === 'percentage') {
                        $discountAmount = $grandTotal * ($voucher->discount_percentage / 100);
                        if ($voucher->max_discount > 0 && $discountAmount > $voucher->max_discount) {
                            $discountAmount = $voucher->max_discount;
                        }
                    } elseif ($voucher->type === 'fixed') {
                        $discountAmount = $voucher->discount_amount;
                        if ($discountAmount > $grandTotal) {
                            $discountAmount = $grandTotal;
                        }
                    }
                }

                $initialStatus = ($validated['delivery_method'] === 'ambil') ? 'Menunggu Diambil' : 'Diproses';

                $order = Order::create([
                    'user_id' => $user->id,
                    'order_number' => 'ORD-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6)),
                    'total_price' => $grandTotal - $discountAmount,
                    'status' => $initialStatus,
                    'payment_method' => $validated['payment_method'],
                    'delivery_method' => $validated['delivery_method'],
                    'voucher_code' => $voucher ? $voucher->code : null,
                    'discount_amount' => $discountAmount,
                    'shipping_cabang_id' => $validated['shipping_cabang_id'] ?? null,
                    'receiver_name' => $validated['receiver_name'] ?? $user->name,
                    'phone_number' => $validated['phone_number'] ?? null,
                    'full_address' => $validated['full_address'] ?? null,
                    'kecamatan' => $validated['kecamatan'] ?? null,
                    'city' => $validated['city'] ?? null,
                    'province' => $validated['province'] ?? null,
                    'pickup_cabang_id' => $validated['pickup_cabang_id'] ?? null,
                ]);

                foreach ($carts as $item) {
                     $order->items()->create([
                        'product_id' => $item->product_id,
                        'variant_id' => $item->variant_id,
                        'product_name' => $item->product->name,
                        'variant_info' => $item->variant->ram . ' / ' . $item->variant->storage . ' / ' . $item->variant->color,
                        'quantity' => $item->quantity,
                        'price' => $item->variant->price ?? $item->product->price,
                    ]);
                    $item->variant->decrement('stock', $item->quantity);
                }

                Cart::whereIn('id', $selectedIds)->where('user_id', $user->id)->delete();

                // --- [PERBAIKAN 3: Logika Pengurangan Stok Voucher] ---
                // Kita hanya boleh mengurangi stok jika stoknya tidak NULL
                if ($voucher) {
                    // Hanya kurangi stok jika stoknya BUKAN unlimited (bukan NULL)
                    if ($voucher->stock !== null) {
                        $voucher->decrement('stock');
                    }
                    $voucher->increment('times_used'); // Tetap catat penggunaan
                }
                // --- [AKHIR PERBAIKAN 3] ---
                
                return $order; 
            });
            
            return redirect()->route('pelanggan.orders.show', $order->id)->with('success', 'Pesanan Anda berhasil dibuat!');

        } catch (\Exception $e) {
            return back()->with('error', 'Terjadi kesalahan: ' . $e->getMessage())->withInput();
        }
    }
}