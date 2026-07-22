# Task: Halaman Leaflet & Poster — Accordion + Tab + Google Drive Link

Tambahkan halaman publik **Leaflet & Poster** ke project website RS TK IV Sintang.
Referensi tampilan: screenshot halaman Leaflet & Poster RSI PKU Muhammadiyah Tegal.

Konteks project: lihat `CLAUDE.md`. Stack: Laravel 10, MySQL, Tailwind CDN, Alpine.js.

---

## Konsep Halaman

Halaman ini menampilkan daftar leaflet dan poster edukasi kesehatan yang bisa diakses
masyarakat. Strukturnya:

```
Tab (Leaflet | Poster)
  └── Kategori (accordion)         ← e.g. "PENYAKIT DALAM", "SARAF", "ANAK"
        └── Item Rincian           ← e.g. "Mengenal Diabetes", link → Google Drive
```

Admin bisa mengelola:
- **Tipe**: Leaflet atau Poster
- **Kategori**: nama grup (e.g. "Penyakit Dalam")
- **Item/Rincian**: nama file + URL Google Drive (langsung tempel link share GDrive)

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan                                                        |
|-------------|-------------------------------------------------------------------------|
| Database    | `rssite_leaflet_kategoris`, `rssite_leaflet_items`                      |
| Models      | `LeafletKategori`, `LeafletItem`                                        |
| Routes      | `/leaflet-poster` (publik) + `/admin/leaflet` (admin CRUD)             |
| Controllers | `LeafletController` (publik) + `Admin/LeafletController` (admin)        |
| Views       | `leaflet/index.blade.php` + admin index/form views                     |
| Navbar      | Dropdown atau link langsung "Leaflet & Poster"                          |
| Sidebar     | Entry baru di grup Konten                                               |

---

## 1. Database Migrations

### Tabel: rssite_leaflet_kategoris

```php
Schema::create('rssite_leaflet_kategoris', function (Blueprint $table) {
    $table->id();
    $table->enum('tipe', ['Leaflet', 'Poster'])->default('Leaflet');
    $table->string('nama', 150);          // "Penyakit Dalam", "Saraf", "Anak", dll
    $table->integer('urutan')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

### Tabel: rssite_leaflet_items

```php
Schema::create('rssite_leaflet_items', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kategori_id')
          ->constrained('rssite_leaflet_kategoris')
          ->onDelete('cascade');
    $table->string('nama', 255);          // "Mengenal Diabetes Melitus"
    $table->text('url_gdrive');           // link Google Drive (view/share)
    $table->string('deskripsi', 255)->nullable(); // opsional, keterangan singkat
    $table->integer('urutan')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

---

## 2. Models

### app/Models/LeafletKategori.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeafletKategori extends Model
{
    protected $table    = 'rssite_leaflet_kategoris';
    protected $fillable = ['tipe', 'nama', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function items()
    {
        return $this->hasMany(LeafletItem::class, 'kategori_id')
                    ->where('aktif', true)
                    ->orderBy('urutan');
    }

    // Semua items tanpa filter aktif (untuk admin)
    public function allItems()
    {
        return $this->hasMany(LeafletItem::class, 'kategori_id')->orderBy('urutan');
    }

    public function scopeAktif($q) { return $q->where('aktif', true); }
    public function scopeTipe($q, string $tipe) { return $q->where('tipe', $tipe); }
}
```

### app/Models/LeafletItem.php

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeafletItem extends Model
{
    protected $table    = 'rssite_leaflet_items';
    protected $fillable = ['kategori_id', 'nama', 'url_gdrive', 'deskripsi', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function kategori()
    {
        return $this->belongsTo(LeafletKategori::class, 'kategori_id');
    }

    // Accessor: konversi URL Google Drive biasa → URL preview/embed yang bisa dibuka
    // Input:  https://drive.google.com/file/d/FILE_ID/view?usp=sharing
    // Output: https://drive.google.com/file/d/FILE_ID/preview  (untuk embed)
    //         atau tetap url asli untuk buka di tab baru
    public function getUrlPreviewAttribute(): string
    {
        $url = $this->url_gdrive;
        // Ekstrak file ID dari berbagai format URL Google Drive
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }
        return $url; // fallback: url asli
    }

    public function getUrlOpenAttribute(): string
    {
        $url = $this->url_gdrive;
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/view';
        }
        return $url;
    }
}
```

---

## 3. Routes (routes/web.php)

Tambahkan di bawah route publik:

```php
// Leaflet & Poster — Publik
Route::get('/leaflet-poster', [LeafletController::class, 'index'])->name('leaflet.index');
```

Tambahkan di dalam group admin (middleware auth):

```php
// Admin Leaflet
Route::prefix('leaflet')->name('leaflet.')->group(function () {
    // Kategori
    Route::get('/',                   [Admin\LeafletController::class, 'index'])         ->name('index');
    Route::get('/kategori/create',    [Admin\LeafletController::class, 'createKategori'])->name('kategori.create');
    Route::post('/kategori',          [Admin\LeafletController::class, 'storeKategori']) ->name('kategori.store');
    Route::get('/kategori/{id}/edit', [Admin\LeafletController::class, 'editKategori'])  ->name('kategori.edit');
    Route::put('/kategori/{id}',      [Admin\LeafletController::class, 'updateKategori'])->name('kategori.update');
    Route::delete('/kategori/{id}',   [Admin\LeafletController::class, 'destroyKategori'])->name('kategori.destroy');
    Route::post('/kategori/{id}/toggle', [Admin\LeafletController::class, 'toggleKategori'])->name('kategori.toggle');

    // Item/Rincian
    Route::get('/item/create',        [Admin\LeafletController::class, 'createItem'])    ->name('item.create');
    Route::post('/item',              [Admin\LeafletController::class, 'storeItem'])     ->name('item.store');
    Route::get('/item/{id}/edit',     [Admin\LeafletController::class, 'editItem'])      ->name('item.edit');
    Route::put('/item/{id}',          [Admin\LeafletController::class, 'updateItem'])    ->name('item.update');
    Route::delete('/item/{id}',       [Admin\LeafletController::class, 'destroyItem'])   ->name('item.destroy');
    Route::post('/item/{id}/toggle',  [Admin\LeafletController::class, 'toggleItem'])    ->name('item.toggle');
});
```

---

## 4. Controllers

### app/Http/Controllers/LeafletController.php (Publik)

```php
<?php

namespace App\Http\Controllers;

use App\Models\LeafletKategori;

class LeafletController extends Controller
{
    public function index()
    {
        // Load kategori + items aktif, pisah per tipe
        $leaflets = LeafletKategori::with('items')
            ->aktif()->tipe('Leaflet')
            ->orderBy('urutan')->get();

        $posters = LeafletKategori::with('items')
            ->aktif()->tipe('Poster')
            ->orderBy('urutan')->get();

        return view('leaflet.index', compact('leaflets', 'posters'));
    }
}
```

### app/Http/Controllers/Admin/LeafletController.php (Admin)

```php
<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeafletKategori;
use App\Models\LeafletItem;
use Illuminate\Http\Request;

class LeafletController extends Controller
{
    // =================== INDEX ===================
    public function index(Request $request)
    {
        $tipe = $request->input('tipe', 'Leaflet');

        $kategoris = LeafletKategori::with('allItems')
            ->when($tipe, fn($q) => $q->tipe($tipe))
            ->orderBy('urutan')
            ->get();

        $countLeaflet = LeafletKategori::tipe('Leaflet')->count();
        $countPoster  = LeafletKategori::tipe('Poster')->count();

        return view('admin.leaflet.index', compact('kategoris', 'tipe', 'countLeaflet', 'countPoster'));
    }

    // =================== KATEGORI ===================
    public function createKategori()
    {
        return view('admin.leaflet.kategori-form', ['kategori' => null]);
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'tipe'   => 'required|in:Leaflet,Poster',
            'nama'   => 'required|string|max:150',
            'urutan' => 'nullable|integer',
        ]);

        LeafletKategori::create([
            'tipe'   => $request->tipe,
            'nama'   => strtoupper(trim($request->nama)), // uppercase seperti referensi
            'urutan' => $request->urutan ?? 0,
            'aktif'  => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.leaflet.index', ['tipe' => $request->tipe])
                         ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori(int $id)
    {
        $kategori = LeafletKategori::findOrFail($id);
        return view('admin.leaflet.kategori-form', compact('kategori'));
    }

    public function updateKategori(Request $request, int $id)
    {
        $kategori = LeafletKategori::findOrFail($id);
        $request->validate([
            'tipe'   => 'required|in:Leaflet,Poster',
            'nama'   => 'required|string|max:150',
            'urutan' => 'nullable|integer',
        ]);

        $kategori->update([
            'tipe'   => $request->tipe,
            'nama'   => strtoupper(trim($request->nama)),
            'urutan' => $request->urutan ?? 0,
            'aktif'  => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.leaflet.index', ['tipe' => $request->tipe])
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(int $id)
    {
        // Akan cascade delete semua items di bawahnya
        LeafletKategori::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori dan semua isinya berhasil dihapus.');
    }

    public function toggleKategori(int $id)
    {
        $k = LeafletKategori::findOrFail($id);
        $k->update(['aktif' => !$k->aktif]);
        return redirect()->back()->with('success', 'Status kategori diperbarui.');
    }

    // =================== ITEM ===================
    public function createItem(Request $request)
    {
        // Ambil kategori_id dari query string jika ada (shortcut dari tombol di index)
        $kategoris    = LeafletKategori::orderBy('tipe')->orderBy('urutan')->get();
        $selectedKat  = $request->input('kategori_id');
        return view('admin.leaflet.item-form', ['item' => null, 'kategoris' => $kategoris, 'selectedKat' => $selectedKat]);
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:rssite_leaflet_kategoris,id',
            'nama'        => 'required|string|max:255',
            'url_gdrive'  => 'required|url|max:2000',
            'deskripsi'   => 'nullable|string|max:255',
            'urutan'      => 'nullable|integer',
        ]);

        LeafletItem::create([
            'kategori_id' => $request->kategori_id,
            'nama'        => $request->nama,
            'url_gdrive'  => $request->url_gdrive,
            'deskripsi'   => $request->deskripsi,
            'urutan'      => $request->urutan ?? 0,
            'aktif'       => $request->boolean('aktif', true),
        ]);

        $tipe = LeafletKategori::find($request->kategori_id)?->tipe ?? 'Leaflet';
        return redirect()->route('admin.leaflet.index', ['tipe' => $tipe])
                         ->with('success', 'Item berhasil ditambahkan.');
    }

    public function editItem(int $id)
    {
        $item      = LeafletItem::findOrFail($id);
        $kategoris = LeafletKategori::orderBy('tipe')->orderBy('urutan')->get();
        return view('admin.leaflet.item-form', compact('item', 'kategoris'));
    }

    public function updateItem(Request $request, int $id)
    {
        $item = LeafletItem::findOrFail($id);
        $request->validate([
            'kategori_id' => 'required|exists:rssite_leaflet_kategoris,id',
            'nama'        => 'required|string|max:255',
            'url_gdrive'  => 'required|url|max:2000',
            'deskripsi'   => 'nullable|string|max:255',
            'urutan'      => 'nullable|integer',
        ]);

        $item->update([
            'kategori_id' => $request->kategori_id,
            'nama'        => $request->nama,
            'url_gdrive'  => $request->url_gdrive,
            'deskripsi'   => $request->deskripsi,
            'urutan'      => $request->urutan ?? 0,
            'aktif'       => $request->boolean('aktif', true),
        ]);

        $tipe = $item->kategori?->tipe ?? 'Leaflet';
        return redirect()->route('admin.leaflet.index', ['tipe' => $tipe])
                         ->with('success', 'Item berhasil diperbarui.');
    }

    public function destroyItem(int $id)
    {
        LeafletItem::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Item berhasil dihapus.');
    }

    public function toggleItem(int $id)
    {
        $item = LeafletItem::findOrFail($id);
        $item->update(['aktif' => !$item->aktif]);
        return redirect()->back()->with('success', 'Status item diperbarui.');
    }
}
```

---

## 5. Views Publik

### resources/views/leaflet/index.blade.php

```blade
@extends('layouts.app')

@section('title', 'Leaflet & Poster — RS TK IV Sintang')

@section('content')

{{-- Page Header --}}
@include('partials.page-header', ['judul' => 'Leaflet & Poster'])

<section class="py-14 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl">

    {{-- Layout 2 kolom: ilustrasi kiri, konten kanan --}}
    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">

      {{-- ===== KIRI: Ilustrasi (lg:col-span-2) ===== --}}
      <div class="lg:col-span-2 flex items-center justify-center">
        {{-- Ilustrasi SVG inline: orang + kaca pembesar (mirip referensi) --}}
        <div class="relative w-full max-w-xs">

          {{-- Card putih dekoratif latar --}}
          <div class="bg-white rounded-3xl shadow-lg p-8 flex items-end justify-center"
               style="min-height: 320px;">

            {{-- SVG Ilustrasi: Orang mencari dokumen --}}
            <svg viewBox="0 0 300 320" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-[260px]">
              <!-- Kaca pembesar -->
              <circle cx="200" cy="140" r="65" fill="none" stroke="#2D6A4F" stroke-width="14"/>
              <line x1="245" y1="192" x2="280" y2="230" stroke="#2D6A4F" stroke-width="14" stroke-linecap="round"/>
              <!-- Ikon dokumen di dalam kaca -->
              <rect x="168" y="108" width="64" height="64" rx="6" fill="#E8F5E9"/>
              <line x1="180" y1="125" x2="220" y2="125" stroke="#2D6A4F" stroke-width="4" stroke-linecap="round"/>
              <line x1="180" y1="138" x2="220" y2="138" stroke="#2D6A4F" stroke-width="4" stroke-linecap="round"/>
              <line x1="180" y1="151" x2="205" y2="151" stroke="#C9A84C" stroke-width="4" stroke-linecap="round"/>
              <!-- Orang -->
              <!-- Kepala -->
              <circle cx="100" cy="80" r="28" fill="#FDDCB5"/>
              <!-- Badan -->
              <rect x="75" y="110" width="50" height="70" rx="8" fill="#E8E8E8"/>
              <!-- Kaki kiri -->
              <rect x="80" y="176" width="18" height="55" rx="8" fill="#333"/>
              <!-- Kaki kanan -->
              <rect x="102" y="176" width="18" height="55" rx="8" fill="#333"/>
              <!-- Sepatu kiri -->
              <ellipse cx="89" cy="231" rx="14" ry="7" fill="#222"/>
              <!-- Sepatu kanan -->
              <ellipse cx="111" cy="231" rx="14" ry="7" fill="#222"/>
              <!-- Lengan kanan (mengarah ke kaca) -->
              <line x1="125" y1="130" x2="168" y2="155" stroke="#E8E8E8" stroke-width="16" stroke-linecap="round"/>
              <!-- Rambut -->
              <ellipse cx="100" cy="60" rx="28" ry="18" fill="#5D4037"/>
            </svg>

          </div>

          {{-- Badge kecil dekoratif --}}
          <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-gradient-to-br
                      from-green-700 to-green-500 rounded-2xl opacity-20"></div>
          <div class="absolute -top-4 -left-4 w-12 h-12 bg-yellow-400 rounded-xl opacity-30"></div>

        </div>
      </div>

      {{-- ===== KANAN: Tab + Accordion (lg:col-span-3) ===== --}}
      <div class="lg:col-span-3"
           x-data="{ activeTab: 'Leaflet', openAccordion: null }">

        {{-- Tab Leaflet | Poster --}}
        <div class="flex border-b border-gray-200 mb-0">

          <button @click="activeTab = 'Leaflet'; openAccordion = null"
                  :class="activeTab === 'Leaflet'
                    ? 'border-b-2 border-green-700 text-green-700 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                  class="px-6 py-3 text-sm transition-colors focus:outline-none">
            Leaflet
          </button>

          <button @click="activeTab = 'Poster'; openAccordion = null"
                  :class="activeTab === 'Poster'
                    ? 'border-b-2 border-green-700 text-green-700 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                  class="px-6 py-3 text-sm transition-colors focus:outline-none">
            Poster
          </button>

        </div>

        {{-- Panel: LEAFLET --}}
        <div x-show="activeTab === 'Leaflet'" x-transition>
          <div class="border border-gray-200 rounded-b-xl rounded-tr-xl overflow-hidden bg-white shadow-sm">

            @forelse($leaflets as $kat)

              {{-- Accordion Item --}}
              <div class="border-b border-gray-100 last:border-b-0">

                {{-- Header accordion --}}
                <button type="button"
                        @click="openAccordion = (openAccordion === 'L{{ $kat->id }}') ? null : 'L{{ $kat->id }}'"
                        class="w-full flex items-center justify-between px-6 py-4
                               text-left text-sm font-semibold text-gray-700
                               hover:bg-green-50 hover:text-green-700 transition-colors">
                  <span>{{ $kat->nama }}</span>
                  <i class="fa fa-chevron-down text-xs transition-transform duration-200"
                     :class="openAccordion === 'L{{ $kat->id }}' ? 'rotate-180 text-green-700' : ''"></i>
                </button>

                {{-- Konten accordion --}}
                <div x-show="openAccordion === 'L{{ $kat->id }}'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-gray-50 border-t border-gray-100">

                  @if($kat->items->isEmpty())
                    <p class="px-6 py-4 text-sm text-gray-400 italic">Belum ada item dalam kategori ini.</p>
                  @else
                    <ul class="divide-y divide-gray-100">
                      @foreach($kat->items as $item)
                        <li class="flex items-center justify-between px-6 py-3 hover:bg-white transition-colors group">

                          <div class="flex items-center gap-3 flex-1 min-w-0">
                            {{-- Ikon PDF/dokumen --}}
                            <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                              <i class="fa fa-file-pdf text-red-500 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                              <p class="text-sm text-gray-700 font-medium group-hover:text-green-700
                                        transition-colors truncate">
                                {{ $item->nama }}
                              </p>
                              @if($item->deskripsi)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->deskripsi }}</p>
                              @endif
                            </div>
                          </div>

                          {{-- Tombol aksi --}}
                          <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                            {{-- Lihat / Preview --}}
                            <a href="{{ $item->url_open }}" target="_blank" rel="noopener noreferrer"
                               title="Buka di Google Drive"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">
                              <i class="fa fa-eye"></i>
                              <span class="hidden sm:inline">Lihat</span>
                            </a>
                            {{-- Download --}}
                            <a href="{{ str_replace('/view', '/download?usp=sharing', $item->url_open) }}"
                               target="_blank" rel="noopener noreferrer"
                               title="Download"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                              <i class="fa fa-download"></i>
                              <span class="hidden sm:inline">Unduh</span>
                            </a>
                          </div>

                        </li>
                      @endforeach
                    </ul>
                  @endif

                </div>
              </div>

            @empty
              <div class="py-16 text-center text-gray-400">
                <i class="fa fa-folder-open text-5xl mb-3 block"></i>
                <p class="text-sm">Belum ada leaflet yang tersedia.</p>
              </div>
            @endforelse

          </div>
        </div>

        {{-- Panel: POSTER (struktur sama dengan Leaflet) --}}
        <div x-show="activeTab === 'Poster'" x-transition>
          <div class="border border-gray-200 rounded-b-xl rounded-tr-xl overflow-hidden bg-white shadow-sm">

            @forelse($posters as $kat)
              <div class="border-b border-gray-100 last:border-b-0">
                <button type="button"
                        @click="openAccordion = (openAccordion === 'P{{ $kat->id }}') ? null : 'P{{ $kat->id }}'"
                        class="w-full flex items-center justify-between px-6 py-4
                               text-left text-sm font-semibold text-gray-700
                               hover:bg-green-50 hover:text-green-700 transition-colors">
                  <span>{{ $kat->nama }}</span>
                  <i class="fa fa-chevron-down text-xs transition-transform duration-200"
                     :class="openAccordion === 'P{{ $kat->id }}' ? 'rotate-180 text-green-700' : ''"></i>
                </button>

                <div x-show="openAccordion === 'P{{ $kat->id }}'"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 -translate-y-2"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     class="bg-gray-50 border-t border-gray-100">

                  @if($kat->items->isEmpty())
                    <p class="px-6 py-4 text-sm text-gray-400 italic">Belum ada item dalam kategori ini.</p>
                  @else
                    <ul class="divide-y divide-gray-100">
                      @foreach($kat->items as $item)
                        <li class="flex items-center justify-between px-6 py-3 hover:bg-white transition-colors group">
                          <div class="flex items-center gap-3 flex-1 min-w-0">
                            <div class="flex-shrink-0 w-8 h-8 bg-blue-100 rounded-lg flex items-center justify-center">
                              <i class="fa fa-image text-blue-500 text-sm"></i>
                            </div>
                            <div class="min-w-0">
                              <p class="text-sm text-gray-700 font-medium group-hover:text-green-700 transition-colors truncate">
                                {{ $item->nama }}
                              </p>
                              @if($item->deskripsi)
                                <p class="text-xs text-gray-400 mt-0.5">{{ $item->deskripsi }}</p>
                              @endif
                            </div>
                          </div>
                          <div class="flex items-center gap-2 flex-shrink-0 ml-4">
                            <a href="{{ $item->url_open }}" target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition-colors">
                              <i class="fa fa-eye"></i>
                              <span class="hidden sm:inline">Lihat</span>
                            </a>
                            <a href="{{ str_replace('/view', '/download?usp=sharing', $item->url_open) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                              <i class="fa fa-download"></i>
                              <span class="hidden sm:inline">Unduh</span>
                            </a>
                          </div>
                        </li>
                      @endforeach
                    </ul>
                  @endif

                </div>
              </div>
            @empty
              <div class="py-16 text-center text-gray-400">
                <i class="fa fa-folder-open text-5xl mb-3 block"></i>
                <p class="text-sm">Belum ada poster yang tersedia.</p>
              </div>
            @endforelse

          </div>
        </div>

      </div>{{-- end kanan --}}
    </div>{{-- end grid --}}

  </div>
</section>

@endsection
```

---

## 6. Views Admin

### resources/views/admin/leaflet/index.blade.php

```
@extends('layouts.admin')
@section('title', 'Kelola Leaflet & Poster')
@section('content')

[Header]:
- Judul "Leaflet & Poster"
- Tombol kanan: "Tambah Kategori" + "Tambah Item"

[Tab Filter Leaflet | Poster]:
- Link ?tipe=Leaflet / ?tipe=Poster dengan badge count
- Active tab highlight

[Untuk setiap $kategoris]:
  Card putih rounded-xl shadow-sm mb-4:

  Header card (flex between):
  - Kiri: badge tipe (Leaflet/Poster), nama kategori (bold), urutan kecil
  - Kanan: tombol [+ Item] [Edit] [Toggle] [Hapus]

  Tabel items di dalam card:
  - Kolom: No | Nama | Deskripsi | URL GDrive (potong 50 char + link ikon) | Urutan | Status | Aksi
  - Setiap baris aksi: [Edit] [Toggle] [Hapus]
  - Baris tombol: link icon "buka" ke url_gdrive (target blank)
  - Jika items kosong: row "Belum ada item" + tombol "+ Tambah Item Pertama"

  Konfirmasi hapus via onclick confirm() JS standar

@endsection
```

### resources/views/admin/leaflet/kategori-form.blade.php

```
@extends('layouts.admin')
@section('title', $kategori ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')

Card putih p-6 max-w-lg mx-auto:

Form action: $kategori ? route('admin.leaflet.kategori.update', $kategori) PUT
                       : route('admin.leaflet.kategori.store') POST

Field:
1. Tipe (radio button besar):
   [ Leaflet ]   [ Poster ]
   — checked sesuai $kategori->tipe atau default Leaflet

2. Nama Kategori* (text, uppercase otomatis via CSS text-transform: uppercase)
   placeholder: "PENYAKIT DALAM"
   helper: "Nama akan otomatis ditampilkan kapital semua"

3. Urutan (number, default 0)
   helper: "Semakin kecil angka, semakin atas posisinya"

4. Status (toggle checkbox aktif)

Tombol: Simpan | Batal

@endsection
```

### resources/views/admin/leaflet/item-form.blade.php

```
@extends('layouts.admin')
@section('title', $item ? 'Edit Item' : 'Tambah Item')
@section('content')

Card putih p-6 max-w-2xl mx-auto:

Form action: $item ? route('admin.leaflet.item.update', $item) PUT
                   : route('admin.leaflet.item.store') POST

Field:
1. Kategori* (select dropdown)
   — Grouped by tipe: optgroup "Leaflet" dan optgroup "Poster"
   — Selected: $selectedKat atau $item->kategori_id
   — Loop: @foreach($kategoris->groupBy('tipe') as $tipe => $kats)
             <optgroup label="{{ $tipe }}">
               @foreach($kats as $k)
                 <option value="{{ $k->id }}">{{ $k->nama }}</option>
               @endforeach
             </optgroup>
           @endforeach

2. Nama Item* (text)
   placeholder: "Mengenal Diabetes Melitus"

3. URL Google Drive* (url input)
   placeholder: "https://drive.google.com/file/d/xxxx/view?usp=sharing"
   helper: "Tempel link share Google Drive. Pastikan akses sudah diset 'Anyone with the link can view'"
   — Jika ada $item->url_gdrive, tampilkan tombol "Buka di GDrive" di samping field (link ikon)

4. Deskripsi (text, opsional)
   placeholder: "Keterangan singkat tentang konten file ini"

5. Urutan (number, default 0)

6. Status (toggle checkbox aktif)

Tombol: Simpan | Batal

CATATAN PENTING — tampilkan alert info box di bawah field URL:
<div class="bg-blue-50 border border-blue-200 rounded-lg p-4 text-sm text-blue-700 mt-2">
  <strong><i class="fa fa-info-circle"></i> Cara mendapatkan link Google Drive:</strong>
  <ol class="mt-2 ml-4 space-y-1 list-decimal">
    <li>Buka Google Drive, klik kanan file → "Get link"</li>
    <li>Set akses ke <strong>"Anyone with the link"</strong></li>
    <li>Klik "Copy link" lalu tempel di kolom di atas</li>
  </ol>
</div>

@endsection
```

---

## 7. Seeder — Data Contoh

Di `DatabaseSeeder.php`, tambahkan:

```php
// Leaflet Kategoris + Items
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
    $kat = \App\Models\LeafletKategori::firstOrCreate(
        ['nama' => $data['nama'], 'tipe' => $data['tipe']],
        ['urutan' => $data['urutan'], 'aktif' => true]
    );
    foreach ($data['items'] as $i => $itemData) {
        \App\Models\LeafletItem::firstOrCreate(
            ['kategori_id' => $kat->id, 'nama' => $itemData['nama']],
            ['url_gdrive' => $itemData['url_gdrive'], 'deskripsi' => $itemData['deskripsi'],
             'urutan' => $i + 1, 'aktif' => true]
        );
    }
}
```

---

## 8. Update Navbar

Di `partials/navbar.blade.php`, tambahkan link "Leaflet & Poster":

```blade
<a href="{{ route('leaflet.index') }}"
   class="font-medium transition-colors
          {{ request()->routeIs('leaflet.*') ? 'text-green-700 font-semibold' : 'text-gray-700 hover:text-green-700' }}">
  Leaflet & Poster
</a>
```

Atau jika ingin masuk ke dalam dropdown menu (misalnya di bawah "Berita"):
```blade
<a href="{{ route('leaflet.index') }}"
   class="flex items-center gap-3 px-4 py-3 hover:bg-green-50 hover:text-green-700 transition-colors
          {{ request()->routeIs('leaflet.*') ? 'text-green-700 bg-green-50 font-medium' : 'text-gray-700' }}">
  <i class="fa fa-file-medical w-5 text-green-600"></i>
  Leaflet & Poster
</a>
```

---

## 9. Update Sidebar Admin

Di `layouts/admin.blade.php`, tambahkan di grup Konten:

```blade
<a href="{{ route('admin.leaflet.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.leaflet.*') ? 'active' : '' }}">
  <i class="fa fa-file-medical w-5"></i>
  <span>Leaflet & Poster</span>
</a>
```

---

## Catatan Teknis: Google Drive URL

Accessor di model `LeafletItem` menangani berbagai format URL Google Drive:

| Format URL Input (dari admin)                              | Output `url_open`                              |
|------------------------------------------------------------|------------------------------------------------|
| `https://drive.google.com/file/d/FILE_ID/view?usp=sharing`| `https://drive.google.com/file/d/FILE_ID/view` |
| `https://drive.google.com/file/d/FILE_ID/view`            | sama                                           |
| `https://drive.google.com/open?id=FILE_ID`                | ekstrak ID → format standar                    |
| URL lain (tidak dikenali)                                  | dikembalikan apa adanya (fallback)             |

Tombol "Unduh" mengubah `/view` → `/download?usp=sharing` agar browser langsung trigger download.

**Syarat agar link berfungsi untuk visitor:** File di Google Drive harus diset
sharing ke **"Anyone with the link can view"**. Admin harus melakukan ini sebelum memasukkan URL ke sistem.

---

## Checklist Akhir

- [ ] Migration `rssite_leaflet_kategoris` berjalan tanpa error
- [ ] Migration `rssite_leaflet_items` berjalan, foreign key ke kategoris benar
- [ ] Seeder mengisi data contoh (minimal 3 kategori leaflet + 2 kategori poster)
- [ ] Route `leaflet.index` → `/leaflet-poster` accessible (HTTP 200)
- [ ] Halaman publik tampil dengan layout 2 kolom (ilustrasi kiri, tab+accordion kanan)
- [ ] Tab "Leaflet" dan "Poster" berfungsi switch konten
- [ ] Accordion expand/collapse dengan animasi smooth (Alpine.js)
- [ ] Hanya satu accordion terbuka dalam satu waktu per tab
- [ ] Ikon dokumen PDF (merah) untuk Leaflet, ikon gambar (biru) untuk Poster
- [ ] Tombol "Lihat" membuka Google Drive di tab baru
- [ ] Tombol "Unduh" trigger download dari Google Drive
- [ ] Accessor `url_open` mengekstrak file ID dengan benar dari URL GDrive
- [ ] Jika kategori tidak punya item → pesan "Belum ada item" dalam accordion
- [ ] Jika tidak ada kategori sama sekali → empty state dengan ikon folder
- [ ] Navbar link "Leaflet & Poster" highlight saat halaman aktif
- [ ] Admin `/admin/leaflet` bisa diakses (auth protected)
- [ ] Tab filter Leaflet/Poster di admin berfungsi via query string `?tipe=`
- [ ] Tambah kategori berfungsi (nama auto-uppercase tersimpan)
- [ ] Edit kategori load data existing
- [ ] Toggle aktif/non-aktif kategori berfungsi
- [ ] Hapus kategori cascade delete items-nya
- [ ] Tambah item: dropdown kategori grouped by tipe
- [ ] Info box "Cara mendapatkan link Google Drive" tampil di form item
- [ ] Edit item load URL GDrive existing + tampilkan tombol "Buka di GDrive"
- [ ] Toggle aktif/non-aktif item berfungsi
- [ ] Hapus item berfungsi
- [ ] Sidebar admin "Leaflet & Poster" highlight saat aktif
- [ ] Mobile responsive: accordion full width, tombol lihat/unduh tetap terlihat
