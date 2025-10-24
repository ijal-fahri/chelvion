<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OfflineOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'invoice_number',
        'kasir_id',
        'cabang_id',
        'customer_name',
        'customer_phone',
        'customer_email',
        'total_amount',
        'payment_method',
        'status',
    ];

    // Satu order dimiliki oleh satu kasir (User)
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }

    // Satu order dimiliki oleh satu cabang
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    // Satu order memiliki banyak item
    public function items()
    {
        return $this->hasMany(OfflineOrderItem::class);
    }
}