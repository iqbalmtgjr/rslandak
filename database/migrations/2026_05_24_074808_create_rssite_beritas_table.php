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
        Schema::create('rssite_beritas', function (Blueprint $table) {
            $table->id();
            $table->string('judul');
            $table->string('slug')->unique();
            $table->enum('kategori', ['Berita', 'Pengumuman', 'Kegiatan'])->default('Berita');
            $table->longText('konten');
            $table->string('gambar')->nullable();
            $table->string('penulis', 100)->default('Admin RS');
            $table->integer('views')->default(0);
            $table->tinyInteger('aktif')->default(1);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rssite_beritas');
    }
};
