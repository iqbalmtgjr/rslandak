<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rssite_leaflet_kategoris', function (Blueprint $table) {
            $table->id();
            $table->enum('tipe', ['Leaflet', 'Poster'])->default('Leaflet');
            $table->string('nama', 150);
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rssite_leaflet_kategoris');
    }
};
