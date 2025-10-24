<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TradeIn extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cabang_id',
        'kasir_id', 
        'new_product_variant_id', 
        'product_name',
        'specs',
        'cost_price',
        'completeness',
        'condition',
        'qc_details',
        'staff_notes',
        'status',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        // Ini akan otomatis mengubah kolom JSON menjadi array saat Anda mengambil data,
        // dan mengubah array menjadi JSON saat Anda menyimpan data. Sangat praktis.
        'qc_details' => 'array',
    ];

    /**
     * Mendapatkan relasi ke cabang.
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * Mendapatkan relasi ke user (kasir) yang menginput.
     * Hapus fungsi ini jika Anda tidak melacak kasir.
     */
    public function kasir()
    {
        return $this->belongsTo(User::class, 'kasir_id');
    }
}
