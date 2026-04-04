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
        Schema::create('riwayat_servis_r2r4s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_r2r4_id')->constrained('data_r2r4s')->cascadeOnDelete();
            $table->date('tanggal_servis');
            $table->string('jenis_servis');
            $table->bigInteger('biaya')->default(0);
            $table->string('bengkel')->nullable();
            $table->text('keterangan')->nullable();
            $table->string('struk_foto')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('riwayat_servis_r2r4s');
    }
};
