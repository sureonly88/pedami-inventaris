<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->string('no_ktp')->nullable()->after('nama_karyawan');
            $table->string('no_hp')->nullable()->after('no_ktp');
            $table->string('no_rekening')->nullable()->after('no_hp');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn(['no_ktp', 'no_hp', 'no_rekening']);
        });
    }
};