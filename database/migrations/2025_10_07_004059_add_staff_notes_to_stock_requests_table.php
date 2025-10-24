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
        Schema::table('stock_requests', function (Blueprint $table) {
            // Kolom baru untuk menyimpan alasan/catatan dari staf
            $table->text('staff_notes')->nullable()->after('notes');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('stock_requests', function (Blueprint $table) {
            $table->dropColumn('staff_notes');
        });
    }
};
