<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('data_r2r4s', function (Blueprint $table) {
            $table->date('tgl_stop_tagihan')->nullable()->after('hrg_sewa');
            $table->string('alasan_stop_tagihan')->nullable()->after('tgl_stop_tagihan');
        });
    }

    public function down(): void
    {
        Schema::table('data_r2r4s', function (Blueprint $table) {
            $table->dropColumn(['tgl_stop_tagihan', 'alasan_stop_tagihan']);
        });
    }
};