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
            $table->string('status_karyawan')->nullable()->after('cabang_id');
            $table->date('joining_date')->nullable()->after('status_karyawan');
            $table->date('contract_end')->nullable()->after('joining_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn(['status_karyawan', 'joining_date', 'contract_end']);
        });
    }
};

