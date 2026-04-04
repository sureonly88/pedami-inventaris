<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('mutasi_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->date('tgl_mutasi');
            $table->string('jabatan_asal')->nullable();
            $table->string('jabatan_tujuan')->nullable();
            $table->unsignedBigInteger('divisi_asal_id')->nullable();
            $table->unsignedBigInteger('subdivisi_asal_id')->nullable();
            $table->unsignedBigInteger('divisi_tujuan_id')->nullable();
            $table->unsignedBigInteger('subdivisi_tujuan_id')->nullable();
            $table->string('alasan')->nullable();
            $table->string('no_sk')->nullable();
            $table->timestamps();

            $table->foreign('divisi_asal_id')->references('id')->on('divisis')->nullOnDelete();
            $table->foreign('subdivisi_asal_id')->references('id')->on('subdivisis')->nullOnDelete();
            $table->foreign('divisi_tujuan_id')->references('id')->on('divisis')->nullOnDelete();
            $table->foreign('subdivisi_tujuan_id')->references('id')->on('subdivisis')->nullOnDelete();
        });

        Schema::create('pensiun_karyawans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('karyawan_id')->constrained('karyawans')->cascadeOnDelete();
            $table->date('tgl_pensiun');
            $table->string('jenis_pensiun'); // Normal, Dini, Meninggal, Diberhentikan
            $table->string('no_sk')->nullable();
            $table->string('jabatan_terakhir')->nullable();
            $table->unsignedBigInteger('divisi_terakhir_id')->nullable();
            $table->unsignedBigInteger('subdivisi_terakhir_id')->nullable();
            $table->decimal('pesangon', 15, 2)->default(0);
            $table->text('keterangan')->nullable();
            $table->timestamps();

            $table->foreign('divisi_terakhir_id')->references('id')->on('divisis')->nullOnDelete();
            $table->foreign('subdivisi_terakhir_id')->references('id')->on('subdivisis')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('pensiun_karyawans');
        Schema::dropIfExists('mutasi_karyawans');
    }
};
