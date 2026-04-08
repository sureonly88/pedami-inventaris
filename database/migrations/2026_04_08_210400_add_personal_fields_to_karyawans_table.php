<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->text('alamat')->nullable()->after('no_rekening');
            $table->date('tanggal_lahir')->nullable()->after('alamat');
            $table->date('tanggal_masuk_kerja')->nullable()->after('tanggal_lahir');
            $table->string('tempat_lahir')->nullable()->after('tanggal_masuk_kerja');
            $table->string('nama_bank')->nullable()->after('tempat_lahir');
            $table->string('kontak_darurat')->nullable()->after('nama_bank');
        });
    }

    public function down(): void
    {
        Schema::table('karyawans', function (Blueprint $table) {
            $table->dropColumn([
                'alamat',
                'tanggal_lahir',
                'tanggal_masuk_kerja',
                'tempat_lahir',
                'nama_bank',
                'kontak_darurat',
            ]);
        });
    }
};