<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rssite_pendaftarans', function (Blueprint $table) {
            $table->id();
            $table->string('kode', 30)->unique();
            $table->string('nama_lengkap', 200);
            $table->string('nik', 20)->nullable();
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
            $table->string('nomor_telepon', 20);
            $table->text('alamat');
            $table->enum('status_pasien', ['Pasien Baru', 'Pasien Lama'])->default('Pasien Baru');
            $table->enum('jenis_layanan', ['Umum', 'BPJS', 'Asuransi Lain', 'TNI/POLRI'])->default('Umum');
            $table->string('nama_asuransi', 100)->nullable();
            $table->string('poli_tujuan', 150);
            $table->text('catatan')->nullable();
            $table->string('foto_ktp', 500)->nullable();
            $table->string('foto_bpjs', 500)->nullable();
            $table->enum('status', ['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'])->default('Menunggu');
            $table->text('catatan_admin')->nullable();
            $table->boolean('sudah_konfirmasi_wa')->default(false);
            $table->timestamp('dikonfirmasi_at')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rssite_pendaftarans');
    }
};
