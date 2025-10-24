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
        Schema::table('users', function (Blueprint $table) {
            // 1. Ubah kolom 'usertype' menjadi enum yang benar
            $table->enum('usertype', ['owner', 'admin', 'staf_gudang', 'kasir', 'pelanggan'])->default('pelanggan')->change();

            // 2. Buat hubungan foreign key untuk 'cabang_id'
            $table->foreign('cabang_id')
                  ->references('id')
                  ->on('cabangs')
                  ->onDelete('set null');

            // 3. Tambahkan sisa kolom yang dibutuhkan
            $table->string('phone')->nullable()->after('cabang_id');
            $table->text('address')->nullable()->after('phone');
            $table->string('photo')->nullable()->after('address');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            // Hapus foreign key terlebih dahulu
            $table->dropForeign(['cabang_id']);

            // Hapus kolom-kolom tambahan
            $table->dropColumn(['phone', 'address', 'photo']);

            // Kembalikan 'usertype' ke bentuk semula
            $table->string('usertype')->default('user')->change();
        });
    }
};
