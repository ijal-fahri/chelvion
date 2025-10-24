<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Method ini akan dijalankan saat migrasi
        Schema::table('carts', function (Blueprint $table) {
            // Mengubah kolom 'ram' dan 'color' agar bisa menerima nilai NULL
            $table->string('ram')->nullable()->change();
            $table->string('color')->nullable()->change();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Method ini untuk membatalkan migrasi (opsional tapi praktik yang baik)
        Schema::table('carts', function (Blueprint $table) {
            // Mengembalikan kolom menjadi tidak bisa NULL
            // PERHATIAN: Ini akan gagal jika sudah ada data NULL di dalamnya
            $table->string('ram')->nullable(false)->change();
            $table->string('color')->nullable(false)->change();
        });
    }
};