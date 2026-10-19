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
        Schema::create('alat', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->enum('kategori', ['rig', 'alat_pendukung'])->index();
            $table->string('merek', 100);
            $table->string('model', 100)->nullable();
            $table->year('tahun_perolehan')->nullable();
            $table->string('no_seri', 50)->unique();
            $table->enum('status', ['aktif', 'standby', 'bermasalah'])->default('aktif')->index();
            $table->text('keterangan')->nullable();
            $table->string('foto', 255)->nullable();
            $table->foreignId('lokasi_id')->nullable()->constrained('lokasi')->nullOnDelete()->cascadeOnUpdate();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('alat');
    }
};
