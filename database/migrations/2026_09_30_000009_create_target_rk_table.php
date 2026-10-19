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
        Schema::create('target_rk', function (Blueprint $table) {
            $table->id();
            $table->year('tahun')->unique();
            $table->unsignedInteger('tajak_target')->nullable();
            $table->unsignedInteger('completed_target')->nullable();
            $table->unsignedInteger('operasional_target')->nullable();
            $table->unsignedInteger('tajak_realisasi')->nullable();
            $table->unsignedInteger('tajak_rkap')->nullable();
            $table->unsignedInteger('tajak_wpb')->nullable();
            $table->unsignedInteger('completed_realisasi')->nullable();
            $table->unsignedInteger('completed_rkap')->nullable();
            $table->unsignedInteger('completed_wpb')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('target_rk');
    }
};
