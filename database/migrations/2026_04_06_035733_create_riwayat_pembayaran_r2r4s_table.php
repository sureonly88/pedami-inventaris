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
        Schema::create('riwayat_pembayaran_r2r4s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_r2r4_id')->constrained('data_r2r4s')->cascadeOnDelete();
            $table->enum('jenis_pembayaran', ['Pajak', 'STNK', 'KIR']);
            $table->date('tanggal_pembayaran');
            $table->bigInteger('biaya')->default(0);
            $table->date('jatuh_tempo_berikutnya')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('bukti_foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_pembayaran_r2r4s');
    }
};
