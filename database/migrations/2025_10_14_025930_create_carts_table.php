<?php

// database/migrations/xxxx_xx_xx_xxxxxx_create_carts_table.php

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
        Schema::create('carts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');
            $table->integer('quantity');
            
            // Kolom ini menyimpan informasi saat item ditambahkan
            // Ini berguna agar jika harga produk/varian berubah, harga di keranjang tetap sama
            $table->string('color');
            $table->string('ram');
            $table->decimal('price', 15, 2); 
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('carts');
    }
};