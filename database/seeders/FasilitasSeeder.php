<?php

namespace Database\Seeders;

use App\Models\Fasilitas;
use App\Models\SiteSetting;
use Illuminate\Database\Seeder;

/**
 * Seeder revisi Juli 2026 (Fasilitas, Fasilitas Difabel, kanal SIPPN/LAPOR).
 *
 * Aman dijalankan di production: semua pakai firstOrCreate, tidak ada yang
 * ditimpa. Jalankan dengan: php artisan db:seed --class=FasilitasSeeder
 */
class FasilitasSeeder extends Seeder
{
    public function run(): void
    {
        $fasilitasData = [
            ['nama' => 'INSTALASI GAWAT DARURAT', 'deskripsi' => '<p>Ruang IGD RSUD Landak dilengkapi ruang triase, ruang resusitasi, dan ruang observasi. Siaga 24 jam dengan dokter jaga dan perawat bersertifikat kegawatdaruratan.</p>', 'urutan' => 10],
            ['nama' => 'INSTALASI BEDAH SENTRAL', 'deskripsi' => '<p>Kamar operasi dengan standar sterilitas, dilengkapi meja operasi, lampu operasi, mesin anestesi, dan monitor pasien untuk tindakan bedah elektif maupun darurat.</p>', 'urutan' => 20],
            ['nama' => 'RADIOLOGI', 'deskripsi' => '<p>Melayani pemeriksaan rontgen digital, USG, dan pencitraan medis lainnya. Didukung radiografer berpengalaman dan pembacaan hasil oleh dokter spesialis radiologi.</p>', 'urutan' => 30],
            ['nama' => 'LABORATORIUM KLINIK', 'deskripsi' => '<p>Dilengkapi alat auto analyzer untuk pemeriksaan hematologi, kimia klinik, imunologi, dan mikrobiologi dasar dengan hasil yang cepat dan akurat.</p>', 'urutan' => 40],
            ['nama' => 'INSTALASI FARMASI', 'deskripsi' => '<p>Melayani kebutuhan obat pasien rawat inap dan rawat jalan selama 24 jam. Dikelola oleh apoteker dan tenaga teknis kefarmasian.</p>', 'urutan' => 50],
            ['nama' => 'AMBULANS', 'deskripsi' => '<p>Armada ambulans siaga 24 jam untuk antar jemput dan rujukan pasien, dilengkapi peralatan medis standar dan tenaga pendamping terlatih.</p>', 'urutan' => 60],
        ];
        foreach ($fasilitasData as $f) {
            Fasilitas::firstOrCreate(['nama' => $f['nama']], array_merge($f, ['aktif' => true, 'untuk_difabel' => false]));
        }

        $fasilitasDifabel = [
            ['nama' => 'RAMP / JALUR LANDAI', 'deskripsi' => '<p>Jalur landai dengan kemiringan aman tersedia di pintu masuk utama dan penghubung antar gedung, memudahkan akses pengguna kursi roda.</p>', 'urutan' => 10],
            ['nama' => 'KURSI RODA', 'deskripsi' => '<p>Kursi roda tersedia gratis di area pendaftaran dan IGD, dapat dipinjam oleh pasien maupun pengunjung yang membutuhkan.</p>', 'urutan' => 20],
            ['nama' => 'TOILET DIFABEL', 'deskripsi' => '<p>Toilet khusus dengan pegangan pengaman (handrail), pintu lebar, dan ruang gerak yang memadai untuk pengguna kursi roda.</p>', 'urutan' => 30],
            ['nama' => 'AREA PARKIR PRIORITAS', 'deskripsi' => '<p>Area parkir khusus penyandang disabilitas dan lansia yang ditempatkan paling dekat dengan pintu masuk rumah sakit.</p>', 'urutan' => 40],
            ['nama' => 'LOKET & ANTREAN PRIORITAS', 'deskripsi' => '<p>Layanan prioritas pada loket pendaftaran bagi penyandang disabilitas, lansia, dan ibu hamil tanpa perlu mengantre panjang.</p>', 'urutan' => 50],
        ];
        foreach ($fasilitasDifabel as $f) {
            Fasilitas::firstOrCreate(['nama' => $f['nama']], array_merge($f, ['aktif' => true, 'untuk_difabel' => true]));
        }

        foreach ([
            'sippn_url' => 'https://sippn.menpan.go.id',
            'lapor_url' => 'https://www.lapor.go.id',
        ] as $key => $value) {
            SiteSetting::firstOrCreate(['key' => $key], ['value' => $value]);
        }
    }
}
