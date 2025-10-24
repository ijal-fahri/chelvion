<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'order_number',
        'total_price',
        'status',
        'payment_method',
        'delivery_method',
        'voucher_code',
        'discount_amount',
        'shipping_cabang_id',
        'receiver_name',
        'phone_number',
        'full_address',
        'kecamatan',
        'city',
        'province',
        'pickup_cabang_id',
    ];

    // Satu pesanan dimiliki oleh satu user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Satu pesanan memiliki banyak item
    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Relasi ke cabang pengirim (jika diantar)
    public function shippingCabang()
    {
        return $this->belongsTo(Cabang::class, 'shipping_cabang_id');
    }

    // Relasi ke cabang pengambilan (jika ambil di toko)
    public function pickupCabang()
    {
        return $this->belongsTo(Cabang::class, 'pickup_cabang_id');
    }
}