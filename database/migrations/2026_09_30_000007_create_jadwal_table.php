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
        Schema::create('jadwal', function (Blueprint $table) {
            $table->id();
            $table->foreignId('alat_id')->constrained('alat')->cascadeOnDelete()->cascadeOnUpdate();
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi')->nullOnDelete()->cascadeOnUpdate();
            $table->foreignId('kategori_id')->nullable()->constrained('dokumentasi_item')->nullOnDelete()->cascadeOnUpdate();
            $table->string('label', 150);
            $table->enum('status', ['selesai', 'progres', 'kendala'])->default('progres')->index();
            $table->date('periode_mulai');
            $table->unsignedTinyInteger('durasi_bulan')->default(1);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('jadwal');
    }
};
