<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('no_bpjs_ketenagakerjaan')->nullable()->after('no_rekening');
            $table->string('no_bpjs_kesehatan')->nullable()->after('no_bpjs_ketenagakerjaan');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn([
                'no_bpjs_ketenagakerjaan',
                'no_bpjs_kesehatan',
            ]);
        });
    }
};