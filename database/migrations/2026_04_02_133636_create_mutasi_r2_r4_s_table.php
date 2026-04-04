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
        Schema::create('mutasi_r2r4s', function (Blueprint $table) {
            $table->id();
            $table->foreignId('data_r2r4_id')->constrained('data_r2r4s')->onDelete('cascade');
            $table->string('pemegang_awal')->nullable();
            $table->string('departemen_awal')->nullable();
            $table->string('pemegang_tujuan');
            $table->string('departemen_tujuan');
            $table->date('tgl_mutasi');
            $table->string('deskripsi')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('mutasi_r2r4s');
    }
};
