<?php

use Illuminate\Database\Migrations\Migration;
use App\Models\SiteSetting;

return new class extends Migration {
    public function up(): void
    {
        $settings = [
            ['key' => 'profil_visi',         'value' => 'Menjadi Rumah Sakit pilihan utama masyarakat Landak dan Kalimantan Barat yang profesional, modern, dan terpercaya.'],
            ['key' => 'profil_misi',         'value' => json_encode([
                'Memberikan pelayanan kesehatan secara adil dan ihsan kepada seluruh masyarakat',
                'Menyediakan layanan kesehatan yang modern dan profesional berbasis teknologi',
                'Meningkatkan kompetensi tenaga medis dan non-medis secara berkelanjutan',
                'Menjadi pusat rujukan kesehatan terpercaya di wilayah Landak dan sekitarnya',
                'Meningkatkan kualitas sarana, prasarana, dan tata kelola rumah sakit',
            ])],
            ['key' => 'profil_motto',        'value' => 'Melayani dengan Sepenuh Hati'],
            ['key' => 'profil_rs_foto',      'value' => null],
            ['key' => 'profil_rs_sejarah',   'value' => '<p>RSUD Landak berdiri sebagai rumah sakit umum daerah milik Pemerintah Kabupaten Landak. Seiring perkembangan waktu, RSUD Landak membuka layanannya untuk seluruh masyarakat umum Kabupaten Landak dan sekitarnya.</p><p>Berlokasi strategis di Jl. Raya Ngabang, Kabupaten Landak, Kalimantan Barat, rumah sakit ini terus berkembang dengan menambah fasilitas dan tenaga medis spesialis untuk memenuhi kebutuhan masyarakat.</p>'],
            ['key' => 'profil_rs_legalitas', 'value' => '<ul><li>Izin Operasional: No. xxx/DPMPTSP/RS/2022</li><li>Akreditasi KARS: Lulus Tingkat Perdana</li><li>Nomor RS: 6109xxx</li></ul>'],
            ['key' => 'profil_rs_nilai',     'value' => json_encode([
                ['ikon' => 'fa-heart',         'judul' => 'Integritas',   'teks' => 'Jujur dan bertanggung jawab dalam setiap tindakan pelayanan'],
                ['ikon' => 'fa-hands-helping', 'judul' => 'Profesional',  'teks' => 'Melayani dengan kompetensi dan standar medis terbaik'],
                ['ikon' => 'fa-shield-alt',    'judul' => 'Keselamatan',  'teks' => 'Mengutamakan keselamatan pasien di atas segalanya'],
                ['ikon' => 'fa-users',         'judul' => 'Kebersamaan',  'teks' => 'Bekerja sebagai satu tim yang solid dan harmonis'],
            ])],
            ['key' => 'direktur_nama',       'value' => 'dr. [Nama Direktur], M.Kes'],
            ['key' => 'direktur_jabatan',    'value' => 'Direktur RSUD Landak'],
            ['key' => 'direktur_nrp',        'value' => 'NIP: xxxxxxxxxx'],
            ['key' => 'direktur_foto',       'value' => null],
            ['key' => 'direktur_sambutan',   'value' => '<p>Assalamualaikum warahmatullahi wabarakatuh.</p><p>Puji syukur kami panjatkan ke hadirat Allah SWT atas rahmat dan karunia-Nya sehingga RSUD Landak dapat terus hadir melayani masyarakat Kabupaten Landak dan sekitarnya.</p><p>Sebagai rumah sakit umum daerah milik Pemerintah Kabupaten Landak, kami berkomitmen untuk memberikan pelayanan kesehatan yang profesional, cepat, dan terjangkau kepada seluruh lapisan masyarakat tanpa terkecuali.</p><p>Wassalamualaikum warahmatullahi wabarakatuh.</p>'],
            ['key' => 'direktur_pendidikan', 'value' => json_encode([
                'S1 Kedokteran — Universitas [xxx]',
                'Spesialis [Bidang] — Universitas [xxx]',
                'Magister Manajemen Rumah Sakit — Universitas [xxx]',
            ])],
            ['key' => 'direktur_riwayat',    'value' => json_encode([
                'Direktur RSUD Landak (20xx–sekarang)',
                'Kepala Puskesmas [xxx] (20xx–20xx)',
                'Kepala Bidang Pelayanan Medis RSUD Landak (20xx–20xx)',
            ])],
        ];

        foreach ($settings as $s) {
            SiteSetting::firstOrCreate(['key' => $s['key']], ['value' => $s['value']]);
        }
    }

    public function down(): void
    {
        $keys = ['profil_visi', 'profil_misi', 'profil_motto', 'profil_rs_foto',
                 'profil_rs_sejarah', 'profil_rs_legalitas', 'profil_rs_nilai',
                 'direktur_nama', 'direktur_jabatan', 'direktur_nrp', 'direktur_foto',
                 'direktur_sambutan', 'direktur_pendidikan', 'direktur_riwayat'];
        SiteSetting::whereIn('key', $keys)->delete();
    }
};
