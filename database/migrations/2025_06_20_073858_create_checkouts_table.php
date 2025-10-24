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
        Schema::create('checkouts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');

            // [INI YANG DITAMBAHKAN]
            // Menghubungkan setiap transaksi checkout ke sebuah cabang
            $table->unsignedBigInteger('cabang_id')->nullable();

            $table->string('receiver_name');
            $table->string('payment_method');
            $table->string('delivery_method');
            
            $table->decimal('total_price', 15, 2); 
            
            $table->string('status')->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('checkouts');
    }
};
