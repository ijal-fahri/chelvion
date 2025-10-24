<?php

namespace App\Http\Controllers\Pelanggan;

use App\Http\Controllers\Controller;
use App\Models\Review;
use App\Models\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ReviewController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'order_item_id' => 'required|exists:order_items,id',
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000',
            'images.*' => 'nullable|image|mimes:jpeg,png,jpg,webp|max:2048', // Validasi setiap gambar
        ]);

        $orderItem = OrderItem::with('order')->find($validated['order_item_id']);

        // Pastikan item milik user yang sedang login dan pesanan sudah selesai
        if ($orderItem->order->user_id !== Auth::id() || $orderItem->order->status !== 'Selesai') {
            return back()->with('error', 'Aksi tidak diizinkan.');
        }

        // Cek apakah item sudah pernah diulas
        if ($orderItem->review) {
            return back()->with('error', 'Produk ini sudah Anda ulas.');
        }

        $imagePaths = [];
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $image) {
                // Simpan gambar ke storage/app/public/reviews
                $path = $image->store('reviews', 'public');
                $imagePaths[] = $path;
            }
        }

        Review::create([
            'order_item_id' => $orderItem->id,
            'user_id' => Auth::id(),
            'product_id' => $orderItem->product_id,
            'rating' => $validated['rating'],
            'comment' => $validated['comment'],
            'images' => $imagePaths,
        ]);

        return redirect()->route('orderan.index')->with('success', 'Terima kasih atas ulasan Anda!');
    }
}