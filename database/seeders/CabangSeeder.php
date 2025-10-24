<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Cabang;

class CabangSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Cabang::create([
            'nama_cabang' => 'Jakarta Pusat',
            'alamat' => 'Jl. Jend. Sudirman No. Kav. 52-53, Senayan, Kebayoran Baru',
            'whatsapp' => '081234567890',
        ]);
    }
}
