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
        Schema::table('tb_toko', function (Blueprint $table) {
            $table->json('jadwal_libur')->nullable()->after('status_libur');
            $table->json('jam_operasional')->nullable()->after('jadwal_libur');
            $table->json('privasi_settings')->nullable()->after('jam_operasional');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('tb_toko', function (Blueprint $table) {
            $table->dropColumn(['jadwal_libur', 'jam_operasional', 'privasi_settings']);
        });
    }
};
