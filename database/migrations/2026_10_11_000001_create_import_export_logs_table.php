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
        Schema::create('import_export_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->enum('jenis', ['import', 'export'])->index();
            $table->enum('modul', ['alat', 'lokasi', 'jadwal', 'audit_log'])->index();
            $table->string('nama_file', 255);
            $table->unsignedInteger('baris_berhasil')->default(0);
            $table->unsignedInteger('baris_total')->default(0);
            $table->enum('status', ['sukses', 'sebagian', 'gagal'])->index();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('import_export_logs');
    }
};
