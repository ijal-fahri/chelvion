<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    use HasFactory;

    protected $fillable = [
        'order_item_id',
        'user_id',
        'product_id',
        'rating',
        'comment',
        'images',
    ];

    protected $casts = [
        'images' => 'array', // Otomatis cast kolom images ke array
    ];

    // Relasi ke User, Product, dan OrderItem
    public function user() { return $this->belongsTo(User::class); }
    public function product() { return $this->belongsTo(Product::class); }
    public function orderItem() { return $this->belongsTo(OrderItem::class); }
}