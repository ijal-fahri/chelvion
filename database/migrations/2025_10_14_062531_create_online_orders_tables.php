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
        // Tabel utama untuk menyimpan data pesanan online
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique(); // Nomor pesanan unik, cth: ORD-20251014-XXXXXX

            // Siapa yang memesan
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');

            // Detail Transaksi
            $table->decimal('total_price', 15, 2);
            $table->string('status')->default('Pending'); // Status awal: Pending, Processing, Shipped, Completed, Cancelled
            $table->string('payment_method');
            $table->string('delivery_method'); // 'antar' atau 'ambil'

            // Informasi Voucher (jika ada)
            $table->string('voucher_code')->nullable();
            $table->decimal('discount_amount', 15, 2)->default(0);

            // Kolom untuk metode 'antar' (Shipping Address)
            $table->foreignId('shipping_cabang_id')->nullable()->constrained('cabangs')->onDelete('set null');
            $table->string('receiver_name')->nullable();
            $table->string('phone_number')->nullable();
            $table->text('full_address')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();

            // Kolom untuk metode 'ambil' (Pickup)
            $table->foreignId('pickup_cabang_id')->nullable()->constrained('cabangs')->onDelete('set null');
            
            $table->timestamps();
        });

        // Tabel detail untuk menyimpan item-item dalam satu pesanan
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained('orders')->onDelete('cascade');
            
            // Relasi ke produk dan varian asli
            $table->foreignId('product_id')->constrained('products')->onDelete('cascade');
            $table->foreignId('variant_id')->constrained('product_variants')->onDelete('cascade');

            // Simpan info produk saat itu untuk histori
            $table->string('product_name'); 
            $table->string('variant_info');  // cth: "8GB / Black"
            $table->integer('quantity');
            $table->decimal('price', 15, 2); // Harga per item saat transaksi

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_items');
        Schema::dropIfExists('orders');
    }
};