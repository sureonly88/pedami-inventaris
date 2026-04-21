<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mutasi_assets', function (Blueprint $table) {
            $table->string('gambar_awal')->nullable()->after('karyawan_id_a');
            $table->string('gambar_terbaru')->nullable()->after('gambar_awal');
        });
    }

    public function down(): void
    {
        Schema::table('mutasi_assets', function (Blueprint $table) {
            $table->dropColumn(['gambar_awal', 'gambar_terbaru']);
        });
    }
};