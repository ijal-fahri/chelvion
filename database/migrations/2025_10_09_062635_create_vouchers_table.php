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
        Schema::create('vouchers', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->unique();
            $table->text('description')->nullable();

            // --- [PERUBAHAN] ---
            $table->string('type')->default('percentage'); // Tipe: 'percentage' atau 'fixed'
            $table->decimal('discount_percentage', 8, 2)->nullable(); // Misal: 15.50%
            $table->decimal('discount_amount', 15, 2)->nullable(); // Misal: Rp 50,000.00
            $table->decimal('min_purchase', 15, 2)->default(0); // Minimal pembelian
            $table->decimal('max_discount', 15, 2)->nullable(); // Maksimal diskon (untuk persentase)
            // --- [AKHIR PERUBAHAN] ---

            $table->unsignedInteger('stock')->nullable(); // Batas penggunaan
            $table->unsignedInteger('times_used')->default(0);
            $table->date('expiry_date')->nullable();
            $table->foreignId('cabang_id')->nullable()->constrained('cabangs')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vouchers');
    }
};