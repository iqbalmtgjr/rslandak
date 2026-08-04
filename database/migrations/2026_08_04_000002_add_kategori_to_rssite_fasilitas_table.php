<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('rssite_fasilitas', function (Blueprint $table) {
            $table->string('kategori')->default('klinik')->after('gambar');
        });

        // Set default based on untuk_difabel column
        DB::table('rssite_fasilitas')
            ->where('untuk_difabel', 1)
            ->update(['kategori' => 'difabel']);
    }

    public function down(): void
    {
        Schema::table('rssite_fasilitas', function (Blueprint $table) {
            $table->dropColumn('kategori');
        });
    }
};
