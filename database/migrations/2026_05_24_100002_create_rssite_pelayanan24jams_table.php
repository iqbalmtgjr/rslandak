<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rssite_pelayanan24jams', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->text('deskripsi');
            $table->string('foto', 500)->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rssite_pelayanan24jams');
    }
};
