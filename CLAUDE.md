# Task: Website Profil RS TK IV Sintang — Laravel 10 + MySQL + Admin Panel

Bangun website profil rumah sakit yang dinamis untuk **RS TK IV Sintang** (Kalimantan Barat)
menggunakan Laravel 10, MySQL, Blade templating, dan Tailwind CSS.

Referensi layout: https://pkutegal.com/

---

## Environment & Constraints

- **Framework**: Laravel 10 (bukan Laravel 8 — ini project baru terpisah dari SIMRS)
- **PHP**: 8.1+
- **Database**: MySQL, prefix tabel `rssite_`
- **CSS**: Tailwind CSS 3 via CDN Play (tidak perlu build step)
- **JS**: Alpine.js via CDN (untuk interaktivitas ringan)
- **Upload**: Laravel Storage (`storage/app/public`), symlink via `php artisan storage:link`
- **Auth**: Laravel built-in `auth` untuk admin, guard `web`
- **Image fallback**: Jika tidak ada gambar yang diupload, tampilkan gradient CSS placeholder — jangan error

---

## Design System

```
Primary   : #2D6A4F  (Hijau TNI)
Dark      : #1B4332
Light     : #52B788
Gold      : #C9A84C
Gold-light: #E9C46A
White     : #FFFFFF
Gray-50   : #F8F9FA
Gray-800  : #343A40

Font Heading : Playfair Display (Google Fonts)
Font Body    : Source Sans 3 (Google Fonts)
```

Load dari Google Fonts di layout master:
```html
<link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600;700&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">
```

---

## Struktur Project

```
rstsintang/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── HomeController.php          ← halaman publik
│   │   │   └── Admin/
│   │   │       ├── DashboardController.php
│   │   │       ├── HeroController.php
│   │   │       ├── LayananController.php
│   │   │       ├── DokterController.php
│   │   │       ├── BeritaController.php
│   │   │       ├── KamarController.php
│   │   │       └── SiteSettingController.php
│   │   └── Requests/
│   │       ├── HeroRequest.php
│   │       ├── LayananRequest.php
│   │       ├── DokterRequest.php
│   │       ├── BeritaRequest.php
│   │       └── KamarRequest.php
│   └── Models/
│       ├── Hero.php
│       ├── Layanan.php
│       ├── Dokter.php
│       ├── Berita.php
│       ├── Kamar.php
│       └── SiteSetting.php
├── database/
│   ├── migrations/
│   └── seeders/
│       └── DatabaseSeeder.php              ← seed data awal RS
├── resources/
│   └── views/
│       ├── layouts/
│       │   ├── app.blade.php               ← layout publik
│       │   └── admin.blade.php             ← layout admin
│       ├── home.blade.php                  ← halaman utama publik
│       └── admin/
│           ├── dashboard.blade.php
│           ├── hero/
│           │   ├── index.blade.php
│           │   └── form.blade.php
│           ├── layanan/
│           │   ├── index.blade.php
│           │   └── form.blade.php
│           ├── dokter/
│           │   ├── index.blade.php
│           │   └── form.blade.php
│           ├── berita/
│           │   ├── index.blade.php
│           │   └── form.blade.php
│           ├── kamar/
│           │   ├── index.blade.php
│           │   └── form.blade.php
│           └── setting/
│               └── index.blade.php
└── routes/
    └── web.php
```

---

## Database Migrations

Buat semua migration dengan prefix `rssite_`.

### rssite_heroes
```sql
id              bigint PK auto
judul           varchar(255)
sub_judul       text nullable
gambar          varchar(255) nullable   -- path storage
tombol_teks     varchar(100) nullable
tombol_url      varchar(255) nullable
urutan          int default 0
aktif           tinyint(1) default 1
timestamps
```

### rssite_layanans
```sql
id              bigint PK auto
nama            varchar(255)
deskripsi       text
ikon            varchar(100)            -- nama class Font Awesome, e.g. "fa-heartbeat"
gambar          varchar(255) nullable
urutan          int default 0
aktif           tinyint(1) default 1
timestamps
```

### rssite_dokters
```sql
id              bigint PK auto
nama            varchar(255)
spesialisasi    varchar(255)
foto            varchar(255) nullable
jadwal          json                    -- [{hari: "Senin", jam: "08:00-12:00"}, ...]
bio             text nullable
aktif           tinyint(1) default 1
urutan          int default 0
timestamps
```

### rssite_beritas
```sql
id              bigint PK auto
judul           varchar(255)
slug            varchar(255) unique
kategori        enum('Berita','Pengumuman','Kegiatan') default 'Berita'
konten          longtext
gambar          varchar(255) nullable
penulis         varchar(100) default 'Admin RS'
views           int default 0
aktif           tinyint(1) default 1
timestamps
```

### rssite_kamars
```sql
id              bigint PK auto
nama            varchar(100)            -- VIP, Kelas I, Kelas II, Kelas III
deskripsi       text
fasilitas       json                    -- ["AC", "TV", "Kamar mandi dalam", ...]
gambar          varchar(255) nullable
badge           varchar(50) nullable    -- "Premium", "BPJS", dll
urutan          int default 0
aktif           tinyint(1) default 1
timestamps
```

### rssite_site_settings
```sql
id              bigint PK auto
key             varchar(100) unique
value           text nullable
timestamps
```
Setting keys yang harus ada (di-seed):
`nama_rs`, `tagline`, `alamat`, `telepon`, `email`, `jam_igd`, `jam_rajal`,
`jam_apotek`, `facebook_url`, `instagram_url`, `youtube_url`,
`sambutan_direktur_nama`, `sambutan_direktur_jabatan`,
`sambutan_direktur_foto`, `sambutan_direktur_teks`,
`stats_tahun_berdiri`, `stats_tenaga_medis`, `stats_kapasitas_tt`, `stats_pasien_pertahun`,
`logo`, `favicon`, `meta_description`

---

## Models

Buat semua model di `app/Models/` dengan:
- `$table` dengan prefix `rssite_`
- `$fillable` semua kolom
- Cast: kolom `json` → `array`, kolom `aktif` → `boolean`
- Model `Berita`: tambah accessor untuk generate `slug` otomatis dari `judul` jika belum ada (pakai `Str::slug`)
- Model `SiteSetting`: tambah static method `get($key, $default = null)` yang query by key

```php
// SiteSetting::get('nama_rs', 'RS TK IV Sintang')
public static function get(string $key, $default = null)
{
    return static::where('key', $key)->value('value') ?? $default;
}
```

---

## Routes (routes/web.php)

```php
// === PUBLIC ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/berita/{slug}', [HomeController::class, 'beritaDetail'])->name('berita.detail');
// increment views saat berita dibuka

// === AUTH ===
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// === ADMIN (middleware auth) ===
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('hero', HeroController::class);
    Route::post('hero/{hero}/toggle', [HeroController::class, 'toggle'])->name('hero.toggle');
    Route::post('hero/reorder', [HeroController::class, 'reorder'])->name('hero.reorder');

    Route::resource('layanan', LayananController::class);
    Route::post('layanan/{layanan}/toggle', [LayananController::class, 'toggle'])->name('layanan.toggle');

    Route::resource('dokter', DokterController::class);
    Route::post('dokter/{dokter}/toggle', [DokterController::class, 'toggle'])->name('dokter.toggle');

    Route::resource('berita', BeritaController::class);
    Route::post('berita/{berita}/toggle', [BeritaController::class, 'toggle'])->name('berita.toggle');

    Route::resource('kamar', KamarController::class);
    Route::post('kamar/{kamar}/toggle', [KamarController::class, 'toggle'])->name('kamar.toggle');

    Route::get('setting', [SiteSettingController::class, 'index'])->name('setting.index');
    Route::post('setting', [SiteSettingController::class, 'update'])->name('setting.update');
});
```

---

## Controllers

### HomeController
- `index()`: load semua data aktif (heroes, layanans, dokters, kamars, 3 berita terbaru, semua site_settings) → pass ke `home.blade.php`
- `beritaDetail($slug)`: find berita by slug, increment `views`, render detail view

### Admin Controllers (semua mengextend Controller)
Setiap resource controller (Hero, Layanan, Dokter, Berita, Kamar) harus punya:
- `index()` — list semua data dengan pagination 10, ada search
- `create()` — form tambah
- `store(Request $r)` — validasi + simpan + handle upload gambar
- `edit($id)` — form edit dengan data existing
- `update(Request $r, $id)` — validasi + update + handle upload gambar baru (hapus gambar lama jika diganti)
- `destroy($id)` — soft delete + hapus gambar dari storage
- `toggle($id)` — flip kolom `aktif` (1→0, 0→1), return redirect back dengan flash message

### Upload Gambar — Pattern yang Harus Dipakai di Semua Controller
```php
private function handleImageUpload($request, $field, $oldPath = null): ?string
{
    if ($request->hasFile($field)) {
        if ($oldPath) Storage::disk('public')->delete($oldPath);
        return $request->file($field)->store('rssite/images', 'public');
    }
    return $oldPath; // tidak ada upload baru, pakai yang lama
}
```

### DashboardController
Tampilkan stats:
- Total hero aktif/non-aktif
- Total layanan
- Total dokter aktif
- Total berita + total views
- Total kamar
- 5 berita terbaru

### SiteSettingController
- `index()`: load semua setting dari DB → group by fungsi → tampilkan form
- `update()`: loop semua input, upsert tiap key. Untuk key yang berupa gambar (logo, favicon, sambutan_direktur_foto), handle upload.

---

## Seeder (DatabaseSeeder.php)

Seed data awal yang realistis:

**SiteSettings**: Isi semua key yang didefinisikan di atas dengan nilai default RS TK IV Sintang.

**Heroes** (3 slide):
1. "Pelayanan Kesehatan Terbaik untuk Masyarakat Sintang" / "Dengan tenaga medis profesional dan fasilitas modern" / tombol "Daftar Sekarang"
2. "Fasilitas Medis Modern & Terakreditasi" / "Didukung peralatan medis terkini untuk pelayanan optimal" / tombol "Lihat Fasilitas"
3. "IGD 24 Jam Siap Melayani Anda" / "Tim dokter jaga dan perawat berpengalaman selalu siaga" / tombol "Hubungi Kami"

**Layanans** (8 layanan): IGD, Rawat Jalan, Rawat Inap, Laboratorium, Radiologi, Farmasi, Bedah, Persalinan

**Dokters** (6 dokter) dengan jadwal JSON lengkap

**Beritas** (3 berita) dengan konten HTML minimal

**Kamars** (4 kelas): VIP, Kelas I, Kelas II, Kelas III dengan fasilitas JSON

**User admin**: email `admin@rstsintang.com`, password `password123` (hash bcrypt)

---

## Views

### layouts/app.blade.php (Layout Publik)

Struktur lengkap:
```
<html>
  <head> Google Fonts + Tailwind CDN + Alpine.js CDN + Font Awesome CDN + custom CSS </head>
  <body>
    @include('partials.topbar')
    @include('partials.navbar')
    @yield('content')
    @include('partials.footer')
    <button id="back-to-top"> ↑ </button>
    <script> vanilla JS untuk: sticky nav, back-to-top, scroll reveal </script>
  </body>
</html>
```

**Topbar**: background `#1B4332`, flex between. Kiri: ikon telepon + nomor, ikon email + email, ikon jam + "IGD 24 Jam". Kanan: ikon sosmed (link ke URL dari setting).

**Navbar**: sticky, background putih, shadow saat scroll. Logo kiri (nama RS + tagline kecil). Menu tengah: Beranda, Layanan, Dokter, Fasilitas, Berita, Kontak. Kanan: tombol "Daftar Online" gold. Hamburger mobile dengan Alpine.js `x-data x-show`.

**Footer**: 4 kolom grid. Background `#1B4332` teks putih. Kolom 1: logo + deskripsi + alamat + sosmed. Kolom 2-4: menu link. Bottom bar: copyright.

### layouts/admin.blade.php (Layout Admin)

Sidebar layout modern:
- **Sidebar kiri** (fixed, 250px): Logo RS, menu navigasi dengan ikon FA, highlight active route
- **Main area** (fluid): topbar (breadcrumb kiri, nama user + logout kanan), konten `@yield('content')`
- Warna sidebar: `#1B4332` teks putih, active item background gold `#C9A84C`
- Responsive: sidebar collapse di mobile dengan toggle

Menu sidebar:
```
Dashboard          fa-tachometer-alt
── Konten
   Hero / Slider   fa-images
   Layanan         fa-hand-holding-medical
   Dokter          fa-user-md
   Berita          fa-newspaper
   Kamar           fa-bed
── Pengaturan
   Pengaturan Situs fa-cog
```

### home.blade.php

Render semua section publik. Setiap section menggunakan data dari controller. Detail setiap section:

**HERO SECTION**
- Auto-play carousel dari data `$heroes`
- Jika `gambar` ada → `<img>` dari storage, jika tidak → gradient CSS (`background: linear-gradient(135deg, #1B4332, #2D6A4F, #52B788)`)
- Overlay gelap 50% di atas gambar/gradient
- Teks judul `font-playfair text-5xl font-bold text-white` + sub_judul
- Tombol CTA jika ada
- Dots navigasi di bawah
- Alpine.js `x-data="{active:0}"` untuk state carousel, `setInterval` auto-play 5 detik

**STATS BAR**
- Background gradient hijau, 4 kolom
- Angka dari site_settings: `stats_tahun_berdiri`, `stats_tenaga_medis`, dll
- Data attribute `data-target` untuk counter JS (IntersectionObserver + requestAnimationFrame)

**LAYANAN**
- Grid `grid-cols-2 md:grid-cols-4` dari `$layanans`
- Card: ikon FA `text-4xl text-green-600`, nama bold, deskripsi text-sm gray
- Hover: `hover:shadow-xl hover:-translate-y-1 transition-all`
- Gambar opsional di atas ikon jika ada

**JADWAL DOKTER**
- Slider horizontal dari `$dokters` (Alpine.js carousel)
- Card dokter: avatar (foto atau inisial dengan background gradient), nama, badge spesialisasi (gold), jadwal JSON di-loop
- Navigasi panah kiri-kanan

**KAMAR RAWAT INAP**
- Grid `grid-cols-1 md:grid-cols-2 lg:grid-cols-4`
- Card: gambar/gradient atas, badge, nama kelas, daftar fasilitas (dari JSON), tombol detail

**SAMBUTAN DIREKTUR**
- Layout 2 kolom: kiri teks sambutan, kanan foto/avatar direktur
- Data dari site_settings: `sambutan_direktur_nama`, `sambutan_direktur_jabatan`, `sambutan_direktur_foto`, `sambutan_direktur_teks`

**BERITA TERBARU**
- Grid 3 kolom dari `$beritas` (3 terbaru)
- Card: thumbnail/gradient, badge kategori warna-warni, judul, tanggal, cuplikan 100 karakter, link detail
- Tombol "Lihat Semua Berita"

**CTA SECTION**
- Full width, background gradient hijau
- Judul besar + 2 tombol

**KONTAK & PETA**
- 2 kolom: info kontak kiri (dari settings), Google Maps embed kanan
- Map embed: `<iframe src="https://maps.google.com/maps?q=RS+TK+IV+Sintang&output=embed">`

### Admin Views

#### admin/dashboard.blade.php
- Grid 5 stat card di atas (hero, layanan, dokter, berita, kamar) dengan ikon dan angka
- Tabel 5 berita terbaru di bawah (judul, kategori, views, status, tanggal)

#### admin/hero/index.blade.php
- Tombol "Tambah Hero" di kanan atas
- Tabel: preview gambar kecil (50x50), judul, urutan, status toggle switch, tombol Edit/Hapus
- Toggle status via form POST ke `admin.hero.toggle`
- Sortable urutan (opsional, bisa manual input)

#### admin/hero/form.blade.php (create & edit)
- Input: Judul*, Sub Judul, Upload Gambar (preview jika sudah ada), Teks Tombol, URL Tombol, Urutan, Status (checkbox)
- Preview gambar saat pilih file baru (JS FileReader)
- Submit tombol "Simpan"

#### admin/layanan/index.blade.php & form.blade.php
- Index: tabel nama, ikon (tampilkan `<i class="fa ...">` live), urutan, status, aksi
- Form: Nama*, Deskripsi*, Input Ikon (text field dengan preview `<i>`), Upload Gambar, Urutan, Status

#### admin/dokter/index.blade.php & form.blade.php
- Index: foto/avatar, nama, spesialisasi, status, aksi
- Form: Nama*, Spesialisasi*, Upload Foto, Bio, Status, **Jadwal (dinamis)**:
  - Jadwal: tombol "Tambah Jadwal" yang append row baru dengan Alpine.js
  - Setiap row: dropdown Hari (Senin–Minggu), input Jam (e.g. "08:00–12:00"), tombol hapus row
  - Data jadwal dikirim sebagai `jadwal[0][hari]`, `jadwal[0][jam]`, dst → controller encode ke JSON

#### admin/berita/index.blade.php & form.blade.php
- Index: thumbnail, judul, kategori badge, views, status, tanggal, aksi
- Form: Judul* (+ auto-generate slug preview di bawahnya via JS), Kategori (select), Konten* (**gunakan Textarea biasa dengan minimal styling — tidak perlu rich text editor pihak ketiga**), Upload Gambar, Penulis, Status

#### admin/kamar/index.blade.php & form.blade.php
- Index: gambar, nama, badge, urutan, status, aksi
- Form: Nama*, Deskripsi*, Upload Gambar, Badge, Urutan, Status, **Fasilitas (dinamis)**:
  - Tombol "Tambah Fasilitas" → append input text baru (Alpine.js)
  - Submit sebagai `fasilitas[]` → controller encode ke JSON

#### admin/setting/index.blade.php
- Form panjang dikelompokkan dalam card/section:
  1. **Identitas RS**: nama_rs, tagline, alamat, telepon, email
  2. **Jam Operasional**: jam_igd, jam_rajal, jam_apotek
  3. **Media Sosial**: facebook_url, instagram_url, youtube_url
  4. **Sambutan Direktur**: nama, jabatan, foto (upload + preview), teks (textarea)
  5. **Statistik**: tahun_berdiri, tenaga_medis, kapasitas_tt, pasien_pertahun
  6. **Branding**: logo (upload + preview), favicon (upload + preview), meta_description
- Satu tombol "Simpan Semua Pengaturan" di bawah

---

## Flash Messages

Semua controller redirect dengan flash:
```php
return redirect()->route('admin.hero.index')->with('success', 'Hero berhasil disimpan.');
return redirect()->back()->with('error', 'Gagal menyimpan data.');
```

Di `layouts/admin.blade.php`, tampilkan flash di atas konten:
```blade
@if(session('success'))
  <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
    {{ session('success') }}
  </div>
@endif
@if(session('error'))
  <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
    {{ session('error') }}
  </div>
@endif
```

---

## JavaScript (Vanilla, inline di layouts)

### layouts/app.blade.php — script block di bawah body:

```javascript
// 1. Sticky navbar shadow
window.addEventListener('scroll', () => {
    document.getElementById('navbar').classList.toggle('shadow-lg', window.scrollY > 50);
});

// 2. Back to top
const btn = document.getElementById('back-to-top');
window.addEventListener('scroll', () => btn.classList.toggle('hidden', window.scrollY < 300));
btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

// 3. Scroll reveal
const observer = new IntersectionObserver((entries) => {
    entries.forEach(e => {
        if (e.isIntersecting) {
            e.target.classList.add('visible');
            observer.unobserve(e.target);
        }
    });
}, { threshold: 0.1 });
document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

// 4. Counter animasi
const counterObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            const el = entry.target;
            const target = parseInt(el.dataset.target);
            let current = 0;
            const step = Math.ceil(target / 60);
            const timer = setInterval(() => {
                current = Math.min(current + step, target);
                el.textContent = current.toLocaleString('id-ID') + (el.dataset.suffix || '');
                if (current >= target) clearInterval(timer);
            }, 30);
            counterObserver.unobserve(el);
        }
    });
});
document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));
```

CSS untuk reveal:
```css
.reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
.reveal.visible { opacity: 1; transform: translateY(0); }
```

### layouts/admin.blade.php — sidebar toggle mobile:
```javascript
document.getElementById('sidebar-toggle').addEventListener('click', () => {
    document.getElementById('sidebar').classList.toggle('-translate-x-full');
});
```

### admin/dokter/form.blade.php — jadwal dinamis (Alpine.js):
```html
<div x-data="{ jadwals: {{ old('jadwal') ? json_encode(old('jadwal')) : ($dokter->jadwal ?? '[]') }} }">
  <template x-for="(j, i) in jadwals" :key="i">
    <div class="flex gap-2 mb-2">
      <select :name="`jadwal[${i}][hari]`" ...>
        <option>Senin</option>...<option>Minggu</option>
      </select>
      <input :name="`jadwal[${i}][jam]`" :value="j.jam" placeholder="08:00-12:00" ...>
      <button type="button" @click="jadwals.splice(i,1)">✕</button>
    </div>
  </template>
  <button type="button" @click="jadwals.push({hari:'Senin',jam:''})">+ Tambah Jadwal</button>
</div>
```

---

## .env Additions

Tambahkan instruksi konfigurasi .env di README:
```env
APP_NAME="RS TK IV Sintang"
APP_URL=http://localhost/rstsintang/public

DB_DATABASE=rstsintang_web
DB_USERNAME=root
DB_PASSWORD=

FILESYSTEM_DISK=public
```

---

## README.md

Buat file README.md dengan instruksi setup lengkap:
```
1. git clone / copy project
2. composer install
3. cp .env.example .env → edit DB
4. php artisan key:generate
5. php artisan migrate
6. php artisan db:seed
7. php artisan storage:link
8. Buka http://localhost/rstsintang/public
9. Admin: http://localhost/rstsintang/public/admin
   Email: admin@rstsintang.com | Password: password123
```

---

## Checklist Akhir

Setelah semua file selesai dibuat, lakukan verifikasi:

- [ ] Semua migration dapat dijalankan tanpa error
- [ ] Seeder mengisi semua tabel dengan data awal yang realistis
- [ ] Login admin berfungsi (redirect ke dashboard)
- [ ] Semua CRUD resource berfungsi (Hero, Layanan, Dokter, Berita, Kamar)
- [ ] Upload gambar tersimpan ke `storage/app/public/rssite/images/`
- [ ] Gambar tampil via `Storage::url()` di public
- [ ] Jika gambar null → fallback gradient CSS (tidak error)
- [ ] Toggle aktif/non-aktif berfungsi
- [ ] Jadwal dokter dinamis (add/remove row) berfungsi
- [ ] Fasilitas kamar dinamis (add/remove input) berfungsi
- [ ] Pengaturan situs tersimpan dan langsung terlihat di halaman publik
- [ ] Halaman publik menampilkan semua section dengan data dari DB
- [ ] Responsive: navbar collapse di mobile, grid menyesuaikan
- [ ] Flash messages tampil di admin
- [ ] Semua route bernama dan prefix benar
- [ ] Middleware `auth` melindungi semua route `/admin/*`
- [ ] README.md ada dan lengkap
```
