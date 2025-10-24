<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'offline_order_id',
        'product_variant_id',
        'product_name',
        'variant_info',
        'quantity',
        'price',
        'subtotal',
    ];

    // Satu item order milik satu order utama
    public function order()
    {
        return $this->belongsTo(OfflineOrder::class, 'offline_order_id');
    }

    // Satu item order merujuk ke satu varian produk
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}