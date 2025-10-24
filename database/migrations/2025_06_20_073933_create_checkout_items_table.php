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
        Schema::create('checkout_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('checkout_id')->constrained('checkouts')->onDelete('cascade');
            
            // [PERBAIKAN] Menyimpan detail sebagai "snapshot" atau "nota"
            $table->string('product_name'); // Simpan nama produk saat itu
            $table->string('variant_info');  // Simpan info varian (cth: "Hitam / 8/128")
            
            $table->integer('quantity');
            
            // [PERBAIKAN] Menggunakan decimal untuk harga satuan saat itu
            $table->decimal('price', 15, 2);
            
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkout_items');
    }
};