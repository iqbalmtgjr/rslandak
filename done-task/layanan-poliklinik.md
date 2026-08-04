# Task: Menu Layanan — Poliklinik (dengan Dokter & Jadwal per Poli)

Tambahkan menu dropdown **Layanan** dengan sub-menu pertama: **Poliklinik**.
Referensi tampilan: screenshot halaman Poliklinik RSI PKU Muhammadiyah Tegal.

Konteks project: lihat `CLAUDE.md`. Stack: Laravel 10, MySQL, Tailwind CDN, Alpine.js.

CATATAN: Model `Dokter` dan tabel `rssite_dokters` sudah ada dari CLAUDE.md.
Task ini menambah tabel Poliklinik, relasi Dokter ↔ Poliklinik, dan halaman publik.

---

## Konsep

```
Navbar → Layanan (dropdown)
  ├── Poliklinik       ← task ini
  ├── Rawat Inap       ← task terpisah
  ├── Pelayanan 24 Jam ← task terpisah

/layanan/poliklinik
  └── Grid semua poli (ikon + nama)
        └── Klik poli → modal atau halaman detail
              ├── Deskripsi poli
              ├── Daftar dokter yang bertugas di poli ini
              └── Jadwal praktik per dokter
```

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan / Diubah                                                   |
|-------------|-----------------------------------------------------------------------------|
| Database    | rssite_polikliniks, rssite_dokter_poliklinik (pivot)                        |
| Models      | Poliklinik (baru), update Dokter (tambah relasi polikliniks)                |
| Routes      | /layanan/poliklinik (list) + /layanan/poliklinik/{slug} (detail)            |
| Controllers | LayananController@poliklinik + @poliklinikDetail                            |
| Views       | layanan/poliklinik/index.blade.php + show.blade.php                         |
| Navbar      | Dropdown "Layanan" dengan 5 sub-menu (4 lainnya placeholder route)          |
| Sidebar     | Entry "Poliklinik" di grup Konten admin                                     |
| Admin       | Admin/PoliklinikController — CRUD poli + assign dokter                      |

---

## 1. Database Migrations

### Tabel: rssite_polikliniks

```php
Schema::create('rssite_polikliniks', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 150);           // "Poliklinik Gigi", "Poliklinik Anak"
    $table->string('slug', 150)->unique(); // "poliklinik-gigi"
    $table->string('ikon', 100)->nullable(); // path gambar SVG/PNG atau FA class
    $table->string('tipe_ikon', 10)->default('fa'); // 'fa' atau 'img'
    $table->text('deskripsi')->nullable();
    $table->text('prosedur')->nullable();   // HTML — tata cara berobat di poli ini
    $table->integer('urutan')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

### Tabel Pivot: rssite_dokter_poliklinik

```php
Schema::create('rssite_dokter_poliklinik', function (Blueprint $table) {
    $table->id();
    $table->foreignId('dokter_id')
          ->constrained('rssite_dokters')
          ->onDelete('cascade');
    $table->foreignId('poliklinik_id')
          ->constrained('rssite_polikliniks')
          ->onDelete('cascade');
    $table->integer('urutan')->default(0);
    $table->unique(['dokter_id', 'poliklinik_id']); // satu dokter tidak dobel di satu poli
    $table->timestamps();
});
```

---

## 2. Models

### app/Models/Poliklinik.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class Poliklinik extends Model
{
    protected $table    = 'rssite_polikliniks';
    protected $fillable = [
        'nama', 'slug', 'ikon', 'tipe_ikon',
        'deskripsi', 'prosedur', 'urutan', 'aktif',
    ];
    protected $casts = ['aktif' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->nama);
        });
    }

    // Relasi many-to-many ke Dokter (melalui pivot)
    public function dokters()
    {
        return $this->belongsToMany(
            Dokter::class,
            'rssite_dokter_poliklinik',
            'poliklinik_id',
            'dokter_id'
        )->withPivot('urutan')->orderBy('rssite_dokter_poliklinik.urutan');
    }

    public function scopeAktif($q) { return $q->where('aktif', true); }

    // Accessor: jumlah dokter aktif
    public function getJumlahDokterAttribute(): int
    {
        return $this->dokters()->where('rssite_dokters.aktif', true)->count();
    }

    // Accessor: URL ikon
    // Jika tipe_ikon = 'img' → dari storage
    // Jika tipe_ikon = 'fa'  → null (FA class langsung dari kolom ikon)
    public function getIkonUrlAttribute(): ?string
    {
        if ($this->tipe_ikon === 'img' && $this->ikon) {
            return asset('storage/' . $this->ikon);
        }
        return null;
    }
}
```

### Update app/Models/Dokter.php

Tambahkan relasi ke Poliklinik di model Dokter yang sudah ada:

```php
// Tambahkan method ini ke model Dokter yang sudah ada

public function polikliniks()
{
    return $this->belongsToMany(
        Poliklinik::class,
        'rssite_dokter_poliklinik',
        'dokter_id',
        'poliklinik_id'
    )->withPivot('urutan');
}
```

---

## 3. Routes (routes/web.php)

Tambahkan di bawah route publik:

```php
// Layanan — Poliklinik
Route::prefix('layanan')->name('layanan.')->group(function () {
    Route::get('/poliklinik',        [LayananController::class, 'poliklinik'])
         ->name('poliklinik.index');
    Route::get('/poliklinik/{slug}', [LayananController::class, 'poliklinikDetail'])
         ->name('poliklinik.show');

    // Placeholder routes untuk sub-menu lain (buat halaman "segera hadir")
    Route::get('/rawat-inap',        [LayananController::class, 'rawatInap'])
         ->name('rawat-inap');
    Route::get('/pelayanan-24-jam',  [LayananController::class, 'pelayanan24Jam'])
         ->name('pelayanan-24-jam');
});
```

Tambahkan di dalam group admin:

```php
// Admin Poliklinik
Route::prefix('poliklinik')->name('poliklinik.')->group(function () {
    Route::get('/',                     [Admin\PoliklinikController::class, 'index'])         ->name('index');
    Route::get('/create',               [Admin\PoliklinikController::class, 'create'])        ->name('create');
    Route::post('/',                    [Admin\PoliklinikController::class, 'store'])          ->name('store');
    Route::get('/{id}/edit',            [Admin\PoliklinikController::class, 'edit'])           ->name('edit');
    Route::put('/{id}',                 [Admin\PoliklinikController::class, 'update'])         ->name('update');
    Route::delete('/{id}',              [Admin\PoliklinikController::class, 'destroy'])        ->name('destroy');
    Route::post('/{id}/toggle',         [Admin\PoliklinikController::class, 'toggle'])         ->name('toggle');
    // Assign/remove dokter ke poli
    Route::get('/{id}/dokter',          [Admin\PoliklinikController::class, 'manageDokter'])   ->name('dokter');
    Route::post('/{id}/dokter/sync',    [Admin\PoliklinikController::class, 'syncDokter'])     ->name('dokter.sync');
});
```

---

## 4. Controllers

### app/Http/Controllers/LayananController.php (Publik)

```php
<?php
namespace App\Http\Controllers;

use App\Models\Poliklinik;

class LayananController extends Controller
{
    public function poliklinik()
    {
        $polikliniks = Poliklinik::aktif()
            ->withCount(['dokters as jumlah_dokter' => function ($q) {
                $q->where('rssite_dokters.aktif', true);
            }])
            ->orderBy('urutan')
            ->get();

        return view('layanan.poliklinik.index', compact('polikliniks'));
    }

    public function poliklinikDetail(string $slug)
    {
        $poli = Poliklinik::aktif()
            ->where('slug', $slug)
            ->with(['dokters' => function ($q) {
                $q->where('rssite_dokters.aktif', true)
                  ->orderBy('rssite_dokter_poliklinik.urutan');
            }])
            ->firstOrFail();

        return view('layanan.poliklinik.show', compact('poli'));
    }

    // Placeholder — halaman "Segera Hadir" untuk sub-menu lain
    public function rawatInap()
    {
        return view('layanan.coming-soon', ['judul' => 'Rawat Inap']);
    }

    public function pelayanan24Jam()
    {
        return view('layanan.coming-soon', ['judul' => 'Pelayanan 24 Jam']);
    }
}
```

### app/Http/Controllers/Admin/PoliklinikController.php (Admin)

```php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Poliklinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoliklinikController extends Controller
{
    public function index()
    {
        $polikliniks = Poliklinik::withCount('dokters')
            ->orderBy('urutan')->paginate(20);
        return view('admin.poliklinik.index', compact('polikliniks'));
    }

    public function create()
    {
        return view('admin.poliklinik.form', ['poli' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'prosedur'  => 'nullable|string',
            'urutan'    => 'nullable|integer',
            'ikon_file' => 'nullable|image|max:1024', // max 1MB untuk SVG/PNG ikon
            'ikon_fa'   => 'nullable|string|max:100',
        ]);

        $data = [
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'prosedur'  => $request->prosedur,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ];

        // Tentukan tipe ikon: gambar upload atau FA class
        if ($request->hasFile('ikon_file')) {
            $data['ikon']      = $request->file('ikon_file')->store('rssite/poli-ikon', 'public');
            $data['tipe_ikon'] = 'img';
        } elseif ($request->filled('ikon_fa')) {
            $data['ikon']      = $request->ikon_fa;
            $data['tipe_ikon'] = 'fa';
        }

        Poliklinik::create($data);

        return redirect()->route('admin.poliklinik.index')
                         ->with('success', 'Poliklinik berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        return view('admin.poliklinik.form', compact('poli'));
    }

    public function update(Request $request, int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'prosedur'  => 'nullable|string',
            'urutan'    => 'nullable|integer',
            'ikon_file' => 'nullable|image|max:1024',
            'ikon_fa'   => 'nullable|string|max:100',
        ]);

        $data = [
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'prosedur'  => $request->prosedur,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('ikon_file')) {
            // Hapus ikon lama jika berupa gambar
            if ($poli->tipe_ikon === 'img' && $poli->ikon) {
                Storage::disk('public')->delete($poli->ikon);
            }
            $data['ikon']      = $request->file('ikon_file')->store('rssite/poli-ikon', 'public');
            $data['tipe_ikon'] = 'img';
        } elseif ($request->filled('ikon_fa')) {
            if ($poli->tipe_ikon === 'img' && $poli->ikon) {
                Storage::disk('public')->delete($poli->ikon);
            }
            $data['ikon']      = $request->ikon_fa;
            $data['tipe_ikon'] = 'fa';
        }

        $poli->update($data);

        return redirect()->route('admin.poliklinik.index')
                         ->with('success', 'Poliklinik berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        if ($poli->tipe_ikon === 'img' && $poli->ikon) {
            Storage::disk('public')->delete($poli->ikon);
        }
        $poli->delete(); // cascade hapus pivot
        return redirect()->back()->with('success', 'Poliklinik berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        $poli->update(['aktif' => !$poli->aktif]);
        return redirect()->back()->with('success', 'Status poliklinik diperbarui.');
    }

    // Halaman assign dokter ke poli
    public function manageDokter(int $id)
    {
        $poli         = Poliklinik::with('dokters')->findOrFail($id);
        $semuaDokter  = Dokter::where('aktif', true)->orderBy('nama')->get();
        $assignedIds  = $poli->dokters->pluck('id')->toArray();

        return view('admin.poliklinik.dokter', compact('poli', 'semuaDokter', 'assignedIds'));
    }

    // Simpan assign dokter ke poli (sync many-to-many)
    public function syncDokter(Request $request, int $id)
    {
        $poli = Poliklinik::findOrFail($id);

        $request->validate([
            'dokter_ids'   => 'nullable|array',
            'dokter_ids.*' => 'exists:rssite_dokters,id',
        ]);

        // Sync: hapus semua pivot lama, isi yang baru
        // Setiap dokter_id datang dengan urutan sesuai posisi array
        $syncData = [];
        foreach ($request->input('dokter_ids', []) as $urutan => $dokterId) {
            $syncData[$dokterId] = ['urutan' => $urutan + 1];
        }

        $poli->dokters()->sync($syncData);

        return redirect()->route('admin.poliklinik.dokter', $id)
                         ->with('success', 'Dokter poli berhasil disimpan.');
    }
}
```

---

## 5. Views Publik

### resources/views/layanan/poliklinik/index.blade.php

```blade
@extends('layouts.app')
@section('title', 'Poliklinik — RS TK IV Sintang')
@section('content')

[Page Header: "Poliklinik", parent: "Layanan"]

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl">

    {{-- Sub-judul --}}
    <p class="text-center text-gray-500 mb-10 text-sm">
      Klik poliklinik untuk melihat dokter dan jadwal praktik
    </p>

    {{-- ===== GRID POLIKLINIK ===== --}}
    {{-- Layout: 4 kolom desktop, 2 tablet, 2 mobile — PERSIS seperti referensi screenshot --}}

    @if($polikliniks->isEmpty())
      <div class="text-center py-24 text-gray-400">
        <i class="fa fa-clinic-medical text-6xl mb-4 block"></i>
        <p>Belum ada poliklinik yang tersedia.</p>
      </div>
    @else

      <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @foreach($polikliniks as $poli)

          <a href="{{ route('layanan.poliklinik.show', $poli->slug) }}"
             class="block bg-white rounded-xl overflow-hidden shadow-sm
                    hover:shadow-md hover:-translate-y-0.5 transition-all duration-200 group reveal">

            {{-- Area ikon (background hijau muda seperti referensi) --}}
            <div class="bg-green-100 flex items-center justify-center p-6"
                 style="min-height: 120px;">

              @if($poli->tipe_ikon === 'img' && $poli->ikon_url)
                {{-- Gambar ikon upload (PNG/SVG hitam di background hijau muda) --}}
                <img src="{{ $poli->ikon_url }}"
                     alt="{{ $poli->nama }}"
                     class="w-16 h-16 object-contain
                            group-hover:scale-110 transition-transform duration-200"
                     style="filter: invert(0);">

              @elseif($poli->tipe_ikon === 'fa' && $poli->ikon)
                {{-- Font Awesome ikon --}}
                <i class="fa {{ $poli->ikon }} text-5xl text-green-800 opacity-70
                           group-hover:scale-110 transition-transform duration-200"></i>

              @else
                {{-- Default fallback: ikon stethoscope --}}
                <i class="fa fa-stethoscope text-5xl text-green-800 opacity-70
                          group-hover:scale-110 transition-transform duration-200"></i>
              @endif

            </div>

            {{-- Nama poliklinik di bawah ikon --}}
            <div class="p-4 border-t border-gray-100">
              <p class="text-sm text-gray-700 font-medium leading-snug
                        group-hover:text-green-700 transition-colors">
                {{ $poli->nama }}
              </p>
              @if($poli->jumlah_dokter > 0)
                <p class="text-xs text-gray-400 mt-1">
                  {{ $poli->jumlah_dokter }} dokter
                </p>
              @endif
            </div>

          </a>

        @endforeach
      </div>

    @endif

  </div>
</section>

@endsection
```

---

### resources/views/layanan/poliklinik/show.blade.php

Halaman detail poli — tampilkan deskripsi + daftar dokter + jadwal:

```blade
@extends('layouts.app')
@section('title', $poli->nama . ' — RS TK IV Sintang')
@section('content')

[Page Header: $poli->nama, parent: "Poliklinik", link parent: route('layanan.poliklinik.index')]

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-5xl">

    ===== HEADER POLI (card putih) =====
    Layout 2 kolom (lg: ikon kiri, info kanan):

    KIRI — kotak ikon besar:
    - bg-green-100 rounded-2xl w-40 h-40 flex items-center justify-center mx-auto
    - Tampilkan ikon: img atau FA atau fallback stethoscope
    - Ukuran lebih besar dari grid (text-7xl atau w-20 h-20 untuk img)

    KANAN — info poli:
    - Nama poli (font-playfair text-2xl font-bold text-gray-800)
    - Garis emas (w-12 h-1 bg-yellow-500 rounded my-3)
    - Deskripsi poli: {!! $poli->deskripsi !!}
    - Badge jumlah dokter (bg-green-100 text-green-700):
        fa-user-md + "X Dokter Bertugas"

    ===== PROSEDUR (jika ada) =====
    Card bg-blue-50 rounded-2xl p-6:
    - Judul: fa-clipboard-list "Tata Cara Berobat di {{ $poli->nama }}"
    - Konten: {!! $poli->prosedur !!} dengan styling prose

    ===== DAFTAR DOKTER & JADWAL =====
    Judul section: "Dokter & Jadwal Praktik"
    Sub: "Jadwal dapat berubah sewaktu-waktu, harap konfirmasi ke bagian pendaftaran"

    @if($poli->dokters->isEmpty())
      Card info:
      - Ikon fa-user-md text-4xl text-gray-300
      - "Informasi dokter sedang diperbarui."
      - Sub: "Silakan hubungi bagian informasi RS."
    @else

      Grid 1 kolom (md: 2 kolom) gap-5:
      @foreach($poli->dokters as $dokter)

        Card dokter (bg-white rounded-2xl shadow-sm p-5 flex gap-4):

        KIRI — Avatar dokter:
        @if($dokter->foto)
          <img src="{{ Storage::url($dokter->foto) }}"
               class="w-20 h-20 rounded-full object-cover border-4 border-green-100 flex-shrink-0">
        @else
          {{-- Avatar inisial --}}
          <div class="w-20 h-20 rounded-full flex-shrink-0 flex items-center justify-center
                      bg-gradient-to-br from-green-700 to-green-500 border-4 border-green-100">
            <span class="text-white font-bold text-xl">
              {{ strtoupper(substr($dokter->nama, 0, 1)) }}
            </span>
          </div>
        @endif

        KANAN — Info dokter:
        - Nama dokter (font-semibold text-gray-800)
        - Badge spesialisasi (bg-green-700 text-white text-xs px-3 py-1 rounded-full)
        - Bio singkat jika ada (text-xs text-gray-500 line-clamp-2)
        - Divider garis tipis
        - Jadwal praktik:
          Judul kecil: fa-calendar-alt "Jadwal Praktik:"
          @foreach(json_decode($dokter->jadwal ?? '[]', true) as $j)
            <div class="flex items-center gap-2 mt-1">
              <span class="w-24 text-xs font-medium text-gray-600">{{ $j['hari'] }}</span>
              <span class="text-xs text-green-700 font-semibold">{{ $j['jam'] }}</span>
            </div>
          @endforeach
          Jika jadwal kosong: teks "Hubungi bagian informasi"

      @endforeach

    @endif

    ===== CTA BAWAH =====
    Card gradient hijau (bg-gradient-to-r from-green-800 to-green-600 rounded-2xl p-6):
    - Judul putih: "Ingin Berobat ke {{ $poli->nama }}?"
    - Sub: "Daftarkan diri Anda melalui aplikasi Mobile JKN atau langsung ke loket pendaftaran"
    - 2 tombol: [Daftar via Mobile JKN] [Hubungi Kami → route('home')#kontak]

    ===== TOMBOL KEMBALI =====
    Link: fa-arrow-left "Kembali ke Daftar Poliklinik" → route('layanan.poliklinik.index')

  </div>
</section>

@endsection
```

---

### resources/views/layanan/coming-soon.blade.php

Halaman placeholder untuk sub-menu lain yang belum dibuat:

```blade
@extends('layouts.app')
@section('title', $judul . ' — RS TK IV Sintang')
@section('content')

@include('partials.page-header', ['judul' => $judul, 'parent' => 'Layanan'])

<section class="py-24 bg-gray-50 text-center">
  <div class="container mx-auto px-4 max-w-lg">
    <div class="bg-white rounded-2xl shadow-sm p-12">
      <i class="fa fa-tools text-6xl text-gray-200 mb-6 block"></i>
      <h2 class="font-playfair text-2xl font-bold text-gray-700 mb-3">
        Segera Hadir
      </h2>
      <p class="text-gray-500 text-sm mb-6">
        Halaman <strong>{{ $judul }}</strong> sedang dalam pengembangan.
        Kami akan segera menyelesaikannya.
      </p>
      <a href="{{ route('home') }}"
         class="inline-flex items-center gap-2 bg-green-700 text-white
                px-6 py-3 rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">
        <i class="fa fa-home"></i> Kembali ke Beranda
      </a>
    </div>
  </div>
</section>

@endsection
```

---

## 6. Views Admin

### resources/views/admin/poliklinik/index.blade.php

```
@extends('layouts.admin')
@section('title', 'Kelola Poliklinik')
@section('content')

HEADER:
- Judul "Kelola Poliklinik"
- Tombol kanan: [+ Tambah Poliklinik] primary

TABEL (rounded-xl shadow-sm overflow-hidden):
Header: # | Ikon | Nama Poliklinik | Dokter | Urutan | Status | Aksi

Per baris:
- Nomor urut row
- Ikon kecil (40x40):
    Jika tipe_ikon='img': <img src="{{ Storage::url($poli->ikon) }}" class="w-10 h-10 object-contain">
    Jika tipe_ikon='fa':  <i class="fa {{ $poli->ikon }} text-2xl text-green-700">
    Fallback: <i class="fa fa-stethoscope text-2xl text-gray-300">
- Nama (font-medium) + slug (text-xs text-gray-400)
- Badge jumlah dokter (bg-green-100 text-green-700)
- Urutan (angka)
- Toggle aktif
- Aksi: [Dokter] kuning (→ manageDokter) | [Edit] | [Hapus confirm]

Pagination default Laravel

@endsection
```

---

### resources/views/admin/poliklinik/form.blade.php

```
@extends('layouts.admin')
@section('title', $poli ? 'Edit Poliklinik' : 'Tambah Poliklinik')
@section('content')

Card max-w-2xl mx-auto:

Form enctype="multipart/form-data" POST/PUT:

1. Nama Poliklinik* (text)
   placeholder: "Poliklinik Gigi Spesialis"

2. PILIHAN IKON (Alpine.js tabs: "Upload Gambar" | "Font Awesome"):
   x-data="{ ikonMode: '{{ $poli ? $poli->tipe_ikon : 'fa' }}' }"

   Tab switcher pill:
   [ Upload Gambar ] [ Font Awesome ]

   PANEL Upload Gambar (x-show="ikonMode === 'img'"):
   - Input file name="ikon_file" accept=".png,.jpg,.svg"
   - Preview existing jika $poli->tipe_ikon === 'img':
       <img src="{{ $poli->ikon_url }}" class="w-20 h-20 object-contain border rounded">
   - Preview baru via FileReader JS
   - Hint: "Rekomendasikan ikon hitam/abu-abu (80x80px) format PNG transparan"
   - hidden input: <input type="hidden" name="tipe_ikon" value="img" x-show="ikonMode==='img'">

   PANEL Font Awesome (x-show="ikonMode === 'fa'"):
   - Input text name="ikon_fa" placeholder="fa-stethoscope"
     x-data untuk live preview
   - Preview: <i :class="'fa text-4xl text-green-700 ' + ikonFa">
   - Contoh: fa-stethoscope, fa-tooth, fa-brain, fa-heartbeat, fa-eye, fa-baby
   - Link: "Lihat semua ikon di fontawesome.com"
   - hidden input: <input type="hidden" name="tipe_ikon" value="fa" x-show="ikonMode==='fa'">

3. Deskripsi (textarea rows=4 nullable)
   hint: "Mendukung HTML sederhana"

4. Prosedur / Tata Cara Berobat (textarea rows=6 nullable)
   hint: "Mendukung HTML. Contoh: <ol><li>Ambil nomor antrian</li></ol>"

5. Urutan (number default 0)
   hint: "Semakin kecil → semakin kiri/atas posisinya"

6. Status aktif (toggle)

Tombol: [Simpan] [Batal → route admin.poliklinik.index]

@endsection
```

---

### resources/views/admin/poliklinik/dokter.blade.php

Halaman assign dokter ke poliklinik:

```
@extends('layouts.admin')
@section('title', 'Kelola Dokter — ' . $poli->nama)
@section('content')

HEADER:
- Breadcrumb: Poliklinik → {{ $poli->nama }} → Kelola Dokter
- Judul "Dokter di {{ $poli->nama }}"
- Sub: "Pilih dokter yang bertugas di poliklinik ini"

Form POST ke route admin.poliklinik.dokter.sync:

LAYOUT 2 KOLOM:

KIRI (col-span-2) — Daftar semua dokter (checkbox):
Card bg-white rounded-xl p-6:
Judul: "Pilih Dokter" ({{ count($semuaDokter) }} dokter tersedia)

Search filter (Alpine.js x-data filter list by nama):
<input type="text" placeholder="Cari nama dokter..."
       x-model="search" class="...">

Grid 2 kolom checklist dokter:
@foreach($semuaDokter as $dokter)
  <label class="flex items-center gap-3 p-3 rounded-xl border cursor-pointer
                hover:bg-green-50 transition-colors
                {{ in_array($dokter->id, $assignedIds) ? 'border-green-500 bg-green-50' : 'border-gray-200' }}"
         x-show="search === '' || '{{ strtolower($dokter->nama) }}'.includes(search.toLowerCase())">

    <input type="checkbox" name="dokter_ids[]" value="{{ $dokter->id }}"
           class="w-4 h-4 text-green-600 rounded"
           {{ in_array($dokter->id, $assignedIds) ? 'checked' : '' }}>

    {{-- Avatar --}}
    @if($dokter->foto)
      <img src="{{ Storage::url($dokter->foto) }}"
           class="w-10 h-10 rounded-full object-cover flex-shrink-0">
    @else
      <div class="w-10 h-10 rounded-full bg-green-700 flex items-center justify-center flex-shrink-0">
        <span class="text-white text-sm font-bold">
          {{ strtoupper(substr($dokter->nama, 0, 1)) }}
        </span>
      </div>
    @endif

    <div>
      <p class="text-sm font-medium text-gray-800">{{ $dokter->nama }}</p>
      <p class="text-xs text-gray-400">{{ $dokter->spesialisasi }}</p>
    </div>

  </label>
@endforeach

KANAN (col-span-1) — Summary:
Card sticky top-24 bg-white rounded-xl p-6:
Judul: "Dokter Terpilih"
- Tampilkan badge hijau setiap dokter yang di-check (Alpine.js reaktif)
- Gunakan Alpine.js $watch atau @change untuk update daftar kanan secara live

TOMBOL:
[Simpan Perubahan] primary full-width
[Kembali ke Daftar Poli] secondary

CATATAN IMPLEMENTASI — Alpine.js untuk summary:
x-data="{ selected: {{ json_encode($assignedIds) }} }"

Setiap checkbox: @change="toggleSelected({{ $dokter->id }})"

Methods:
toggleSelected(id) {
  const i = this.selected.indexOf(id);
  if (i === -1) this.selected.push(id);
  else this.selected.splice(i, 1);
}

Summary kanan: tampilkan nama dokter berdasarkan this.selected:
Karena Alpine.js tidak tahu nama dokter, simpan map id→nama di x-data:
const dokterMap = {{ Js::from($semuaDokter->pluck('nama', 'id')) }};

@endsection
```

---

## 7. Navbar — Dropdown Layanan

Di `partials/navbar.blade.php`, tambahkan dropdown Layanan:

```blade
<div class="relative" x-data="{ open: false }">
  <button @click="open = !open" @click.away="open = false"
          class="flex items-center gap-1 font-medium transition-colors
                 {{ request()->routeIs('layanan.*') ? 'text-green-700' : 'text-gray-700 hover:text-green-700' }}">
    Layanan
    <i class="fa fa-chevron-down text-xs transition-transform duration-200"
       :class="open ? 'rotate-180' : ''"></i>
  </button>

  <div x-show="open" x-transition
       class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl
              border border-gray-100 py-2 z-50">

    @php
    $layananMenu = [
      ['route' => 'layanan.poliklinik.index', 'ikon' => 'fa-clinic-medical', 'label' => 'Poliklinik'],
      ['route' => 'layanan.rawat-inap',       'ikon' => 'fa-bed',            'label' => 'Rawat Inap'],
      ['route' => 'layanan.pelayanan-24-jam', 'ikon' => 'fa-ambulance',      'label' => 'Pelayanan 24 Jam'],
    ];
    @endphp

    @foreach($layananMenu as $menu)
      <a href="{{ route($menu['route']) }}"
         class="flex items-center gap-3 px-4 py-3 text-sm transition-colors
                {{ request()->routeIs($menu['route']) || request()->routeIs(str_replace('.index','',$menu['route']).'.*')
                   ? 'bg-green-50 text-green-700 font-medium'
                   : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }}">
        <i class="fa {{ $menu['ikon'] }} w-5 text-green-600"></i>
        {{ $menu['label'] }}
      </a>
    @endforeach

  </div>
</div>
```

---

## 8. Sidebar Admin — Entry Poliklinik

Di `layouts/admin.blade.php`, tambahkan di grup Konten:

```blade
<a href="{{ route('admin.poliklinik.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.poliklinik.*') ? 'active' : '' }}">
  <i class="fa fa-clinic-medical w-5"></i>
  <span>Poliklinik</span>
</a>
```

---

## 9. Seeder — Data Poliklinik Awal

Di `DatabaseSeeder.php`:

```php
$polikliniks = [
    ['nama' => 'Klinik Umum',                   'ikon' => 'fa-stethoscope',  'tipe_ikon' => 'fa', 'urutan' => 1],
    ['nama' => 'Poliklinik Gigi',               'ikon' => 'fa-tooth',        'tipe_ikon' => 'fa', 'urutan' => 2],
    ['nama' => 'Poliklinik Anak',               'ikon' => 'fa-baby',         'tipe_ikon' => 'fa', 'urutan' => 3],
    ['nama' => 'Poliklinik Kandungan & Kebidanan','ikon'=> 'fa-female',      'tipe_ikon' => 'fa', 'urutan' => 4],
    ['nama' => 'Poliklinik Penyakit Dalam',     'ikon' => 'fa-heartbeat',    'tipe_ikon' => 'fa', 'urutan' => 5],
    ['nama' => 'Poliklinik Bedah Umum',         'ikon' => 'fa-scalpel',      'tipe_ikon' => 'fa', 'urutan' => 6],
    ['nama' => 'Poliklinik Mata',               'ikon' => 'fa-eye',          'tipe_ikon' => 'fa', 'urutan' => 7],
    ['nama' => 'Poliklinik THT',                'ikon' => 'fa-deaf',         'tipe_ikon' => 'fa', 'urutan' => 8],
    ['nama' => 'Poliklinik Saraf',              'ikon' => 'fa-brain',        'tipe_ikon' => 'fa', 'urutan' => 9],
    ['nama' => 'Poliklinik Kulit & Kelamin',    'ikon' => 'fa-allergies',    'tipe_ikon' => 'fa', 'urutan' => 10],
    ['nama' => 'Poliklinik Paru',               'ikon' => 'fa-lungs',        'tipe_ikon' => 'fa', 'urutan' => 11],
    ['nama' => 'Poliklinik Jantung',            'ikon' => 'fa-heart',        'tipe_ikon' => 'fa', 'urutan' => 12],
    ['nama' => 'Poliklinik Ortopedi',           'ikon' => 'fa-bone',         'tipe_ikon' => 'fa', 'urutan' => 13],
    ['nama' => 'Radiologi',                     'ikon' => 'fa-x-ray',        'tipe_ikon' => 'fa', 'urutan' => 14],
    ['nama' => 'Rehabilitasi Medik',            'ikon' => 'fa-wheelchair',   'tipe_ikon' => 'fa', 'urutan' => 15],
];

foreach ($polikliniks as $p) {
    \App\Models\Poliklinik::firstOrCreate(
        ['nama' => $p['nama']],
        array_merge($p, [
            'slug'  => \Illuminate\Support\Str::slug($p['nama']),
            'aktif' => true,
        ])
    );
}

// Assign dokter yang sudah ada ke poli (jika dokter seeder sudah jalan)
// Contoh: dokter SpA ke Poliklinik Anak
$poliAnak = \App\Models\Poliklinik::where('slug', 'poliklinik-anak')->first();
$dokterSpA = \App\Models\Dokter::where('spesialisasi', 'like', '%Anak%')->first();
if ($poliAnak && $dokterSpA) {
    $poliAnak->dokters()->syncWithoutDetaching([$dokterSpA->id => ['urutan' => 1]]);
}
```

---

## 10. CSS Tambahan

Tambahkan ke style di `layouts/app.blade.php`:

```css
/* Grid poliklinik — ikon area hijau muda persis referensi */
.poli-card .poli-ikon-area {
    background: #DCFCE7; /* green-100 */
    transition: background 0.2s;
}
.poli-card:hover .poli-ikon-area {
    background: #BBF7D0; /* green-200 */
}

/* Jadwal praktik — hari/jam layout */
.jadwal-row {
    display: grid;
    grid-template-columns: 100px 1fr;
    gap: 8px;
    align-items: center;
    padding: 4px 0;
    border-bottom: 1px dashed #E5E7EB;
}
.jadwal-row:last-child { border-bottom: none; }
```

---

## Checklist Akhir

**Database & Model**
- [ ] Migration rssite_polikliniks berjalan
- [ ] Migration rssite_dokter_poliklinik (pivot) berjalan dengan unique constraint
- [ ] Model Poliklinik: relasi dokters() many-to-many berfungsi
- [ ] Model Dokter: tambahan relasi polikliniks() berfungsi
- [ ] Seeder membuat 15 poliklinik default dengan FA ikon

**Halaman Publik /layanan/poliklinik**
- [ ] HTTP 200 accessible
- [ ] Grid 4 kolom desktop, 3 tablet, 2 mobile
- [ ] Area ikon background hijau muda (green-100) persis referensi
- [ ] Ikon FA tampil dengan benar per poli
- [ ] Ikon gambar (upload) tampil jika tipe_ikon = 'img'
- [ ] Fallback stethoscope jika tidak ada ikon
- [ ] Badge jumlah dokter tampil di bawah nama poli
- [ ] Klik card → navigasi ke detail poli

**Halaman Detail /layanan/poliklinik/{slug}**
- [ ] HTTP 200, slug valid → poli tampil
- [ ] Slug tidak ada / poli non-aktif → 404
- [ ] Nama, deskripsi poli tampil
- [ ] Prosedur / tata cara berobat tampil (jika ada)
- [ ] Grid dokter 2 kolom di desktop, 1 di mobile
- [ ] Foto dokter tampil jika ada, avatar inisial jika tidak
- [ ] Badge spesialisasi per dokter
- [ ] Jadwal JSON di-loop dan tampil per baris (hari + jam)
- [ ] Jadwal kosong → teks "Hubungi bagian informasi"
- [ ] Dokter kosong → pesan informasi sedang diperbarui
- [ ] CTA daftar berobat di bawah
- [ ] Link kembali ke daftar poli

**Halaman Coming Soon**
- [ ] /layanan/rawat-inap → coming-soon page
- [ ] /layanan/pelayanan-24-jam → coming-soon page

**Navbar**
- [ ] Dropdown "Layanan" muncul dengan 5 sub-menu
- [ ] Klik "Poliklinik" → /layanan/poliklinik
- [ ] Dropdown menutup saat klik di luar (Alpine.js @click.away)
- [ ] Active highlight saat di halaman layanan manapun

**Admin Panel**
- [ ] /admin/poliklinik accessible (auth protected)
- [ ] Tabel index tampil dengan ikon, nama, jumlah dokter
- [ ] Tambah poli: form dengan pilihan ikon (FA atau upload)
- [ ] Mode FA: live preview ikon saat ketik
- [ ] Mode img: preview upload gambar
- [ ] Edit poli: data existing ter-load dengan benar
- [ ] Hapus poli: ikon gambar ikut terhapus dari storage
- [ ] Toggle aktif/non-aktif berfungsi
- [ ] Halaman manage dokter: semua dokter aktif tampil sebagai checklist
- [ ] Search dokter di halaman manage (Alpine.js filter)
- [ ] Checkbox pre-checked untuk dokter yang sudah diassign
- [ ] Summary kanan menampilkan dokter yang terpilih secara live
- [ ] Sync dokter: simpan/update assignment + urutan
- [ ] Sidebar admin "Poliklinik" highlight saat aktif
