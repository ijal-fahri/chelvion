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
        Schema::table('checkout_items', function (Blueprint $table) {
            // Menambahkan foreign key untuk product_variant_id
            // Pastikan ini ditambahkan setelah kolom yang sudah ada, misalnya 'product_id'
            // onDelete('set null') berarti jika varian produk dihapus, data penjualan ini tidak ikut terhapus.
            $table->foreignId('product_variant_id')->nullable()->constrained()->onDelete('set null')->after('product_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('checkout_items', function (Blueprint $table) {
            // Ini akan menghapus foreign key dan kolomnya jika migrasi di-rollback
            $table->dropForeign(['product_variant_id']);
            $table->dropColumn('product_variant_id');
        });
    }
};
