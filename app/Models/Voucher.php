<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    /**
     * [PENTING] Pastikan semua field ini ada di $fillable
     */
    protected $fillable = [
        'name',
        'code',
        'description',
        'type',                 // 'percentage' or 'fixed'
        'discount_percentage',  // (nullable)
        'discount_amount',      // (nullable)
        'min_purchase',         // (nullable)
        'max_discount',         // (nullable)
        'stock',
        'times_used',
        'expiry_date',
        'cabang_id',
    ];

    protected $casts = [
        'expiry_date' => 'date',
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }
}