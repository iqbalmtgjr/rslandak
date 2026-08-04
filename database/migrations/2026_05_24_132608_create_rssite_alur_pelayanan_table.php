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
        Schema::create('rssite_alur_pelayanan', function (Blueprint $table) {
            $table->id();
            $table->string('judul', 200);
            $table->string('gambar', 500)->nullable(); // nullable for seeder; required at form validation
            $table->text('keterangan')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('aktif')->default(true);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rssite_alur_pelayanan');
    }
};
