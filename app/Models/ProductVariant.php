<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductVariant extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'cabang_id',
        'color',
        'ram',
        'stock',
        'price',
        'image',
        'status',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = ['first_image_url']; // <-- CUKUP TAMBAHKAN BARIS INI

    /**
     * Accessor untuk gambar varian
     */
    public function getFirstImageUrlAttribute(): ?string
    {
        // Ambil path gambar langsung dari atribut
        $imagePath = $this->attributes['image'];

        // Cek jika path tidak kosong DAN file-nya benar-benar ada di storage
        if (!empty($imagePath) && Storage::disk('public')->exists($imagePath)) {
            // Jika ada, kembalikan URL lengkapnya
            return asset('storage/' . $imagePath);
        }

        // Jika tidak ada path atau file tidak ditemukan, kembalikan null (atau URL placeholder)
        return null; // JavaScript Anda sudah menangani jika ini null
    }

    /**
     * Accessor untuk gambar utama
     */
    public function getPrimaryImageUrlAttribute()
    {
        $urls = $this->image_urls;
        return !empty($urls) ? $urls[0] : null;
    }

    /**
     * Relasi ke model Product.
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /**
     * Relasi ke model Cabang.
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }
}