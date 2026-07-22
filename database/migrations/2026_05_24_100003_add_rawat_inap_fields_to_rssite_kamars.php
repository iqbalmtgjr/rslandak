<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('rssite_kamars', function (Blueprint $table) {
            $table->unsignedBigInteger('tarif')->nullable()->after('badge');
            $table->string('foto_1', 500)->nullable()->after('tarif');
            $table->string('foto_2', 500)->nullable()->after('foto_1');
            $table->string('foto_3', 500)->nullable()->after('foto_2');
            $table->string('foto_4', 500)->nullable()->after('foto_3');
            $table->string('foto_5', 500)->nullable()->after('foto_4');
        });
    }

    public function down(): void
    {
        Schema::table('rssite_kamars', function (Blueprint $table) {
            $table->dropColumn(['tarif','foto_1','foto_2','foto_3','foto_4','foto_5']);
        });
    }
};
