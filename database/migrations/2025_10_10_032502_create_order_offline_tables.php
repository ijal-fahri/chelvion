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
        // Tabel utama untuk menyimpan setiap transaksi
        Schema::create('offline_orders', function (Blueprint $table) {
            $table->id();
            $table->string('invoice_number')->unique(); // Nomor invoice unik

            // Siapa kasir & dari cabang mana
            $table->foreignId('kasir_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');

            // Data Pelanggan
            $table->string('customer_name');
            $table->string('customer_phone');
            $table->string('customer_email')->nullable();

            // Detail Transaksi
            $table->decimal('total_amount', 15, 2);
            $table->string('payment_method');
            $table->string('status')->default('Selesai'); // Status transaksi

            $table->timestamps();
        });

        // Tabel detail untuk menyimpan item-item dalam satu transaksi
        Schema::create('offline_order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('offline_order_id')->constrained('offline_orders')->onDelete('cascade');
            $table->foreignId('product_variant_id')->constrained('product_variants')->onDelete('cascade');

            $table->string('product_name'); // Simpan nama produk untuk histori
            $table->string('variant_info');  // Simpan info varian (misal: "Black / 8/256GB")
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Simpan harga per item saat transaksi
            $table->decimal('subtotal', 15, 2);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('offline_order_items');
        Schema::dropIfExists('offline_orders');
    }
};