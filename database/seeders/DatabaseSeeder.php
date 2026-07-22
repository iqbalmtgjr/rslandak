<?php

namespace Database\Seeders;

use App\Models\Hero;
use App\Models\Layanan;
use App\Models\Dokter;
use App\Models\Berita;
use App\Models\Kamar;
use App\Models\SiteSetting;
use App\Models\User;
use App\Models\DownloadKategori;
use App\Models\LeafletKategori;
use App\Models\LeafletItem;
use App\Models\Poliklinik;
use App\Models\Pelayanan24Jam;
use App\Models\AlurPelayanan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Admin user
        User::updateOrCreate(
            ['email' => 'admin@rsudlandak.com'],
            ['name' => 'Admin RS', 'username' => 'admin', 'password' => Hash::make('password123')]
        );

        // Site Settings
        $settings = [
            'nama_rs' => 'RSUD Landak',
            'tagline' => 'Melayani dengan Sepenuh Hati',
            'alamat' => 'Jl. Raya Ngabang, Kec. Ngabang, Kabupaten Landak, Kalimantan Barat 79357',
            'telepon' => '(0563) 2022170',
            'email' => 'rsudlandak@gmail.com',
            'jam_igd' => '24 Jam',
            'jam_rajal' => 'Senin - Sabtu, 08:00 - 14:00',
            'jam_apotek' => 'Senin - Sabtu, 08:00 - 20:00',
            'facebook_url' => 'https://facebook.com',
            'instagram_url' => 'https://instagram.com',
            'youtube_url' => 'https://youtube.com',
            'sambutan_direktur_nama' => 'dr. Andi Prasetyo, Sp.PD',
            'sambutan_direktur_jabatan' => 'Direktur RSUD Landak',
            'sambutan_direktur_foto' => '',
            'sambutan_direktur_teks' => 'Selamat datang di RSUD Landak. Kami berkomitmen untuk memberikan pelayanan kesehatan terbaik kepada seluruh masyarakat Landak dan sekitarnya. Dengan tenaga medis profesional dan fasilitas modern, kami siap melayani Anda dengan sepenuh hati. Kepercayaan Anda adalah motivasi kami untuk terus meningkatkan kualitas pelayanan.',
            'stats_tahun_berdiri' => '1970',
            'stats_tenaga_medis' => '150',
            'stats_kapasitas_tt' => '120',
            'stats_pasien_pertahun' => '25000',
            'logo' => '',
            'favicon' => '',
            'meta_description' => 'RSUD Landak - Rumah Sakit Umum Daerah milik Pemerintah Kabupaten Landak yang melayani masyarakat Landak, Kalimantan Barat dengan pelayanan kesehatan profesional dan fasilitas modern.',
            'wa_pendaftaran'   => '6283830331205',
        ];

        foreach ($settings as $key => $value) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // Heroes
        $heroes = [
            ['judul' => 'Pelayanan Kesehatan Terbaik untuk Masyarakat Landak', 'sub_judul' => 'Dengan tenaga medis profesional dan fasilitas modern, kami siap memberikan pelayanan terbaik untuk kesehatan Anda dan keluarga.', 'tombol_teks' => 'Daftar Sekarang', 'tombol_url' => '#kontak', 'urutan' => 1],
            ['judul' => 'Fasilitas Medis Modern & Terakreditasi', 'sub_judul' => 'Didukung peralatan medis terkini dan standar pelayanan terakreditasi untuk pelayanan kesehatan yang optimal.', 'tombol_teks' => 'Lihat Fasilitas', 'tombol_url' => '#layanan', 'urutan' => 2],
            ['judul' => 'IGD 24 Jam Siap Melayani Anda', 'sub_judul' => 'Tim dokter jaga dan perawat berpengalaman selalu siaga 24 jam untuk menangani kondisi darurat medis Anda.', 'tombol_teks' => 'Hubungi Kami', 'tombol_url' => '#kontak', 'urutan' => 3],
        ];

        foreach ($heroes as $hero) {
            Hero::create(array_merge($hero, ['aktif' => 1]));
        }

        // Layanans
        $layanans = [
            ['nama' => 'Instalasi Gawat Darurat', 'deskripsi' => 'Pelayanan gawat darurat 24 jam dengan tenaga medis terlatih dan peralatan lengkap untuk penanganan kondisi darurat.', 'ikon' => 'fa-ambulance', 'urutan' => 1],
            ['nama' => 'Rawat Jalan', 'deskripsi' => 'Pelayanan poliklinik rawat jalan dengan berbagai spesialisasi dokter berpengalaman untuk konsultasi dan pemeriksaan kesehatan.', 'ikon' => 'fa-stethoscope', 'urutan' => 2],
            ['nama' => 'Rawat Inap', 'deskripsi' => 'Fasilitas rawat inap nyaman dengan berbagai kelas kamar, dilengkapi perawatan profesional selama 24 jam.', 'ikon' => 'fa-bed', 'urutan' => 3],
            ['nama' => 'Laboratorium', 'deskripsi' => 'Pemeriksaan laboratorium lengkap dengan peralatan modern untuk diagnosis yang akurat dan cepat.', 'ikon' => 'fa-flask', 'urutan' => 4],
            ['nama' => 'Radiologi', 'deskripsi' => 'Pelayanan radiologi meliputi X-Ray, USG, dan pemeriksaan penunjang lainnya dengan teknologi terkini.', 'ikon' => 'fa-x-ray', 'urutan' => 5],
            ['nama' => 'Farmasi', 'deskripsi' => 'Apotek rumah sakit yang menyediakan obat-obatan lengkap dengan apoteker profesional yang siap membantu.', 'ikon' => 'fa-pills', 'urutan' => 6],
            ['nama' => 'Bedah', 'deskripsi' => 'Kamar operasi modern dengan tim dokter bedah berpengalaman untuk berbagai tindakan operasi elektif maupun darurat.', 'ikon' => 'fa-scalpel', 'urutan' => 7],
            ['nama' => 'Persalinan', 'deskripsi' => 'Pelayanan persalinan normal dan caesar dengan bidan dan dokter spesialis kebidanan yang berpengalaman.', 'ikon' => 'fa-baby', 'urutan' => 8],
        ];

        foreach ($layanans as $layanan) {
            Layanan::create(array_merge($layanan, ['aktif' => 1]));
        }

        // Dokters
        $dokters = [
            [
                'nama' => 'dr. Budi Santoso, Sp.PD',
                'spesialisasi' => 'Spesialis Penyakit Dalam',
                'jadwal' => [
                    ['hari' => 'Senin', 'jam' => '08:00-12:00'],
                    ['hari' => 'Rabu', 'jam' => '08:00-12:00'],
                    ['hari' => 'Jumat', 'jam' => '08:00-12:00'],
                ],
                'bio' => 'Dokter spesialis penyakit dalam dengan pengalaman lebih dari 15 tahun.',
                'urutan' => 1,
            ],
            [
                'nama' => 'dr. Sari Dewi, Sp.OG',
                'spesialisasi' => 'Spesialis Kebidanan & Kandungan',
                'jadwal' => [
                    ['hari' => 'Selasa', 'jam' => '09:00-13:00'],
                    ['hari' => 'Kamis', 'jam' => '09:00-13:00'],
                    ['hari' => 'Sabtu', 'jam' => '09:00-12:00'],
                ],
                'bio' => 'Dokter spesialis kebidanan dengan keahlian dalam persalinan normal dan caesar.',
                'urutan' => 2,
            ],
            [
                'nama' => 'dr. Ahmad Fauzi, Sp.B',
                'spesialisasi' => 'Spesialis Bedah Umum',
                'jadwal' => [
                    ['hari' => 'Senin', 'jam' => '13:00-16:00'],
                    ['hari' => 'Rabu', 'jam' => '13:00-16:00'],
                    ['hari' => 'Jumat', 'jam' => '13:00-16:00'],
                ],
                'bio' => 'Dokter bedah umum berpengalaman dalam berbagai tindakan operasi.',
                'urutan' => 3,
            ],
            [
                'nama' => 'dr. Rina Kusuma, Sp.A',
                'spesialisasi' => 'Spesialis Anak',
                'jadwal' => [
                    ['hari' => 'Selasa', 'jam' => '08:00-12:00'],
                    ['hari' => 'Kamis', 'jam' => '08:00-12:00'],
                    ['hari' => 'Sabtu', 'jam' => '08:00-11:00'],
                ],
                'bio' => 'Dokter spesialis anak yang berdedikasi untuk kesehatan si kecil.',
                'urutan' => 4,
            ],
            [
                'nama' => 'dr. Hendra Wijaya, Sp.Rad',
                'spesialisasi' => 'Spesialis Radiologi',
                'jadwal' => [
                    ['hari' => 'Senin', 'jam' => '08:00-14:00'],
                    ['hari' => 'Selasa', 'jam' => '08:00-14:00'],
                    ['hari' => 'Rabu', 'jam' => '08:00-14:00'],
                ],
                'bio' => 'Dokter radiologi dengan keahlian interpretasi berbagai modalitas pencitraan medis.',
                'urutan' => 5,
            ],
            [
                'nama' => 'dr. Maya Putri, Sp.THT',
                'spesialisasi' => 'Spesialis THT',
                'jadwal' => [
                    ['hari' => 'Rabu', 'jam' => '10:00-13:00'],
                    ['hari' => 'Jumat', 'jam' => '10:00-13:00'],
                ],
                'bio' => 'Dokter spesialis THT dengan pengalaman menangani berbagai gangguan telinga, hidung, dan tenggorokan.',
                'urutan' => 6,
            ],
        ];

        foreach ($dokters as $dokter) {
            Dokter::create(array_merge($dokter, ['aktif' => 1]));
        }

        // Beritas
        $beritas = [
            [
                'judul'    => 'RSUD Landak Raih Akreditasi Paripurna dari KARS',
                'slug'     => 'rsud-landak-raih-akreditasi-paripurna',
                'kategori' => 'Berita',
                'konten'   => '<p>RSUD Landak berhasil meraih akreditasi Paripurna dari Komisi Akreditasi Rumah Sakit (KARS). Pencapaian ini merupakan bukti komitmen rumah sakit dalam memberikan pelayanan kesehatan yang berkualitas dan aman bagi masyarakat.</p><p>Direktur RSUD Landak, dr. Andi Prasetyo, Sp.PD, menyampaikan rasa syukur atas pencapaian ini. "Akreditasi Paripurna ini merupakan hasil kerja keras seluruh tim yang tidak kenal lelah dalam meningkatkan mutu pelayanan," ujarnya.</p><p>Proses akreditasi melibatkan penilaian terhadap berbagai aspek pelayanan, mulai dari keselamatan pasien, kompetensi tenaga medis, hingga kelengkapan fasilitas pendukung.</p>',
                'penulis'  => 'Humas RSUD Landak',
                'views'    => 245,
            ],
            [
                'judul'    => 'Pembukaan Poli Jantung Baru untuk Masyarakat Landak',
                'slug'     => 'pembukaan-poli-jantung-baru-landak',
                'kategori' => 'Pengumuman',
                'konten'   => '<p>RSUD Landak dengan bangga mengumumkan pembukaan Poliklinik Jantung yang baru. Fasilitas ini hadir untuk memenuhi kebutuhan masyarakat Landak akan pelayanan kesehatan jantung yang komprehensif.</p><p>Poli Jantung akan dilayani oleh dokter spesialis jantung dan pembuluh darah yang berpengalaman. Pelayanan meliputi konsultasi, EKG, ekokardiografi, dan treadmill test.</p><p>Masyarakat dapat mendaftarkan diri melalui loket pendaftaran atau melalui hotline rumah sakit.</p>',
                'penulis'  => 'Admin RS',
                'views'    => 189,
            ],
            [
                'judul'    => 'Bakti Sosial Pemeriksaan Kesehatan Gratis di Desa Terpencil',
                'slug'     => 'bakti-sosial-pemeriksaan-kesehatan-gratis',
                'kategori' => 'Kegiatan',
                'konten'   => '<p>Tim dokter dan tenaga medis RSUD Landak mengadakan kegiatan bakti sosial berupa pemeriksaan kesehatan gratis di beberapa desa terpencil di Kabupaten Landak. Kegiatan ini merupakan wujud kepedulian rumah sakit terhadap kesehatan masyarakat yang tinggal di daerah sulit dijangkau.</p><p>Sebanyak lebih dari 500 warga mendapat pelayanan kesehatan meliputi pemeriksaan umum, cek tekanan darah, kadar gula darah, dan pemberian obat gratis.</p><p>Kegiatan ini akan terus dilaksanakan secara rutin setiap tiga bulan sekali sebagai program CSR rumah sakit.</p>',
                'penulis'  => 'Tim Humas',
                'views'    => 312,
            ],
            [
                'judul'    => 'RSUD Landak Gelar Bakti Sosial Kesehatan untuk Warga Perbatasan',
                'slug'     => 'bakti-sosial-kesehatan-warga-perbatasan',
                'kategori' => 'Kegiatan',
                'konten'   => '<p>RSUD Landak menyelenggarakan bakti sosial kesehatan gratis bagi masyarakat di wilayah perbatasan Kabupaten Landak. Kegiatan ini melibatkan puluhan tenaga medis dari berbagai spesialisasi.</p><p>Dalam kegiatan ini, masyarakat mendapat layanan pemeriksaan kesehatan umum, pengobatan gratis, serta konsultasi gizi secara cuma-cuma. Total lebih dari 300 warga terlayani dalam satu hari.</p>',
                'penulis'  => 'Humas RSUD Landak',
                'views'    => 178,
            ],
            [
                'judul'    => 'Pembukaan Poli Spesialis Saraf — RSUD Landak Hadirkan Dokter SpS',
                'slug'     => 'pembukaan-poli-spesialis-saraf',
                'kategori' => 'Berita',
                'konten'   => '<p>RSUD Landak resmi membuka Poli Spesialis Saraf dengan menghadirkan dokter spesialis saraf berpengalaman. Layanan ini hadir untuk memenuhi kebutuhan masyarakat Landak yang selama ini harus merujuk ke kota lain untuk penanganan gangguan saraf.</p><p>Pelayanan Poli Saraf meliputi konsultasi, penanganan stroke, epilepsi, nyeri kepala, dan gangguan saraf tepi. Jadwal praktek dua kali seminggu setiap Selasa dan Kamis pukul 09.00–13.00.</p>',
                'penulis'  => 'Humas RSUD Landak',
                'views'    => 203,
            ],
            [
                'judul'    => 'Pengumuman: Perubahan Jam Pelayanan Poli Rawat Jalan Selama Ramadan',
                'slug'     => 'perubahan-jam-pelayanan-ramadan',
                'kategori' => 'Pengumuman',
                'konten'   => '<p>Dalam rangka menyambut bulan suci Ramadan, RSUD Landak menyesuaikan jam operasional Poli Rawat Jalan. Perubahan berlaku mulai 1 Ramadan 1446 H.</p><ul><li>Poli Rawat Jalan: 07.30 – 12.00 WIB</li><li>IGD: Tetap 24 Jam</li><li>Apotek: 07.00 – 20.00 WIB</li></ul><p>Untuk informasi lebih lanjut, hubungi hotline RS di (0563) 2022170.</p>',
                'penulis'  => 'Admin RSUD Landak',
                'views'    => 156,
            ],
            [
                'judul'    => 'Pelatihan Resusitasi Jantung Paru untuk Staf Medis RSUD Landak',
                'slug'     => 'pelatihan-resusitasi-jantung-paru-staf-medis',
                'kategori' => 'Kegiatan',
                'konten'   => '<p>RSUD Landak menyelenggarakan pelatihan Basic Life Support (BLS) dan Resusitasi Jantung Paru (RJP) untuk seluruh staf medis dan paramedis. Pelatihan ini bertujuan meningkatkan kesiapsiagaan tenaga kesehatan dalam menangani kondisi darurat.</p><p>Sebanyak 80 peserta mengikuti pelatihan intensif selama dua hari yang dipandu oleh instruktur bersertifikat nasional dari Rumah Sakit Kariadi Semarang.</p>',
                'penulis'  => 'Komite Keperawatan RS',
                'views'    => 134,
            ],
            [
                'judul'    => 'RSUD Landak Terima Kunjungan Studi Banding dari RS Daerah Melawi',
                'slug'     => 'kunjungan-studi-banding-rs-melawi',
                'kategori' => 'Kegiatan',
                'konten'   => '<p>RSUD Landak menerima kunjungan studi banding dari tim manajemen RSUD Melawi. Kunjungan ini bertujuan untuk berbagi pengalaman dalam pengelolaan rumah sakit di wilayah Kalimantan Barat.</p><p>Direktur RSUD Landak menyambut hangat rombongan tamu dan memaparkan sistem manajemen mutu yang telah diterapkan, termasuk program akreditasi KARS dan sistem informasi rumah sakit terintegrasi.</p>',
                'penulis'  => 'Sekretariat RS',
                'views'    => 89,
            ],
            [
                'judul'    => 'Peringatan HUT RI ke-80: RSUD Landak Gelar Lomba Olahraga',
                'slug'     => 'hut-tni-ke-79-lomba-olahraga',
                'kategori' => 'Kegiatan',
                'konten'   => '<p>Dalam rangka memperingati HUT RI ke-80, RSUD Landak mengadakan serangkaian kegiatan olahraga yang melibatkan seluruh personel dan karyawan rumah sakit. Kegiatan berlangsung meriah dan penuh semangat kebersamaan.</p><p>Lomba yang diselenggarakan meliputi pertandingan futsal, bulu tangkis, dan senam bersama. Kegiatan diakhiri dengan ramah tamah dan penyerahan hadiah kepada para pemenang.</p>',
                'penulis'  => 'Panitia HUT RI RSUD Landak',
                'views'    => 167,
            ],
            [
                'judul'    => 'Pemberitahuan: Jadwal Dokter Spesialis Bulan Juni 2025',
                'slug'     => 'jadwal-dokter-spesialis-juni-2025',
                'kategori' => 'Pengumuman',
                'konten'   => '<p>Berikut adalah jadwal praktik dokter spesialis RSUD Landak untuk bulan Juni 2025. Jadwal dapat berubah sewaktu-waktu, harap konfirmasi ke bagian pendaftaran sebelum berkunjung.</p><h2>Poli Penyakit Dalam</h2><p>dr. Budi Santoso, Sp.PD — Senin, Rabu, Jumat: 08.00–12.00</p><h2>Poli Kebidanan & Kandungan</h2><p>dr. Sari Dewi, Sp.OG — Selasa, Kamis: 09.00–13.00 | Sabtu: 09.00–12.00</p><h2>Poli Bedah</h2><p>dr. Ahmad Fauzi, Sp.B — Senin, Rabu, Jumat: 13.00–16.00</p>',
                'penulis'  => 'Bagian Pendaftaran RS',
                'views'    => 421,
            ],
            [
                'judul'    => 'Penandatanganan MoU RSUD Landak dengan Dinkes Kabupaten Landak',
                'slug'     => 'mou-rsud-landak-dengan-dinkes-landak',
                'kategori' => 'Berita',
                'konten'   => '<p>RSUD Landak dan Dinas Kesehatan Kabupaten Landak resmi menandatangani Memorandum of Understanding (MoU) kerja sama pelayanan kesehatan. MoU ini mencakup kerja sama dalam program promotif, preventif, dan kuratif untuk masyarakat Kabupaten Landak.</p><p>Penandatanganan dilakukan oleh Direktur RSUD Landak dan Kepala Dinas Kesehatan Kabupaten Landak, disaksikan oleh Bupati Landak dan Sekretaris Daerah Kabupaten Landak.</p>',
                'penulis'  => 'Humas RSUD Landak',
                'views'    => 298,
            ],
            [
                'judul'    => 'RSUD Landak Luncurkan Aplikasi Pendaftaran Online',
                'slug'     => 'peluncuran-aplikasi-pendaftaran-online',
                'kategori' => 'Berita',
                'konten'   => '<p>RSUD Landak resmi meluncurkan aplikasi pendaftaran online untuk memudahkan masyarakat mendaftar layanan kesehatan tanpa harus mengantri. Aplikasi dapat diunduh melalui Google Play Store dan App Store.</p><p>Dengan aplikasi ini, pasien dapat memilih dokter, jadwal, dan jenis layanan yang dibutuhkan secara real-time. Fitur notifikasi juga membantu pasien mendapat pengingat jadwal kunjungan.</p><p>Kepala Bagian IT RSUD Landak menyatakan aplikasi ini merupakan bagian dari transformasi digital rumah sakit menuju pelayanan yang lebih modern dan efisien.</p>',
                'penulis'  => 'Tim IT RSUD Landak',
                'views'    => 512,
            ],
        ];

        foreach ($beritas as $berita) {
            Berita::firstOrCreate(['slug' => $berita['slug']], array_merge($berita, ['aktif' => 1]));
        }

        // Kamars
        $kamars = [
            [
                'nama' => 'VIP',
                'deskripsi' => 'Kamar VIP dengan fasilitas premium dan privasi terjaga. Ideal untuk pasien yang menginginkan kenyamanan maksimal selama perawatan.',
                'fasilitas' => ['AC', 'TV LED 43 inch', 'Kulkas', 'Sofa tamu', 'Kamar mandi dalam', 'Air panas', 'WiFi', 'Meja makan', 'Lemari pakaian', 'Satu tempat tidur pendamping'],
                'badge' => 'Premium',
                'urutan' => 1,
            ],
            [
                'nama' => 'Kelas I',
                'deskripsi' => 'Kamar Kelas I dengan fasilitas lengkap dan nyaman. Tersedia untuk 1-2 pasien per kamar dengan privasi yang terjaga.',
                'fasilitas' => ['AC', 'TV', 'Kamar mandi dalam', 'WiFi', 'Lemari pakaian', 'Satu tempat tidur pendamping'],
                'badge' => 'Comfort',
                'urutan' => 2,
            ],
            [
                'nama' => 'Kelas II',
                'deskripsi' => 'Kamar Kelas II dengan fasilitas standar yang nyaman. Tersedia untuk 2-3 pasien per kamar.',
                'fasilitas' => ['AC', 'TV bersama', 'Kamar mandi dalam', 'Loker pasien'],
                'badge' => 'Standard',
                'urutan' => 3,
            ],
            [
                'nama' => 'Kelas III',
                'deskripsi' => 'Kamar Kelas III dengan fasilitas dasar yang memadai. Dapat digunakan oleh peserta BPJS Kesehatan.',
                'fasilitas' => ['Kipas angin', 'Kamar mandi bersama', 'Loker pasien'],
                'badge' => 'BPJS',
                'urutan' => 4,
            ],
        ];

        foreach ($kamars as $kamar) {
            Kamar::create(array_merge($kamar, ['aktif' => 1]));
        }
        // Download Kategoris
        $downloadKategoris = [
            ['nama' => 'Formulir',        'ikon' => 'fa-clipboard',     'warna' => '#2563EB', 'urutan' => 1],
            ['nama' => 'Regulasi & SK',   'ikon' => 'fa-file-contract', 'warna' => '#D97706', 'urutan' => 2],
            ['nama' => 'Panduan Pasien',  'ikon' => 'fa-book-medical',  'warna' => '#3B82F6', 'urutan' => 3],
            ['nama' => 'Laporan Tahunan', 'ikon' => 'fa-chart-bar',     'warna' => '#8B5CF6', 'urutan' => 4],
        ];

        foreach ($downloadKategoris as $kd) {
            DownloadKategori::firstOrCreate(
                ['nama' => $kd['nama']],
                array_merge($kd, [
                    'slug'  => Str::slug($kd['nama']),
                    'aktif' => true,
                ])
            );
        }

        // Leaflet & Poster
        $leafletData = [
            [
                'tipe'   => 'Leaflet',
                'nama'   => 'PENYAKIT DALAM',
                'urutan' => 1,
                'items'  => [
                    ['nama' => 'Mengenal Diabetes Melitus', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_1/view?usp=sharing', 'deskripsi' => 'Panduan lengkap mengenal diabetes'],
                    ['nama' => 'Hipertensi dan Cara Mengatasinya', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_2/view?usp=sharing', 'deskripsi' => null],
                ],
            ],
            [
                'tipe'   => 'Leaflet',
                'nama'   => 'ANAK',
                'urutan' => 2,
                'items'  => [
                    ['nama' => 'Imunisasi Dasar Lengkap', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_3/view?usp=sharing', 'deskripsi' => null],
                    ['nama' => 'Stunting pada Balita', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_4/view?usp=sharing', 'deskripsi' => null],
                ],
            ],
            [
                'tipe'   => 'Leaflet',
                'nama'   => 'HAK & KEWAJIBAN PASIEN',
                'urutan' => 3,
                'items'  => [
                    ['nama' => 'Hak dan Kewajiban Pasien RS', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_5/view?usp=sharing', 'deskripsi' => null],
                ],
            ],
            [
                'tipe'   => 'Poster',
                'nama'   => 'CUCI TANGAN',
                'urutan' => 1,
                'items'  => [
                    ['nama' => 'Poster 6 Langkah Cuci Tangan', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_6/view?usp=sharing', 'deskripsi' => 'WHO Hand Hygiene'],
                ],
            ],
            [
                'tipe'   => 'Poster',
                'nama'   => 'KESELAMATAN PASIEN',
                'urutan' => 2,
                'items'  => [
                    ['nama' => 'Poster TBAK (Tulis, Baca, Konfirmasi)', 'url_gdrive' => 'https://drive.google.com/file/d/CONTOH_ID_7/view?usp=sharing', 'deskripsi' => null],
                ],
            ],
        ];

        foreach ($leafletData as $data) {
            $kat = LeafletKategori::firstOrCreate(
                ['nama' => $data['nama'], 'tipe' => $data['tipe']],
                ['urutan' => $data['urutan'], 'aktif' => true]
            );
            foreach ($data['items'] as $i => $itemData) {
                LeafletItem::firstOrCreate(
                    ['kategori_id' => $kat->id, 'nama' => $itemData['nama']],
                    ['url_gdrive' => $itemData['url_gdrive'], 'deskripsi' => $itemData['deskripsi'],
                     'urutan' => $i + 1, 'aktif' => true]
                );
            }
        }

        // Polikliniks
        $polikliniks = [
            ['nama' => 'Klinik Umum',                      'ikon' => 'fa-stethoscope', 'tipe_ikon' => 'fa', 'urutan' => 1],
            ['nama' => 'Poliklinik Gigi',                  'ikon' => 'fa-tooth',       'tipe_ikon' => 'fa', 'urutan' => 2],
            ['nama' => 'Poliklinik Anak',                  'ikon' => 'fa-baby',        'tipe_ikon' => 'fa', 'urutan' => 3],
            ['nama' => 'Poliklinik Kandungan & Kebidanan', 'ikon' => 'fa-female',      'tipe_ikon' => 'fa', 'urutan' => 4],
            ['nama' => 'Poliklinik Penyakit Dalam',        'ikon' => 'fa-heartbeat',   'tipe_ikon' => 'fa', 'urutan' => 5],
            ['nama' => 'Poliklinik Bedah Umum',            'ikon' => 'fa-scalpel',     'tipe_ikon' => 'fa', 'urutan' => 6],
            ['nama' => 'Poliklinik Mata',                  'ikon' => 'fa-eye',         'tipe_ikon' => 'fa', 'urutan' => 7],
            ['nama' => 'Poliklinik THT',                   'ikon' => 'fa-deaf',        'tipe_ikon' => 'fa', 'urutan' => 8],
            ['nama' => 'Poliklinik Saraf',                 'ikon' => 'fa-brain',       'tipe_ikon' => 'fa', 'urutan' => 9],
            ['nama' => 'Poliklinik Kulit & Kelamin',       'ikon' => 'fa-allergies',   'tipe_ikon' => 'fa', 'urutan' => 10],
            ['nama' => 'Poliklinik Paru',                  'ikon' => 'fa-lungs',       'tipe_ikon' => 'fa', 'urutan' => 11],
            ['nama' => 'Poliklinik Jantung',               'ikon' => 'fa-heart',       'tipe_ikon' => 'fa', 'urutan' => 12],
            ['nama' => 'Poliklinik Ortopedi',              'ikon' => 'fa-bone',        'tipe_ikon' => 'fa', 'urutan' => 13],
            ['nama' => 'Radiologi',                        'ikon' => 'fa-x-ray',       'tipe_ikon' => 'fa', 'urutan' => 14],
            ['nama' => 'Rehabilitasi Medik',               'ikon' => 'fa-wheelchair',  'tipe_ikon' => 'fa', 'urutan' => 15],
        ];
        foreach ($polikliniks as $p) {
            Poliklinik::firstOrCreate(
                ['nama' => $p['nama']],
                array_merge($p, ['slug' => Str::slug($p['nama']), 'aktif' => true])
            );
        }

        // Pelayanan 24 Jam
        $pelayanan24Jams = [
            ['nama' => 'INSTALASI GAWAT DARURAT (IGD)', 'deskripsi' => 'Instalasi gawat darurat RSUD Landak merupakan bagian dari unit pelayanan 24 jam yang memiliki sumber daya manusia profesional yang terlatih dan bersertifikat. Didukung oleh dokter jaga bersertifikat dan perawat terampil siaga penuh.', 'urutan' => 1],
            ['nama' => 'FARMASI', 'deskripsi' => 'Instalasi farmasi RSUD Landak buka 24 jam melayani kebutuhan obat pasien rawat inap dan rawat jalan. Dikelola oleh tenaga apoteker, asisten apoteker, dan administrasi yang profesional.', 'urutan' => 2],
            ['nama' => 'LABORATORIUM', 'deskripsi' => 'Laboratorium RSUD Landak dilengkapi alat-alat auto analyzer untuk pemeriksaan hematologi dan kimia klinik. Layanan laboratorium buka 24 jam untuk mendukung diagnosis yang cepat dan akurat.', 'urutan' => 3],
            ['nama' => 'RADIOLOGI', 'deskripsi' => 'Instalasi Radiologi RSUD Landak melayani rontgen, USG, dan berbagai pemeriksaan pencitraan medis lainnya. Didukung tenaga radiografer berpengalaman dan peralatan modern.', 'urutan' => 4],
            ['nama' => 'BANK DARAH', 'deskripsi' => 'Bank darah RSUD Landak siap memenuhi kebutuhan darah untuk transfusi pasien rawat inap maupun pasien yang dirujuk. Bekerja sama dengan PMI setempat untuk ketersediaan stok darah.', 'urutan' => 5],
            ['nama' => 'AMBULANS', 'deskripsi' => 'Pelayanan ambulans RSUD Landak beroperasi 24 jam untuk antar jemput pasien. Dilengkapi peralatan medis standar dan dikemudikan oleh tenaga supir yang handal dan berpengalaman.', 'urutan' => 6],
        ];
        foreach ($pelayanan24Jams as $p) {
            Pelayanan24Jam::firstOrCreate(['nama' => $p['nama']], array_merge($p, ['aktif' => true]));
        }

        // Update kamars dengan tarif
        $kamarTarifs = [
            ['nama' => 'VIP',      'tarif' => 1020000],
            ['nama' => 'Kelas I',  'tarif' => 450000],
            ['nama' => 'Kelas II', 'tarif' => 225000],
            ['nama' => 'Kelas III','tarif' => 75000],
        ];
        foreach ($kamarTarifs as $kt) {
            \App\Models\Kamar::where('nama', $kt['nama'])->update(['tarif' => $kt['tarif']]);
        }

        // Alur Pelayanan — non-aktif karena gambar wajib diupload manual oleh admin
        $alurData = [
            ['judul' => 'Alur Pendaftaran Pasien Lama',    'keterangan' => 'Pasien yang sudah pernah berobat ke RSUD Landak sebelumnya.', 'urutan' => 10],
            ['judul' => 'Alur Pendaftaran Pasien Baru',    'keterangan' => 'Pasien yang pertama kali berobat ke RSUD Landak.',             'urutan' => 20],
            ['judul' => 'Alur Pelayanan Pasien BPJS',   'keterangan' => 'Khusus pasien peserta BPJS Kesehatan.', 'urutan' => 30],
        ];
        foreach ($alurData as $a) {
            AlurPelayanan::firstOrCreate(
                ['judul' => $a['judul']],
                array_merge($a, ['aktif' => false]) // non-aktif sampai admin upload gambar
            );
        }
    }
}
