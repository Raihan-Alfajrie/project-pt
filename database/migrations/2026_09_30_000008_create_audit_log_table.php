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
        Schema::create('audit_log', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete()->cascadeOnUpdate();
            $table->string('username_snapshot', 50);
            $table->string('email_snapshot', 150);
            $table->enum('jabatan_snapshot', ['administrator', 'pekerja']);
            $table->enum('aksi', ['login', 'logout', 'tambah', 'edit', 'hapus'])->index();
            $table->enum('modul', ['alat', 'lokasi', 'jadwal', 'user', 'sistem'])->index();
            $table->text('keterangan');
            $table->dateTime('waktu')->index();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('audit_log');
    }
};
