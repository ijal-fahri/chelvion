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
        Schema::create('trade_ins', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cabang_id')->constrained('cabangs')->onDelete('cascade');
            $table->foreignId('new_product_variant_id')->nullable()->constrained('product_variants')->onDelete('set null');
            $table->string('product_name'); 
            $table->string('specs');
            $table->unsignedBigInteger('cost_price');
            $table->string('completeness');
            $table->string('condition');
            $table->json('qc_details')->nullable();
            $table->text('staff_notes')->nullable();
            $table->enum('status', ['perlu_qc', 'selesai'])->default('perlu_qc');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('trade_ins');
    }
};
