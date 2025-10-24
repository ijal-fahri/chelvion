<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StockRequest extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     * Menggunakan guarded agar lebih fleksibel saat menyimpan data.
     *
     * @var array
     */
    protected $guarded = ['id'];

    /**
     * Relasi ke User (Admin yang membuat permintaan).
     */
    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }

    /**
     * Relasi ke User (Staf yang memproses permintaan).
     */
    public function staff()
    {
        return $this->belongsTo(User::class, 'staff_id');
    }

    /**
     * Relasi ke Cabang tempat permintaan dibuat.
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * Relasi ke ProductVariant yang diminta.
     */
    public function productVariant()
    {
        return $this->belongsTo(ProductVariant::class);
    }
}

