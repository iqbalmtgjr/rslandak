<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rssite_leaflet_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('kategori_id')
                  ->constrained('rssite_leaflet_kategoris')
                  ->onDelete('cascade');
            $table->string('nama', 255);
            $table->text('url_gdrive');
            $table->string('deskripsi', 255)->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rssite_leaflet_items');
    }
};
