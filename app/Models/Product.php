<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'master_price',
        'category',
        'status',
        'display_status',
        'image',
    ];

    /**
     * Relasi one-to-many: Satu produk master bisa punya banyak varian.
     */
    public function variants()
    {
        return $this->hasMany(ProductVariant::class);
    }

    /**
     * [BARU] Relasi untuk mengambil varian yang berstatus 'draft'.
     */
    public function draftVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'draft');
    }

    /**
     * [BARU] Relasi untuk mengambil varian yang berstatus 'published'.
     */
    public function publishedVariants()
    {
        return $this->hasMany(ProductVariant::class)->where('status', 'published');
    }
}

