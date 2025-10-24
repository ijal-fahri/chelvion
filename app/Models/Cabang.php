<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cabang extends Model
{
    use HasFactory;

    /**
     * Menggunakan nama kolom sesuai modelmu agar konsisten.
     */
    protected $fillable = [
        'nama_cabang',
        'alamat',
        'whatsapp',
    ];

    /**
     * Relasi one-to-many: Satu cabang memiliki banyak User (karyawan).
     */
    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * Relasi one-to-many: Satu cabang memiliki banyak stok varian produk.
     */
    public function productVariants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function vouchers()
    {
        return $this->hasMany(Voucher::class);
    }
}
