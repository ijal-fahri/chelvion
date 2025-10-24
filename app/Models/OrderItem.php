<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    /**
     * The relationships that should always be loaded.
     *
     * @var array
     */
    protected $with = ['variant']; // <-- TAMBAHKAN BARIS INI

    protected $fillable = [
        'order_id',
        'product_id',
        'variant_id',
        'product_name',
        'variant_info',
        'quantity',
        'price',
    ];

    // Satu item pesanan milik satu pesanan utama
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

    // Satu item merujuk ke satu produk
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Satu item merujuk ke satu varian produk
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id');
    }

    public function review()
    {
        return $this->hasOne(Review::class);
    }
}