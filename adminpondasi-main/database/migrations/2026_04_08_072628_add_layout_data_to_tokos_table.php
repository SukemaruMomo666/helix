<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
    	Schema::table('tb_toko', function (Blueprint $table) {
        // biarkan isi di dalamnya tetap sama
   	 });
    }

    public function down(): void
    {
    	Schema::table('tb_toko', function (Blueprint $table) {
        // biarkan isi di dalamnya tetap sama
    	});
    }
};
