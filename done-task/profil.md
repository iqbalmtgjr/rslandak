# Task: Menu Profil RS — Sub Halaman Visi Misi, Profil RS, dan Direktur

Tambahkan menu **Profil RS** dengan 3 sub-menu halaman statis dinamis ke project website
RS TK IV Sintang yang sudah ada (lihat `CLAUDE.md` untuk konteks project).

Referensi tampilan: screenshot halaman Visi & Misi RSI PKU Muhammadiyah Tegal.

---

## Ringkasan Perubahan

| Area           | Yang Ditambahkan                                                  |
|----------------|-------------------------------------------------------------------|
| Database       | 3 kolom baru di `rssite_site_settings` (atau tabel baru)         |
| Routes         | 3 route publik + 3 route admin                                    |
| Controllers    | `ProfilController` (publik) + `Admin/ProfilController` (admin)   |
| Views publik   | `profil/visi-misi.blade.php`, `profil/profil-rs.blade.php`, `profil/direktur.blade.php` |
| Views admin    | `admin/profil/index.blade.php`                                   |
| Navbar         | Dropdown "Profil RS" dengan 3 sub-menu                           |
| Sidebar admin  | Entry baru "Profil RS" di group Konten                           |

---

## 1. Database

### Opsi: Tambah keys ke `rssite_site_settings`

Tidak perlu tabel baru. Cukup tambah keys berikut ke seeder (dan insert manual via migration baru jika project sudah jalan):

```php
// Visi & Misi
['key' => 'profil_visi',          'value' => 'Menjadi Rumah Sakit pilihan utama masyarakat Sintang dan Kalimantan Barat yang profesional, modern, dan terpercaya.'],
['key' => 'profil_misi',          'value' => json_encode([
    'Memberikan pelayanan kesehatan secara adil dan ihsan kepada seluruh masyarakat',
    'Menyediakan layanan kesehatan yang modern dan profesional berbasis teknologi',
    'Meningkatkan kompetensi tenaga medis dan non-medis secara berkelanjutan',
    'Menjadi pusat rujukan kesehatan terpercaya di wilayah Sintang dan sekitarnya',
    'Meningkatkan kualitas sarana, prasarana, dan tata kelola rumah sakit',
])],
['key' => 'profil_motto',         'value' => 'Melayani dengan Sepenuh Hati'],

// Profil RS
['key' => 'profil_rs_foto',       'value' => null],   // path gambar gedung RS
['key' => 'profil_rs_sejarah',    'value' => '<p>RS TK IV Sintang berdiri sejak tahun 1965 sebagai rumah sakit militer yang melayani prajurit TNI dan keluarganya. Seiring perkembangan waktu, RS TK IV Sintang membuka layanannya untuk seluruh masyarakat umum Kabupaten Sintang dan sekitarnya.</p><p>Berlokasi strategis di Jl. MT. Haryono No.89, Sintang, Kalimantan Barat, rumah sakit ini terus berkembang dengan menambah fasilitas dan tenaga medis spesialis untuk memenuhi kebutuhan masyarakat.</p>'],
['key' => 'profil_rs_legalitas',  'value' => '<ul><li>Izin Operasional: No. xxx/DPMPTSP/RS/2022</li><li>Akreditasi KARS: Lulus Tingkat Perdana</li><li>Nomor RS: 6109xxx</li></ul>'],
['key' => 'profil_rs_nilai',      'value' => json_encode([
    ['ikon' => 'fa-heart',          'judul' => 'Integritas',    'teks' => 'Jujur dan bertanggung jawab dalam setiap tindakan pelayanan'],
    ['ikon' => 'fa-hands-helping',  'judul' => 'Profesional',   'teks' => 'Melayani dengan kompetensi dan standar medis terbaik'],
    ['ikon' => 'fa-shield-alt',     'judul' => 'Keselamatan',   'teks' => 'Mengutamakan keselamatan pasien di atas segalanya'],
    ['ikon' => 'fa-users',          'judul' => 'Kebersamaan',   'teks' => 'Bekerja sebagai satu tim yang solid dan harmonis'],
])],

// Direktur
['key' => 'direktur_nama',        'value' => 'Kolonel CKM dr. [Nama Direktur], Sp.--'],
['key' => 'direktur_jabatan',     'value' => 'Kepala RS TK IV Sintang'],
['key' => 'direktur_nrp',         'value' => 'NRP: xxxxxxxxxx'],
['key' => 'direktur_foto',        'value' => null],
['key' => 'direktur_sambutan',    'value' => '<p>Assalamualaikum warahmatullahi wabarakatuh.</p><p>Puji syukur kami panjatkan ke hadirat Allah SWT atas rahmat dan karunia-Nya sehingga RS TK IV Sintang dapat terus hadir melayani masyarakat Kabupaten Sintang dan sekitarnya.</p><p>Sebagai rumah sakit yang berada di bawah naungan TNI Angkatan Darat, kami berkomitmen untuk memberikan pelayanan kesehatan yang profesional, cepat, dan terjangkau kepada seluruh lapisan masyarakat tanpa terkecuali.</p><p>Kami senantiasa berupaya meningkatkan kualitas sumber daya manusia, fasilitas, dan sistem manajemen rumah sakit agar dapat memberikan pelayanan terbaik. Kepercayaan masyarakat adalah amanah yang kami jaga dengan sepenuh hati.</p><p>Wassalamualaikum warahmatullahi wabarakatuh.</p>'],
['key' => 'direktur_pendidikan',  'value' => json_encode([
    'S1 Kedokteran — Universitas [xxx]',
    'Spesialis [Bidang] — Universitas [xxx]',
    'Pendidikan Militer Kesehatan — SSKAD',
])],
['key' => 'direktur_riwayat',     'value' => json_encode([
    'Kepala RS TK IV Sintang (20xx–sekarang)',
    'Kepala Puskesmas [xxx] (20xx–20xx)',
    'Dokter Batalyon [xxx] (20xx–20xx)',
])],
```

### Migration Baru (jika project sudah running)

Buat file migration: `2024_01_02_000000_seed_profil_settings.php`

```php
public function up(): void
{
    $settings = [
        // ... semua key di atas
    ];

    foreach ($settings as $s) {
        \App\Models\SiteSetting::firstOrCreate(['key' => $s['key']], ['value' => $s['value']]);
    }
}

public function down(): void
{
    $keys = ['profil_visi', 'profil_misi', 'profil_motto', 'profil_rs_foto',
             'profil_rs_sejarah', 'profil_rs_legalitas', 'profil_rs_nilai',
             'direktur_nama', 'direktur_jabatan', 'direktur_nrp', 'direktur_foto',
             'direktur_sambutan', 'direktur_pendidikan', 'direktur_riwayat'];
    \App\Models\SiteSetting::whereIn('key', $keys)->delete();
}
```

---

## 2. Routes (routes/web.php)

Tambahkan di bawah route publik yang sudah ada:

```php
// Profil RS — Publik
Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/visi-misi',  [ProfilController::class, 'visiMisi'])->name('visi-misi');
    Route::get('/profil-rs',  [ProfilController::class, 'profilRs'])->name('profil-rs');
    Route::get('/direktur',   [ProfilController::class, 'direktur'])->name('direktur');
});
```

Tambahkan di bawah route admin yang sudah ada:

```php
// Admin Profil
Route::get('profil',  [Admin\ProfilController::class, 'index'])->name('profil.index');
Route::post('profil', [Admin\ProfilController::class, 'update'])->name('profil.update');
```

---

## 3. Controllers

### app/Http/Controllers/ProfilController.php

```php
<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class ProfilController extends Controller
{
    private function settings(): array
    {
        // Load semua key profil sekaligus
        return SiteSetting::whereIn('key', [
            'profil_visi', 'profil_misi', 'profil_motto',
            'profil_rs_foto', 'profil_rs_sejarah', 'profil_rs_legalitas', 'profil_rs_nilai',
            'direktur_nama', 'direktur_jabatan', 'direktur_nrp', 'direktur_foto',
            'direktur_sambutan', 'direktur_pendidikan', 'direktur_riwayat',
            'nama_rs', 'alamat', 'telepon', 'email',
        ])->pluck('value', 'key')->toArray();
    }

    public function visiMisi()
    {
        $s = $this->settings();
        return view('profil.visi-misi', [
            'visi'  => $s['profil_visi'] ?? '',
            'misi'  => json_decode($s['profil_misi'] ?? '[]', true),
            'motto' => $s['profil_motto'] ?? '',
        ]);
    }

    public function profilRs()
    {
        $s = $this->settings();
        return view('profil.profil-rs', [
            'foto'      => $s['profil_rs_foto'] ?? null,
            'sejarah'   => $s['profil_rs_sejarah'] ?? '',
            'legalitas' => $s['profil_rs_legalitas'] ?? '',
            'nilai'     => json_decode($s['profil_rs_nilai'] ?? '[]', true),
            'nama_rs'   => $s['nama_rs'] ?? 'RS TK IV Sintang',
        ]);
    }

    public function direktur()
    {
        $s = $this->settings();
        return view('profil.direktur', [
            'nama'       => $s['direktur_nama'] ?? '',
            'jabatan'    => $s['direktur_jabatan'] ?? '',
            'nrp'        => $s['direktur_nrp'] ?? '',
            'foto'       => $s['direktur_foto'] ?? null,
            'sambutan'   => $s['direktur_sambutan'] ?? '',
            'pendidikan' => json_decode($s['direktur_pendidikan'] ?? '[]', true),
            'riwayat'    => json_decode($s['direktur_riwayat'] ?? '[]', true),
        ]);
    }
}
```

### app/Http/Controllers/Admin/ProfilController.php

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    private array $profilKeys = [
        'profil_visi', 'profil_motto',
        'profil_rs_sejarah', 'profil_rs_legalitas',
        'direktur_nama', 'direktur_jabatan', 'direktur_nrp',
        'direktur_sambutan',
    ];

    private array $jsonKeys = [
        'profil_misi', 'profil_rs_nilai', 'direktur_pendidikan', 'direktur_riwayat',
    ];

    private array $imageKeys = [
        'profil_rs_foto', 'direktur_foto',
    ];

    public function index()
    {
        $allKeys = array_merge($this->profilKeys, $this->jsonKeys, $this->imageKeys);
        $settings = SiteSetting::whereIn('key', $allKeys)->pluck('value', 'key');
        return view('admin.profil.index', compact('settings'));
    }

    public function update(Request $request)
    {
        // Simpan field teks biasa
        foreach ($this->profilKeys as $key) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
        }

        // Simpan field JSON (array dinamis dari form)
        // profil_misi: input name="profil_misi[]"
        if ($request->has('profil_misi')) {
            $misi = array_filter($request->input('profil_misi', []));
            SiteSetting::updateOrCreate(['key' => 'profil_misi'], ['value' => json_encode(array_values($misi))]);
        }

        // profil_rs_nilai: input name="nilai[0][ikon]", nilai[0][judul], nilai[0][teks]
        if ($request->has('nilai')) {
            $nilai = array_filter($request->input('nilai', []), fn($n) => !empty($n['judul']));
            SiteSetting::updateOrCreate(['key' => 'profil_rs_nilai'], ['value' => json_encode(array_values($nilai))]);
        }

        // direktur_pendidikan: input name="direktur_pendidikan[]"
        if ($request->has('direktur_pendidikan')) {
            $pend = array_filter($request->input('direktur_pendidikan', []));
            SiteSetting::updateOrCreate(['key' => 'direktur_pendidikan'], ['value' => json_encode(array_values($pend))]);
        }

        // direktur_riwayat: input name="direktur_riwayat[]"
        if ($request->has('direktur_riwayat')) {
            $riw = array_filter($request->input('direktur_riwayat', []));
            SiteSetting::updateOrCreate(['key' => 'direktur_riwayat'], ['value' => json_encode(array_values($riw))]);
        }

        // Upload gambar
        foreach ($this->imageKeys as $key) {
            if ($request->hasFile($key)) {
                $old = SiteSetting::where('key', $key)->value('value');
                if ($old) Storage::disk('public')->delete($old);
                $path = $request->file($key)->store('rssite/profil', 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
            }
        }

        return redirect()->route('admin.profil.index')->with('success', 'Data profil berhasil disimpan.');
    }
}
```

---

## 4. Views Publik

Semua view publik mengextend `layouts.app` dan menggunakan class Tailwind.
Tambahkan partial `@include('partials.page-header', ['judul' => '...'])` di atas konten — buat partial ini.

### partials/page-header.blade.php

Banner tipis di bawah navbar, mirip referensi screenshot:

```blade
<section class="page-header py-10 text-center"
  style="background: linear-gradient(135deg, #1B4332 0%, #2D6A4F 60%, #52B788 100%);">
  <div class="container mx-auto px-4">
    <h1 class="font-playfair text-3xl font-bold text-white">{{ $judul }}</h1>
    {{-- Breadcrumb --}}
    <nav class="mt-2 text-sm text-green-200">
      <a href="{{ route('home') }}" class="hover:text-white">Beranda</a>
      <span class="mx-2">/</span>
      @isset($parent)
        <span>{{ $parent }}</span>
        <span class="mx-2">/</span>
      @endisset
      <span class="text-white font-medium">{{ $judul }}</span>
    </nav>
  </div>
</section>
```

---

### resources/views/profil/visi-misi.blade.php

```
@extends('layouts.app')
@section('title', 'Visi & Misi — RS TK IV Sintang')
@section('content')

[Page Header: "Visi & Misi", parent: "Profil RS"]

<section class="py-16 bg-white">
  <div class="container mx-auto px-4 max-w-5xl">

    Layout 2 kolom (lg:grid-cols-2 gap-12):

    KIRI — Foto gedung RS:
    - Jika $foto ada: <img src="{{ Storage::url($foto) }}" ...>
    - Jika tidak: div gradient hijau dengan ornamen SVG (palang medis atau bintang TNI)
    - Tambahkan elemen dekoratif kotak hijau solid di pojok kiri atas (seperti referensi screenshot)
    - rounded-lg shadow-xl overflow-hidden

    KANAN — Konten Visi Misi:
    - Border kiri 4px gold (border-l-4 border-yellow-500 pl-6) di judul RS
    - Nama RS besar dengan Playfair Display font-bold text-3xl text-green-900
    - -------
    - Section "Visi":
      - Label "Visi" font-semibold text-gold uppercase tracking-wide text-sm
      - Teks visi: {{ $visi }} — italic text-gray-700
    - -------
    - Section "Misi":
      - Label "Misi" font-semibold text-gold uppercase tracking-wide text-sm
      - Ordered list misi dari $misi (array):
        @foreach($misi as $i => $item)
          <li class="flex gap-3 mb-3">
            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-green-700 text-white text-sm flex items-center justify-center font-bold">{{ $i+1 }}</span>
            <span class="text-gray-700">{{ $item }}</span>
          </li>
        @endforeach
    - -------
    - Motto (jika ada): card kecil background gold-light, ikon tanda kutip besar, teks motto italic

  </div>
</section>

@endsection
```

---

### resources/views/profil/profil-rs.blade.php

```
@extends('layouts.app')
@section('title', 'Profil RS — RS TK IV Sintang')
@section('content')

[Page Header: "Profil Rumah Sakit", parent: "Profil RS"]

<section class="py-16 bg-white">
  <div class="container mx-auto px-4 max-w-6xl">

    BAGIAN 1 — Sejarah RS (2 kolom, gambar kiri teks kanan):
    - Foto gedung jika $foto ada, gradient placeholder jika tidak
    - Dekoratif: kotak solid hijau gelap di pojok (seperti screenshot referensi, offset 20px)
    - Judul "Sejarah Singkat" dengan Playfair + garis emas bawah
    - Konten {!! $sejarah !!} — render HTML (tinymce-like content)
    - Legalitas: {!! $legalitas !!} di bawah sejarah dalam card abu-abu ringan

    BAGIAN 2 — Nilai-Nilai RS (full width, bg-gray-50):
    - Judul "Nilai-Nilai Kami" centered
    - Grid 4 kolom dari $nilai:
      @foreach($nilai as $n)
        Card: ikon FA besar text-green-600, judul bold, teks kecil gray
        Hover: shadow-lg -translate-y-1 border-t-4 border-gold
      @endforeach

    BAGIAN 3 — Legalitas & Akreditasi (bg-green-900 text-white):
    - Judul "Legalitas & Akreditasi" centered text-white
    - Render {!! $legalitas !!} dengan styling putih
    - Logo akreditasi placeholder (KARS, Kemenkes) sebagai badge card putih

  </div>
</section>

@endsection
```

---

### resources/views/profil/direktur.blade.php

```
@extends('layouts.app')
@section('title', 'Direktur RS — RS TK IV Sintang')
@section('content')

[Page Header: "Direktur Rumah Sakit", parent: "Profil RS"]

<section class="py-16 bg-white">
  <div class="container mx-auto px-4 max-w-6xl">

    Layout 2 kolom (lg:grid-cols-5):
    - Kiri (2/5): Foto direktur
      - Jika $foto ada: <img> dalam frame persegi panjang rounded-xl shadow-xl
      - Jika tidak: div gradient militer (#1B4332 → #2D6A4F) dengan
        inisial nama besar + bintang TNI SVG inline di tengah
      - Di bawah foto: card info singkat:
        - Nama direktur (bold, Playfair)
        - Jabatan (text-gold)
        - NRP (text-gray-500 text-sm)
      - Badge "Kepala RS TK IV Sintang" (bg-green-900 text-white rounded px-3 py-1)

    - Kanan (3/5): Sambutan + Riwayat
      - Ikon tanda kutip besar text-gold opacity-30 (dekoratif)
      - Sub-judul "Sambutan Direktur" (Playfair italic text-2xl text-green-900)
      - Konten {!! $sambutan !!} — paragraf sambutan, text-gray-700 leading-relaxed
      - Tanda tangan (teks italic) + nama tebal + jabatan

      - Divider garis tipis emas

      - 2 kolom bawah:
        KIRI — "Riwayat Pendidikan":
          - List dari $pendidikan:
            @foreach($pendidikan as $p)
              <li class="flex gap-2 mb-2">
                <i class="fa fa-graduation-cap text-green-600 mt-1"></i>
                <span>{{ $p }}</span>
              </li>
            @endforeach

        KANAN — "Riwayat Jabatan":
          - List dari $riwayat:
            @foreach($riwayat as $r)
              <li class="flex gap-2 mb-2">
                <i class="fa fa-briefcase text-gold mt-1"></i>
                <span>{{ $r }}</span>
              </li>
            @endforeach

  </div>
</section>

@endsection
```

---

## 5. View Admin

### resources/views/admin/profil/index.blade.php

Form panjang dalam 1 halaman, dikelompokkan dalam 4 card/tab section.
Gunakan Alpine.js `x-data="{tab: 'visi'}"` untuk tab navigation.

```
@extends('layouts.admin')
@section('title', 'Kelola Profil RS')
@section('content')

<form action="{{ route('admin.profil.update') }}" method="POST" enctype="multipart/form-data">
  @csrf

  [Tab Navigation — 4 tab horizontal]:
  - Visi & Misi
  - Profil RS
  - Nilai-Nilai
  - Direktur

  =====================
  TAB 1: VISI & MISI
  =====================
  Card putih shadow rounded-xl p-6:

  - Input "Motto RS" (text):
    name="profil_motto", value="{{ $settings['profil_motto'] ?? '' }}"

  - Textarea "Visi" (2 baris):
    name="profil_visi", value="{{ $settings['profil_visi'] ?? '' }}"

  - Section "Misi" (dinamis dengan Alpine.js):
    x-data="{ misi: {{ Js::from(json_decode($settings['profil_misi'] ?? '[]', true)) }} }"
    - Tombol "+ Tambah Poin Misi"
    - Loop: setiap item = input text name="profil_misi[]" + tombol hapus ✕
    - Sortable (opsional — cukup add/remove)

  =====================
  TAB 2: PROFIL RS
  =====================
  Card putih:

  - Upload "Foto Gedung RS":
    name="profil_rs_foto"
    - Preview gambar existing jika ada: <img src="{{ Storage::url($settings['profil_rs_foto']) }}">
    - Preview saat pilih file baru (JS FileReader)
    - Helper: "Rekomendasi ukuran: 800×600px, maks 2MB"

  - Textarea "Sejarah RS" (HTML):
    name="profil_rs_sejarah", rows=8
    {{ $settings['profil_rs_sejarah'] ?? '' }}
    - Catatan di bawah: "Konten mendukung tag HTML dasar (<p>, <strong>, <em>, <ul>, <li>)"

  - Textarea "Legalitas & Akreditasi" (HTML):
    name="profil_rs_legalitas", rows=5
    {{ $settings['profil_rs_legalitas'] ?? '' }}

  =====================
  TAB 3: NILAI-NILAI RS
  =====================
  Card putih:

  x-data="{ nilai: {{ Js::from(json_decode($settings['profil_rs_nilai'] ?? '[]', true)) }} }"

  - Tombol "+ Tambah Nilai"
  - Loop template: setiap item = row 3 input dalam grid:
    - Input "Ikon" (FA class, e.g. fa-heart): name="nilai[i][ikon]"
      + Preview live: <i :class="'fa ' + nilai[i].ikon"></i>
    - Input "Judul": name="nilai[i][judul]"
    - Input "Deskripsi": name="nilai[i][teks]"
    - Tombol hapus row ✕
  - Max 8 nilai

  =====================
  TAB 4: DIREKTUR
  =====================
  Card putih, 2 kolom (lg:grid-cols-2 gap-6):

  KOLOM KIRI:
  - Upload "Foto Direktur":
    name="direktur_foto"
    - Preview existing + preview baru via FileReader
    - Frame foto persegi panjang (3:4 ratio) rounded-xl

  KOLOM KANAN:
  - Input "Nama Lengkap & Pangkat": name="direktur_nama"
    placeholder="Kolonel CKM dr. Nama, Sp.XX"
  - Input "Jabatan": name="direktur_jabatan"
  - Input "NRP": name="direktur_nrp"

  FULL WIDTH di bawahnya:
  - Textarea "Teks Sambutan" (HTML): name="direktur_sambutan", rows=10
    {{ $settings['direktur_sambutan'] ?? '' }}

  2 kolom lagi:
  KIRI — Riwayat Pendidikan (dinamis):
    x-data="{ pend: {{ Js::from(json_decode($settings['direktur_pendidikan'] ?? '[]', true)) }} }"
    - Loop: input text name="direktur_pendidikan[]" + hapus
    - Tombol "+ Tambah Pendidikan"

  KANAN — Riwayat Jabatan (dinamis):
    x-data="{ riw: {{ Js::from(json_decode($settings['direktur_riwayat'] ?? '[]', true)) }} }"
    - Loop: input text name="direktur_riwayat[]" + hapus
    - Tombol "+ Tambah Jabatan"

  [Tombol Simpan — sticky di bawah]:
  <div class="sticky bottom-0 bg-white border-t p-4 flex justify-end gap-3">
    <a href="{{ route('admin.dashboard') }}" class="btn-secondary">Batal</a>
    <button type="submit" class="btn-primary">
      <i class="fa fa-save mr-2"></i> Simpan Semua Perubahan
    </button>
  </div>

</form>
@endsection
```

---

## 6. Navbar — Dropdown Profil RS

Di `partials/navbar.blade.php`, ubah/tambahkan item menu menjadi:

```blade
{{-- Menu Profil RS dengan Dropdown --}}
<div class="relative" x-data="{ open: false }">
    <button @click="open = !open" @click.away="open = false"
        class="flex items-center gap-1 hover:text-green-700 font-medium transition-colors">
        Profil RS
        <i class="fa fa-chevron-down text-xs transition-transform" :class="open ? 'rotate-180' : ''"></i>
    </button>

    <div x-show="open" x-transition
        class="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">

        <a href="{{ route('profil.visi-misi') }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 hover:text-green-700 transition-colors
                  {{ request()->routeIs('profil.visi-misi') ? 'text-green-700 bg-green-50 font-medium' : 'text-gray-700' }}">
            <i class="fa fa-eye w-5 text-green-600"></i>
            Visi & Misi
        </a>

        <a href="{{ route('profil.profil-rs') }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 hover:text-green-700 transition-colors
                  {{ request()->routeIs('profil.profil-rs') ? 'text-green-700 bg-green-50 font-medium' : 'text-gray-700' }}">
            <i class="fa fa-hospital w-5 text-green-600"></i>
            Profil RS
        </a>

        <a href="{{ route('profil.direktur') }}"
           class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 hover:text-green-700 transition-colors
                  {{ request()->routeIs('profil.direktur') ? 'text-green-700 bg-green-50 font-medium' : 'text-gray-700' }}">
            <i class="fa fa-user-tie w-5 text-green-600"></i>
            Direktur RS
        </a>
    </div>
</div>
```

Pastikan dropdown ini menggunakan Alpine.js yang sudah di-load di `layouts/app.blade.php`.

---

## 7. Sidebar Admin — Tambah Entry Profil RS

Di `layouts/admin.blade.php`, tambahkan item menu di bawah entry Dashboard:

```blade
<a href="{{ route('admin.profil.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
    <i class="fa fa-id-card w-5"></i>
    <span>Profil RS</span>
</a>
```

---

## 8. Detail CSS Tambahan

Tambahkan ke tag `<style>` di `layouts/app.blade.php`:

```css
/* Foto Direktur — frame militer */
.direktur-frame {
    position: relative;
    border-radius: 12px;
    overflow: hidden;
    aspect-ratio: 3/4;
}
.direktur-frame::before {
    content: '';
    position: absolute;
    inset: 0;
    background: linear-gradient(to bottom, transparent 60%, rgba(27,67,50,0.8));
    z-index: 1;
}

/* Dekoratif kotak pojok (referensi screenshot) */
.photo-deco {
    position: relative;
}
.photo-deco::before {
    content: '';
    position: absolute;
    width: 60px;
    height: 60px;
    background: #C9A84C;
    top: -15px;
    left: -15px;
    z-index: -1;
    border-radius: 4px;
}
.photo-deco::after {
    content: '';
    position: absolute;
    width: 40px;
    height: 40px;
    background: #2D6A4F;
    bottom: -10px;
    right: -10px;
    z-index: -1;
    border-radius: 4px;
}

/* Tab admin aktif */
.tab-btn.active {
    background: #2D6A4F;
    color: white;
}
```

---

## Checklist Akhir

Setelah semua file dibuat/dimodifikasi:

- [ ] Migration baru dapat dijalankan tanpa error (`php artisan migrate`)
- [ ] Semua keys profil ter-seed di `rssite_site_settings`
- [ ] Route `profil.*` terdaftar (`php artisan route:list | grep profil`)
- [ ] Halaman Visi & Misi dapat diakses di `/profil/visi-misi`
- [ ] Halaman Profil RS dapat diakses di `/profil/profil-rs`
- [ ] Halaman Direktur dapat diakses di `/profil/direktur`
- [ ] Navbar dropdown "Profil RS" muncul dengan 3 sub-menu
- [ ] Dropdown berfungsi di desktop (hover/click) dan mobile (tap)
- [ ] Active state navbar highlight sesuai halaman aktif
- [ ] Breadcrumb tampil benar di semua halaman profil
- [ ] Admin `/admin/profil` dapat diakses (auth protected)
- [ ] Tab navigasi admin berfungsi (Alpine.js)
- [ ] Simpan visi → tampil di halaman publik visi-misi
- [ ] Simpan misi (dinamis add/remove) → tersimpan JSON → tampil sebagai list
- [ ] Upload foto gedung → tampil di halaman profil-rs dan visi-misi
- [ ] Upload foto direktur → tampil di halaman direktur
- [ ] Riwayat pendidikan & jabatan (dinamis) → tersimpan dan tampil
- [ ] Jika foto null → fallback gradient (tidak ada error atau broken image)
- [ ] Halaman Direktur responsif di mobile (stack vertikal)
- [ ] Sidebar admin menampilkan "Profil RS" dengan highlight saat aktif
