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
        Schema::create('dokumentasi_panel', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 150);
            $table->text('deskripsi')->nullable();
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });

        Schema::create('dokumentasi_item', function (Blueprint $table) {
            $table->id();
            $table->foreignId('panel_id')->constrained('dokumentasi_panel')->cascadeOnDelete()->cascadeOnUpdate();
            $table->string('label', 150);
            $table->enum('gaya', ['solid', 'dashed', 'border'])->default('solid');
            $table->char('warna', 7);
            $table->unsignedInteger('urutan')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dokumentasi_item');
        Schema::dropIfExists('dokumentasi_panel');
    }
};
