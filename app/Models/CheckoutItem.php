<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CheckoutItem extends Model
{
    use HasFactory;

    /**
     * Menggunakan $guarded untuk mengizinkan semua kolom diisi secara massal
     * kecuali 'id'. Ini akan memastikan semua data item (termasuk variant_id, dll.)
     * dari controller dapat disimpan dengan benar.
     */
    protected $guarded = ['id'];

    /**
     * Item checkout biasanya tidak memerlukan timestamp 'updated_at'.
     * Menonaktifkannya dapat menghemat sedikit ruang database.
     */
    public $timestamps = false;

    /**
     * Relasi ke model Checkout.
     * Setiap item dimiliki oleh satu checkout (pesanan).
     */
    public function checkout()
    {
        return $this->belongsTo(Checkout::class);
    }

    /**
     * Relasi ke model Product.
     * Setiap item merujuk pada satu produk.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke model ProductVariant.
     * Setiap item merujuk pada satu varian spesifik.
     */
    public function variant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

