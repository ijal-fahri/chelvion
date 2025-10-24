<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Checkout extends Model
{
    use HasFactory;

    /**
     * Menggunakan $guarded adalah alternatif yang lebih fleksibel dari $fillable.
     * Ini mengizinkan SEMUA kolom untuk diisi secara massal KECUALI kolom 'id'.
     * Ini akan secara otomatis mengizinkan semua data dari controller (seperti invoice_number,
     * name, address, city, state, grand_total, dll) untuk disimpan dengan benar.
     */
    protected $guarded = ['id'];

    /**
     * Relasi ke model User.
     * Sebuah checkout (pesanan) dimiliki oleh satu user.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Relasi ke model CheckoutItem.
     * Sebuah checkout (pesanan) memiliki banyak item.
     */
    public function items()
    {
        return $this->hasMany(CheckoutItem::class);
    }
}

