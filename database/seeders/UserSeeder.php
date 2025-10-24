<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Cabang; // Pastikan model Cabang di-import
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $cabang1 = Cabang::find(1);
        $cabang2 = Cabang::find(2) ?? $cabang1; 

        User::create([
            'name' => 'Owner Utama',
            'email' => 'owner1@gmail.com',
            'password' => Hash::make('owner123'),
            'usertype' => 'owner',
            'cabang_id' => null, 
        ]);

        if ($cabang1) {
            User::create([
                'name' => 'Admin Cabang Jakarta',
                'email' => 'admin1@gmail.com',
                'password' => Hash::make('admin123'),
                'usertype' => 'admin',
                'cabang_id' => $cabang1->id, 
            ]);
        }

        if ($cabang1) {
            User::create([
                'name' => 'Staf Gudang Jakarta',
                'email' => 'staf1@gmail.com',
                'password' => Hash::make('staff123'),
                'usertype' => 'staf_gudang',
                'cabang_id' => $cabang1->id, 
            ]);
        }

        if ($cabang2) {
            User::create([
                'name' => 'Kasir Bogor',
                'email' => 'kasir1@gmail.com',
                'password' => Hash::make('kasir123'),
                'usertype' => 'kasir',
                'cabang_id' => $cabang2->id,
            ]);
        }
    }
}

