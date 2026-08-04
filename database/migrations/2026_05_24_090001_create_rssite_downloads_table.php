<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rssite_downloads', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                  ->constrained('rssite_download_kategoris')
                  ->onDelete('restrict');
            $table->string('judul', 255);
            $table->string('deskripsi', 500)->nullable();
            $table->string('nama_file', 255);
            $table->string('path_file', 500);
            $table->string('tipe_file', 20);
            $table->string('mime_type', 100);
            $table->unsignedBigInteger('ukuran_file');
            $table->unsignedInteger('jumlah_download')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rssite_downloads');
    }
};
