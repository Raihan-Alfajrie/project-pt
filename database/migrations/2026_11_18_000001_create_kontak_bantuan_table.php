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
        Schema::create('kontak_bantuan', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 100);
            $table->string('email', 100);
            $table->string('no_hp', 30)->nullable();
            $table->string('instansi', 150)->nullable();
            $table->string('kategori', 50)->default('bantuan')->index();
            $table->string('perihal', 200);
            $table->text('pesan');
            $table->string('status', 30)->default('baru')->index();
            $table->text('catatan_admin')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('kontak_bantuan');
    }
};
