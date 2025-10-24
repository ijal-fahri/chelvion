<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Voucher extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'type',
        'discount_percentage',
        'discount_amount',
        'min_purchase',
        'max_discount',
        'stock',
        'times_used',
        'expiry_date',
        'cabang_id',
    ];

    protected $casts = [
        'expiry_date' => 'date', // Biarkan ini apa adanya
        'min_purchase' => 'decimal:2',
        'max_discount' => 'decimal:2',
        'discount_percentage' => 'decimal:2',
        'discount_amount' => 'decimal:2',
    ];

    /**
     * [BARU] Tambahkan properti ini untuk otomatis menyertakan
     * accessor kita di JSON/array.
     */
    protected $appends = ['expiry_date_formatted'];

    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * [BARU] Accessor untuk format tanggal kadaluwarsa yang dinamis.
     * Ini akan membuat atribut 'expiry_date_formatted'.
     */
    public function getExpiryDateFormattedAttribute()
    {
        // Kita periksa nilai mentah dari database
        // $this->attributes['expiry_date'] akan berisi '2025-10-22' atau NULL
        if (empty($this->attributes['expiry_date'])) {
            return 'Berlaku selamanya';
        }
        
        // Jika ada tanggal, $this->expiry_date akan otomatis jadi objek Carbon
        // berkat $casts di atas.
        return $this->expiry_date->isoFormat('D MMMM YYYY');
    }
}