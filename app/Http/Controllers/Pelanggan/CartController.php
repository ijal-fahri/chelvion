<?php

namespace App\Http\Controllers\Pelanggan;

use App\Models\Cart;
use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;

class CartController extends Controller
{
    /**
     * Menambahkan produk ke keranjang via AJAX.
     */
    public function add(Request $request)
    {
        // Cek apakah user sudah login
        if (!Auth::check()) {
            return response()->json([
                'success' => false, 
                'message' => 'Silakan login untuk menambahkan produk ke keranjang.',
                'redirect' => route('login')
            ], 401);
        }

        // Validasi data yang dibutuhkan
        $validated = $request->validate([
            'product_id' => 'required|exists:products,id',
            'variant_id' => 'required|exists:product_variants,id',
            'quantity'   => 'required|integer|min:1',
            'color'      => 'nullable|string|max:50', // Diubah menjadi opsional
            'ram'        => 'nullable|string|max:50', // Diubah menjadi opsional
            'price'      => 'required|numeric|min:0',
        ]);

        try {
            $variant = ProductVariant::findOrFail($validated['variant_id']);
            $product = Product::findOrFail($validated['product_id']);
            $userId = Auth::id();

            // Cek stok yang tersedia
            if ($variant->stock < $validated['quantity']) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok produk tidak mencukupi. Stok tersedia: ' . $variant->stock,
                ], 422);
            }

            // Cek apakah varian yang sama persis sudah ada di keranjang
            $existingCart = Cart::where('user_id', $userId)
                ->where('variant_id', $validated['variant_id'])
                ->first();

            if ($existingCart) {
                // Jika sudah ada, cek lagi stok totalnya
                $newQuantity = $existingCart->quantity + $validated['quantity'];
                if ($variant->stock < $newQuantity) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak cukup. Anda sudah memiliki ' . $existingCart->quantity . ' item di keranjang. Stok tersedia: ' . $variant->stock,
                    ], 422);
                }
                
                // Jika stok aman, update quantity
                $existingCart->update(['quantity' => $newQuantity]);
                $cartItem = $existingCart;
            } else {
                // Jika belum ada, buat entri baru
                $cartItem = Cart::create([
                    'user_id'    => $userId,
                    'product_id' => $validated['product_id'],
                    'variant_id' => $validated['variant_id'],
                    'quantity'   => $validated['quantity'],
                    'color'      => $validated['color'],
                    'ram'        => $validated['ram'],
                    'price'      => $validated['price'],
                ]);
            }

            // Hitung total cart count dan subtotal
            $cartCount = Auth::user()->carts()->count();
            $subtotal = $cartItem->price * $cartItem->quantity;

            // Pastikan tidak ada dd(), dump(), var_dump(), atau echo di sini

            return response()->json([
                'success' => true, 
                'message' => 'Produk berhasil ditambahkan ke keranjang!',
                'cart_count' => $cartCount,
                'subtotal' => number_format($subtotal, 0, ',', '.'),
                'item_id' => $cartItem->id
            ]);

        } catch (\Exception $e) {
            \Log::error('Error adding to cart: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Menampilkan halaman keranjang.
     */
    public function show()
    {
        $carts = Cart::with(['product', 'variant'])
            ->where('user_id', Auth::id())
            ->latest()
            ->get();

        // Hitung total keseluruhan
        $total = $carts->sum(function($cart) {
            return $cart->price * $cart->quantity;
        });

        // Hitung total items
        $totalItems = $carts->sum('quantity');

        return view('pelanggan.cart.show', compact('carts', 'total', 'totalItems'));
    }

    /**
     * Update quantity item di keranjang.
     */
    public function update(Request $request, Cart $cart)
    {
        // Authorization check
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false, 
                'message' => 'Aksi tidak diizinkan.'
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        try {
            // Cek stok
            if ($cart->variant->stock < $request->quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi. Stok tersedia: ' . $cart->variant->stock
                ], 422);
            }

            // Update quantity
            $cart->update(['quantity' => $request->quantity]);

            // Hitung ulang subtotal dan total
            $subtotal = $cart->price * $cart->quantity;
            $total = Auth::user()->carts()->get()->sum(function($cart) {
                return $cart->price * $cart->quantity;
            });
            $cartCount = Auth::user()->carts()->count();

            return response()->json([
                'success' => true,
                'message' => 'Quantity berhasil diupdate',
                'subtotal' => 'Rp' . number_format($subtotal, 0, ',', '.'),
                'total' => 'Rp' . number_format($total, 0, ',', '.'),
                'cart_count' => $cartCount,
                'new_quantity' => $cart->quantity
            ]);

        } catch (\Exception $e) {
            \Log::error('Error updating cart: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
            ], 500);
        }
    }

    /**
     * Menghapus item dari keranjang.
     */
    public function destroy(Cart $cart, Request $request)
    {
        // Authorization check
        if ($cart->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false, 
                    'message' => 'Aksi tidak diizinkan.'
                ], 403);
            }
            return redirect()->route('cart.show')->with('error', 'Aksi tidak diizinkan.');
        }

        try {
            $cart->delete();

            // Hitung ulang total dan cart count
            $total = Auth::user()->carts()->get()->sum(function($cart) {
                return $cart->price * $cart->quantity;
            });
            $cartCount = Auth::user()->carts()->count();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Item berhasil dihapus dari keranjang.',
                    'total' => 'Rp' . number_format($total, 0, ',', '.'),
                    'cart_count' => $cartCount
                ]);
            }

            return back()->with('success', 'Item berhasil dihapus dari keranjang.');

        } catch (\Exception $e) {
            \Log::error('Error deleting cart item: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Mengosongkan seluruh keranjang.
     */
    public function clear(Request $request)
    {
        try {
            $deleted = Cart::where('user_id', Auth::id())->delete();

            if ($request->expectsJson()) {
                return response()->json([
                    'success' => true, 
                    'message' => 'Keranjang berhasil dikosongkan.',
                    'cart_count' => 0,
                    'total' => 'Rp0'
                ]);
            }

            return back()->with('success', 'Keranjang berhasil dikosongkan.');

        } catch (\Exception $e) {
            \Log::error('Error clearing cart: ' . $e->getMessage());
            
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Terjadi kesalahan sistem. Silakan coba lagi.'
                ], 500);
            }

            return back()->with('error', 'Terjadi kesalahan sistem. Silakan coba lagi.');
        }
    }

    /**
     * Get cart summary (untuk update real-time di navbar).
     */
    public function getSummary()
    {
        if (!Auth::check()) {
            return response()->json([
                'success' => true,
                'cart_count' => 0,
                'total' => 'Rp0'
            ]);
        }

        $carts = Auth::user()->carts;
        $cartCount = $carts->count();
        $total = $carts->sum(function($cart) {
            return $cart->price * $cart->quantity;
        });

        return response()->json([
            'success' => true,
            'cart_count' => $cartCount,
            'total' => 'Rp' . number_format($total, 0, ',', '.')
        ]);
    }
}