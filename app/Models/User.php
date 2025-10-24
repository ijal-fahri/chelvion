<?php

namespace App\Models;

// [BARU] Tambahkan ini untuk mengakses file
use Illuminate\Support\Facades\Storage; 
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'usertype',
        'cabang_id',
        'phone',
        'address',
        'photo', // 'photo' sudah ada di sini, bagus!
        'status_karyawan',
        'joining_date',
        'contract_end',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * [BARU] Atribut ini akan otomatis ditambahkan ke JSON response.
     */
    protected $appends = ['photo_url'];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'joining_date' => 'datetime', // [DIUBAH] Tambahkan ini
        ];
    }

    /**
     * [BARU] Accessor untuk mendapatkan URL foto profil.
     * Ini akan membuat atribut 'photo_url' secara dinamis.
     */
    public function getPhotoUrlAttribute()
    {
        if ($this->photo) {
            // Jika user punya foto, kembalikan URL dari storage
            return Storage::url($this->photo);
        }

        // Jika tidak, kembalikan placeholder
        $initials = strtoupper(substr($this->name, 0, 2));
        return "https://placehold.co/128x128/e0e7ff/4f46e5?text={$initials}";
    }

    /**
     * Relasi many-to-one ke Cabang.
     */
    public function cabang()
    {
        return $this->belongsTo(Cabang::class);
    }

    /**
     * Relasi one-to-many ke Order.
     */
    public function orders()
    {
        return $this->hasMany(Order::class, 'customer_id');
    }

    /**
     * Relasi one-to-many ke Cart.
     */
    public function carts(): HasMany
    {
        return $this->hasMany(Cart::class);
    }
}