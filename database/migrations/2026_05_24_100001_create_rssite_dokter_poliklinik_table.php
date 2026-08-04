<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rssite_dokter_poliklinik', function (Blueprint $table) {
            $table->id();
            $table->foreignId('dokter_id')->constrained('rssite_dokters')->onDelete('cascade');
            $table->foreignId('poliklinik_id')->constrained('rssite_polikliniks')->onDelete('cascade');
            $table->integer('urutan')->default(0);
            $table->unique(['dokter_id', 'poliklinik_id']);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rssite_dokter_poliklinik');
    }
};
