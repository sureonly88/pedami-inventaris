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
        Schema::table('data_r2r4s', function (Blueprint $table) {
            $table->decimal('hrg_beli', 15, 2)->nullable()->after('hrg_sewa');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('data_r2r4s', function (Blueprint $table) {
            $table->dropColumn('hrg_beli');
        });
    }
};
