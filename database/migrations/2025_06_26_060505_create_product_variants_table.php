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
        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->onDelete('cascade');
            $table->foreignId('cabang_id')->constrained()->onDelete('cascade');
            $table->string('color');

            // Untuk HP: '8/256GB', untuk Aksesori: '25W' atau null
            $table->string('ram')->nullable()->comment('RAM/ROM untuk HP, Spesifikasi untuk Aksesori');

            // Untuk HP: harga per varian, untuk Aksesori: harga produk
            $table->decimal('price', 15, 2)->nullable();
            
            $table->integer('stock');
            $table->string('image')->nullable();
            $table->string('status')->default('draft');
            
            $table->timestamps();
            
            // Index untuk performa
            $table->index(['product_id', 'cabang_id']);
            $table->index(['cabang_id', 'stock']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_variants');
    }
};