# Task: Halaman Berita — List, Filter, Pagination & Detail

Tambahkan halaman publik **Berita & Event** ke project website RS TK IV Sintang.
Referensi tampilan: screenshot halaman Berita RSI PKU Muhammadiyah Tegal.

Konteks project: lihat `CLAUDE.md` (struktur Laravel, model, tabel yang sudah ada).
Model `Berita` dan tabel `rssite_beritas` sudah ada — task ini hanya menambah halaman publik
list berita + detail berita + update navbar.

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan / Diubah                                           |
|-------------|---------------------------------------------------------------------|
| Routes      | `/berita` (list) sudah ada di HomeController — pindah ke BeritaController publik |
| Controller  | `BeritaController@index` (list + filter + pagination) + `@show` (detail) |
| Views       | `berita/index.blade.php`, `berita/show.blade.php`, `partials/berita-card.blade.php` |
| Navbar      | Link "Berita" → `route('berita.index')`                            |

---

## 1. Routes (routes/web.php)

Ganti/tambahkan route berita publik (hapus yang lama jika ada di HomeController):

```php
// Berita Publik
Route::get('/berita',          [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}',   [BeritaController::class, 'show'])->name('berita.show');
```

Pastikan `use App\Http\Controllers\BeritaController;` ada di atas routes/web.php.

---

## 2. Controller (app/Http/Controllers/BeritaController.php)

Buat file baru ini (BUKAN yang di Admin — ini controller publik):

```php
<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::where('aktif', true)->latest();

        // Filter kategori jika ada query ?kategori=Berita
        if ($request->filled('kategori') && in_array($request->kategori, ['Berita', 'Pengumuman', 'Kegiatan'])) {
            $query->where('kategori', $request->kategori);
        }

        // Search judul jika ada query ?q=...
        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $beritas   = $query->paginate(9)->withQueryString();
        $kategori  = $request->input('kategori', '');
        $search    = $request->input('q', '');

        // Jumlah per kategori untuk badge filter
        $countAll         = Berita::where('aktif', true)->count();
        $countBerita      = Berita::where('aktif', true)->where('kategori', 'Berita')->count();
        $countPengumuman  = Berita::where('aktif', true)->where('kategori', 'Pengumuman')->count();
        $countKegiatan    = Berita::where('aktif', true)->where('kategori', 'Kegiatan')->count();

        return view('berita.index', compact(
            'beritas', 'kategori', 'search',
            'countAll', 'countBerita', 'countPengumuman', 'countKegiatan'
        ));
    }

    public function show(string $slug)
    {
        $berita = Berita::where('slug', $slug)->where('aktif', true)->firstOrFail();

        // Increment views
        $berita->increment('views');

        // Berita terkait (kategori sama, exclude current, max 3)
        $terkait = Berita::where('aktif', true)
            ->where('kategori', $berita->kategori)
            ->where('id', '!=', $berita->id)
            ->latest()
            ->limit(3)
            ->get();

        // Berita terbaru untuk sidebar (5 teratas)
        $terbaru = Berita::where('aktif', true)
            ->where('id', '!=', $berita->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('berita.show', compact('berita', 'terkait', 'terbaru'));
    }
}
```

---

## 3. Model Berita — Pastikan Ada

Verifikasi `app/Models/Berita.php` sudah memiliki:

```php
protected $table = 'rssite_beritas';

protected $fillable = [
    'judul', 'slug', 'kategori', 'konten', 'gambar',
    'penulis', 'views', 'aktif',
];

protected $casts = [
    'aktif' => 'boolean',
];

// Accessor: ringkasan konten (strip HTML, 150 karakter)
public function getRingkasanAttribute(): string
{
    return mb_substr(strip_tags($this->konten), 0, 150) . '...';
}

// Accessor: URL gambar atau null
public function getGambarUrlAttribute(): ?string
{
    return $this->gambar ? asset('storage/' . $this->gambar) : null;
}
```

---

## 4. Views

### 4a. resources/views/berita/index.blade.php

```blade
@extends('layouts.app')

@section('title', 'Berita & Event — RS TK IV Sintang')
@section('meta_description', 'Informasi dan berita terkini dari RS TK IV Sintang, Kalimantan Barat.')

@section('content')

{{-- PAGE HEADER --}}
@include('partials.page-header', ['judul' => 'Berita & Event', 'parent' => null])

{{-- MAIN CONTENT --}}
<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-7xl">

    {{-- ===== FILTER & SEARCH BAR ===== --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-10">

      {{-- Filter Kategori (pill tabs) --}}
      <div class="flex flex-wrap gap-2">

        {{-- Semua --}}
        <a href="{{ route('berita.index', array_merge(request()->except('kategori', 'page'), ['q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === '' ? 'bg-green-700 text-white border-green-700 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-green-600 hover:text-green-700' }}">
          Semua
          <span class="ml-1 text-xs opacity-75">({{ $countAll }})</span>
        </a>

        {{-- Berita --}}
        <a href="{{ route('berita.index', array_merge(request()->except('page'), ['kategori' => 'Berita', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Berita' ? 'bg-green-700 text-white border-green-700 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-green-600 hover:text-green-700' }}">
          Berita
          <span class="ml-1 text-xs opacity-75">({{ $countBerita }})</span>
        </a>

        {{-- Pengumuman --}}
        <a href="{{ route('berita.index', array_merge(request()->except('page'), ['kategori' => 'Pengumuman', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Pengumuman' ? 'bg-yellow-500 text-white border-yellow-500 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-yellow-500 hover:text-yellow-600' }}">
          Pengumuman
          <span class="ml-1 text-xs opacity-75">({{ $countPengumuman }})</span>
        </a>

        {{-- Kegiatan --}}
        <a href="{{ route('berita.index', array_merge(request()->except('page'), ['kategori' => 'Kegiatan', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Kegiatan' ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-500 hover:text-blue-600' }}">
          Kegiatan
          <span class="ml-1 text-xs opacity-75">({{ $countKegiatan }})</span>
        </a>

      </div>

      {{-- Search box --}}
      <form method="GET" action="{{ route('berita.index') }}" class="flex gap-2 w-full md:w-auto">
        @if($kategori)
          <input type="hidden" name="kategori" value="{{ $kategori }}">
        @endif
        <input type="text" name="q" value="{{ $search }}"
               placeholder="Cari berita..."
               class="border border-gray-300 rounded-full px-4 py-2 text-sm focus:outline-none focus:border-green-500 w-full md:w-60">
        <button type="submit"
                class="bg-green-700 text-white px-5 py-2 rounded-full text-sm hover:bg-green-800 transition-colors">
          <i class="fa fa-search"></i>
        </button>
      </form>

    </div>

    {{-- ===== GRID BERITA ===== --}}
    @if($beritas->isEmpty())

      {{-- Empty state --}}
      <div class="text-center py-24">
        <i class="fa fa-newspaper text-6xl text-gray-300 mb-4"></i>
        <p class="text-xl text-gray-400 font-medium">Belum ada berita</p>
        @if($search || $kategori)
          <p class="text-gray-400 text-sm mt-2">Coba kata kunci atau kategori lain</p>
          <a href="{{ route('berita.index') }}" class="mt-4 inline-block text-green-600 hover:underline text-sm">
            ← Lihat semua berita
          </a>
        @endif
      </div>

    @else

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-7">
        @foreach($beritas as $item)
          @include('partials.berita-card', ['item' => $item])
        @endforeach
      </div>

      {{-- ===== PAGINATION ===== --}}
      @if($beritas->hasPages())
        <div class="mt-12 flex justify-center">
          <nav class="flex items-center gap-1">

            {{-- Prev --}}
            @if($beritas->onFirstPage())
              <span class="px-3 py-2 rounded text-gray-300 border border-gray-200 cursor-not-allowed text-sm">«</span>
            @else
              <a href="{{ $beritas->previousPageUrl() }}"
                 class="px-3 py-2 rounded text-gray-600 border border-gray-300 hover:bg-green-700 hover:text-white hover:border-green-700 transition-all text-sm">«</a>
            @endif

            {{-- Page numbers --}}
            @foreach($beritas->getUrlRange(1, $beritas->lastPage()) as $page => $url)
              @if($page == $beritas->currentPage())
                <span class="px-4 py-2 rounded bg-green-700 text-white text-sm font-semibold border border-green-700">{{ $page }}</span>
              @else
                <a href="{{ $url }}"
                   class="px-4 py-2 rounded text-gray-600 border border-gray-300 hover:bg-green-700 hover:text-white hover:border-green-700 transition-all text-sm">{{ $page }}</a>
              @endif
            @endforeach

            {{-- Next --}}
            @if($beritas->hasMorePages())
              <a href="{{ $beritas->nextPageUrl() }}"
                 class="px-3 py-2 rounded text-gray-600 border border-gray-300 hover:bg-green-700 hover:text-white hover:border-green-700 transition-all text-sm">»</a>
            @else
              <span class="px-3 py-2 rounded text-gray-300 border border-gray-200 cursor-not-allowed text-sm">»</span>
            @endif

          </nav>
        </div>

        {{-- Info jumlah: "Menampilkan 1–9 dari 27 berita" --}}
        <p class="text-center text-gray-400 text-sm mt-4">
          Menampilkan {{ $beritas->firstItem() }}–{{ $beritas->lastItem() }} dari {{ $beritas->total() }} berita
        </p>
      @endif

    @endif

  </div>
</section>

@endsection
```

---

### 4b. resources/views/partials/berita-card.blade.php

Partial card berita — dipakai di halaman list dan juga bisa di home:

```blade
<article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300
                hover:-translate-y-1 overflow-hidden group reveal">

  {{-- Thumbnail --}}
  <a href="{{ route('berita.show', $item->slug) }}" class="block overflow-hidden h-52 relative">

    @if($item->gambar_url)
      <img src="{{ $item->gambar_url }}"
           alt="{{ $item->judul }}"
           class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
    @else
      {{-- Gradient placeholder berdasarkan kategori --}}
      <div class="w-full h-full flex items-center justify-center
        @if($item->kategori === 'Berita') bg-gradient-to-br from-green-700 to-green-500
        @elseif($item->kategori === 'Pengumuman') bg-gradient-to-br from-yellow-600 to-yellow-400
        @else bg-gradient-to-br from-blue-700 to-blue-500
        @endif">
        <i class="fa fa-newspaper text-5xl text-white opacity-40"></i>
      </div>
    @endif

    {{-- Badge kategori di atas gambar (pojok kiri bawah) --}}
    <span class="absolute bottom-3 left-3 text-xs font-semibold px-3 py-1 rounded-full
      @if($item->kategori === 'Berita') bg-green-700 text-white
      @elseif($item->kategori === 'Pengumuman') bg-yellow-500 text-white
      @else bg-blue-600 text-white
      @endif">
      {{ $item->kategori }}
    </span>

  </a>

  {{-- Konten card --}}
  <div class="p-5">

    {{-- Tanggal posting --}}
    <p class="text-xs text-gray-400 mb-2">
      <i class="fa fa-calendar-alt mr-1"></i>
      Diposting : {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
    </p>

    {{-- Judul --}}
    <h3 class="font-playfair font-semibold text-gray-800 text-base leading-snug mb-3
               group-hover:text-green-700 transition-colors line-clamp-3">
      <a href="{{ route('berita.show', $item->slug) }}">{{ $item->judul }}</a>
    </h3>

    {{-- Ringkasan --}}
    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4">
      {{ $item->ringkasan }}
    </p>

    {{-- Footer card: penulis + link --}}
    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
      <span class="text-xs text-gray-400">
        <i class="fa fa-user-circle mr-1"></i>{{ $item->penulis }}
      </span>
      <a href="{{ route('berita.show', $item->slug) }}"
         class="text-xs font-semibold text-green-700 hover:text-green-900 transition-colors">
        Baca selengkapnya →
      </a>
    </div>

  </div>
</article>
```

---

### 4c. resources/views/berita/show.blade.php

Halaman detail berita dengan sidebar:

```blade
@extends('layouts.app')

@section('title', $berita->judul . ' — RS TK IV Sintang')
@section('meta_description', $berita->ringkasan)

@section('content')

{{-- Page Header --}}
@include('partials.page-header', ['judul' => 'Berita & Event', 'parent' => null])

<section class="py-12 bg-gray-50">
  <div class="container mx-auto px-4 max-w-7xl">

    {{-- Layout: Artikel Kiri (2/3) + Sidebar Kanan (1/3) --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      {{-- ===== ARTIKEL UTAMA ===== --}}
      <article class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

          {{-- Thumbnail besar --}}
          @if($berita->gambar_url)
            <img src="{{ $berita->gambar_url }}"
                 alt="{{ $berita->judul }}"
                 class="w-full h-80 object-cover">
          @else
            <div class="w-full h-64 flex items-center justify-center
              @if($berita->kategori === 'Berita') bg-gradient-to-br from-green-700 to-green-500
              @elseif($berita->kategori === 'Pengumuman') bg-gradient-to-br from-yellow-600 to-yellow-400
              @else bg-gradient-to-br from-blue-700 to-blue-500
              @endif">
              <i class="fa fa-newspaper text-7xl text-white opacity-30"></i>
            </div>
          @endif

          <div class="p-8">

            {{-- Meta --}}
            <div class="flex flex-wrap items-center gap-3 mb-4">
              <span class="text-xs font-semibold px-3 py-1 rounded-full
                @if($berita->kategori === 'Berita') bg-green-100 text-green-700
                @elseif($berita->kategori === 'Pengumuman') bg-yellow-100 text-yellow-700
                @else bg-blue-100 text-blue-700
                @endif">
                {{ $berita->kategori }}
              </span>
              <span class="text-xs text-gray-400">
                <i class="fa fa-calendar-alt mr-1"></i>
                {{ \Carbon\Carbon::parse($berita->created_at)->translatedFormat('d F Y') }}
              </span>
              <span class="text-xs text-gray-400">
                <i class="fa fa-user-circle mr-1"></i>
                {{ $berita->penulis }}
              </span>
              <span class="text-xs text-gray-400 ml-auto">
                <i class="fa fa-eye mr-1"></i>
                {{ number_format($berita->views) }} kali dibaca
              </span>
            </div>

            {{-- Judul --}}
            <h1 class="font-playfair text-2xl md:text-3xl font-bold text-gray-800 leading-tight mb-6">
              {{ $berita->judul }}
            </h1>

            {{-- Garis pemisah emas --}}
            <div class="w-16 h-1 bg-yellow-500 rounded mb-6"></div>

            {{-- Konten HTML --}}
            <div class="prose prose-green max-w-none text-gray-700 leading-relaxed article-content">
              {!! $berita->konten !!}
            </div>

            {{-- Share buttons --}}
            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center gap-3 flex-wrap">
              <span class="text-sm text-gray-500 font-medium">Bagikan:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                 target="_blank"
                 class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full text-xs hover:bg-blue-700 transition-colors">
                <i class="fab fa-facebook-f"></i> Facebook
              </a>
              <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($berita->judul) }}"
                 target="_blank"
                 class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-full text-xs hover:bg-sky-600 transition-colors">
                <i class="fab fa-twitter"></i> Twitter
              </a>
              <a href="https://wa.me/?text={{ urlencode($berita->judul . ' ' . request()->url()) }}"
                 target="_blank"
                 class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-full text-xs hover:bg-green-600 transition-colors">
                <i class="fab fa-whatsapp"></i> WhatsApp
              </a>
            </div>

            {{-- Navigasi kembali --}}
            <div class="mt-6">
              <a href="{{ route('berita.index') }}"
                 class="inline-flex items-center gap-2 text-green-700 font-medium text-sm hover:text-green-900 transition-colors">
                <i class="fa fa-arrow-left"></i> Kembali ke Daftar Berita
              </a>
            </div>

          </div>
        </div>

        {{-- ===== BERITA TERKAIT ===== --}}
        @if($terkait->isNotEmpty())
          <div class="mt-8">
            <h2 class="font-playfair text-xl font-bold text-gray-800 mb-5">
              Berita Terkait
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-5">
              @foreach($terkait as $item)
                @include('partials.berita-card', ['item' => $item])
              @endforeach
            </div>
          </div>
        @endif

      </article>

      {{-- ===== SIDEBAR ===== --}}
      <aside class="lg:col-span-1 space-y-6">

        {{-- Berita Terbaru --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-playfair text-lg font-bold text-gray-800 mb-4 pb-3 border-b-2 border-green-700">
            Berita Terbaru
          </h3>
          <ul class="space-y-4">
            @foreach($terbaru as $item)
              <li class="flex gap-3 group">

                {{-- Thumbnail kecil --}}
                <a href="{{ route('berita.show', $item->slug) }}"
                   class="flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden">
                  @if($item->gambar_url)
                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                  @else
                    <div class="w-full h-full bg-gradient-to-br from-green-700 to-green-500
                                flex items-center justify-center">
                      <i class="fa fa-newspaper text-white text-lg opacity-60"></i>
                    </div>
                  @endif
                </a>

                {{-- Teks --}}
                <div class="flex-1 min-w-0">
                  <a href="{{ route('berita.show', $item->slug) }}"
                     class="text-sm font-medium text-gray-700 group-hover:text-green-700 transition-colors
                            leading-snug line-clamp-2 block">
                    {{ $item->judul }}
                  </a>
                  <p class="text-xs text-gray-400 mt-1">
                    {{ \Carbon\Carbon::parse($item->created_at)->format('d M Y') }}
                  </p>
                </div>

              </li>
            @endforeach
          </ul>
        </div>

        {{-- Filter Kategori --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-playfair text-lg font-bold text-gray-800 mb-4 pb-3 border-b-2 border-green-700">
            Kategori
          </h3>
          <ul class="space-y-2">
            <li>
              <a href="{{ route('berita.index') }}"
                 class="flex items-center justify-between px-3 py-2 rounded-lg text-sm
                        text-gray-600 hover:bg-green-50 hover:text-green-700 transition-colors">
                <span><i class="fa fa-newspaper mr-2 text-gray-400"></i> Semua Berita</span>
              </a>
            </li>
            @foreach(['Berita' => 'fa-rss', 'Pengumuman' => 'fa-bullhorn', 'Kegiatan' => 'fa-calendar-check'] as $kat => $icon)
              <li>
                <a href="{{ route('berita.index', ['kategori' => $kat]) }}"
                   class="flex items-center justify-between px-3 py-2 rounded-lg text-sm
                          {{ $berita->kategori === $kat ? 'bg-green-700 text-white' : 'text-gray-600 hover:bg-green-50 hover:text-green-700' }}
                          transition-colors">
                  <span><i class="fa {{ $icon }} mr-2 opacity-70"></i> {{ $kat }}</span>
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        {{-- CTA Hubungi --}}
        <div class="bg-gradient-to-br from-green-800 to-green-600 rounded-2xl p-6 text-white text-center">
          <i class="fa fa-phone-alt text-3xl mb-3 text-yellow-400"></i>
          <h3 class="font-playfair text-lg font-bold mb-2">Butuh Informasi?</h3>
          <p class="text-green-200 text-sm mb-4">Hubungi kami untuk informasi lebih lanjut</p>
          <a href="tel:{{ \App\Models\SiteSetting::get('telepon') }}"
             class="block bg-yellow-500 text-white font-semibold py-2 px-4 rounded-full text-sm
                    hover:bg-yellow-400 transition-colors">
            {{ \App\Models\SiteSetting::get('telepon', '(0565) 21021') }}
          </a>
        </div>

      </aside>

    </div>
  </div>
</section>

@endsection
```

---

## 5. CSS Tambahan — layouts/app.blade.php

Tambahkan ke blok `<style>` yang sudah ada:

```css
/* === Berita: line-clamp utility (fallback jika Tailwind CDN belum support) === */
.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
}
.line-clamp-3 {
    display: -webkit-box;
    -webkit-line-clamp: 3;
    -webkit-box-orient: vertical;
    overflow: hidden;
}

/* === Konten artikel (HTML dari DB) === */
.article-content p    { margin-bottom: 1rem; line-height: 1.8; }
.article-content h2   { font-family: 'Playfair Display', serif; font-size: 1.4rem; font-weight: 700; color: #1B4332; margin: 1.5rem 0 0.75rem; }
.article-content h3   { font-family: 'Playfair Display', serif; font-size: 1.2rem; font-weight: 600; color: #2D6A4F; margin: 1.25rem 0 0.5rem; }
.article-content ul,
.article-content ol   { padding-left: 1.5rem; margin-bottom: 1rem; }
.article-content li   { margin-bottom: 0.4rem; line-height: 1.7; }
.article-content img  { border-radius: 12px; max-width: 100%; height: auto; margin: 1rem 0; }
.article-content a    { color: #2D6A4F; text-decoration: underline; }
.article-content blockquote {
    border-left: 4px solid #C9A84C;
    padding: 0.75rem 1rem;
    margin: 1rem 0;
    background: #F8F9FA;
    font-style: italic;
    color: #555;
    border-radius: 0 8px 8px 0;
}

/* === Pagination custom (jika page count > 10, sembunyikan tengah) === */
```

---

## 6. Update Navbar

Di `resources/views/partials/navbar.blade.php` (atau di dalam `layouts/app.blade.php`),
ganti link Berita yang ada menjadi:

```blade
<a href="{{ route('berita.index') }}"
   class="font-medium transition-colors
          {{ request()->routeIs('berita.*') ? 'text-green-700 font-semibold' : 'text-gray-700 hover:text-green-700' }}">
  Berita
</a>
```

---

## 7. Update Home Page — Tombol "Lihat Semua Berita"

Di `resources/views/home.blade.php`, pada section Berita Terbaru, pastikan tombol sudah
menggunakan route yang benar:

```blade
<a href="{{ route('berita.index') }}"
   class="...">
  Lihat Semua Berita →
</a>
```

Dan setiap card berita di home juga link ke:
```blade
<a href="{{ route('berita.show', $item->slug) }}">...</a>
```

---

## 8. Seeder — Tambah Data Berita

Di `DatabaseSeeder.php`, pastikan ada minimal 12 berita agar pagination terlihat
(9 per halaman → butuh minimal 10 untuk muncul halaman 2).

Tambah berita contoh yang realistis untuk RS TK IV Sintang:

```php
$beritas = [
    [
        'judul'    => 'RS TK IV Sintang Gelar Bakti Sosial Kesehatan Gratis untuk Masyarakat Perbatasan',
        'slug'     => 'bakti-sosial-kesehatan-gratis-perbatasan',
        'kategori' => 'Kegiatan',
        'konten'   => '<p>RS TK IV Sintang menyelenggarakan bakti sosial kesehatan gratis bagi masyarakat di wilayah perbatasan Kabupaten Sintang. Kegiatan ini melibatkan puluhan tenaga medis dari berbagai spesialisasi.</p><p>Dalam kegiatan ini, masyarakat mendapat layanan pemeriksaan kesehatan umum, pengobatan gratis, serta konsultasi gizi secara cuma-cuma.</p>',
        'penulis'  => 'Humas RS TK IV Sintang',
        'aktif'    => true,
    ],
    [
        'judul'    => 'Pembukaan Poli Spesialis Saraf — RS TK IV Sintang Hadirkan Dokter SpS',
        'slug'     => 'pembukaan-poli-spesialis-saraf',
        'kategori' => 'Berita',
        'konten'   => '<p>RS TK IV Sintang resmi membuka Poli Spesialis Saraf dengan menghadirkan dokter spesialis saraf berpengalaman. Layanan ini hadir untuk memenuhi kebutuhan masyarakat Sintang yang selama ini harus merujuk ke kota lain.</p>',
        'penulis'  => 'Humas RS TK IV Sintang',
        'aktif'    => true,
    ],
    [
        'judul'    => 'Pengumuman: Perubahan Jam Pelayanan Poli Rawat Jalan Selama Ramadan',
        'slug'     => 'perubahan-jam-pelayanan-ramadan',
        'kategori' => 'Pengumuman',
        'konten'   => '<p>Dalam rangka menyambut bulan suci Ramadan, RS TK IV Sintang menyesuaikan jam operasional Poli Rawat Jalan. Perubahan berlaku mulai 1 Ramadan 1446 H.</p><ul><li>Poli Rawat Jalan: 07.30 – 12.00 WIB</li><li>IGD: Tetap 24 Jam</li><li>Apotek: 07.00 – 20.00 WIB</li></ul>',
        'penulis'  => 'Admin RS TK IV Sintang',
        'aktif'    => true,
    ],
    // ... tambahkan hingga total 12–15 berita dengan variasi kategori dan tanggal
];

foreach ($beritas as $b) {
    \App\Models\Berita::firstOrCreate(['slug' => $b['slug']], $b);
}
```

---

## 9. Pagination — Handling Halaman Banyak

Jika total halaman > 7, tampilkan elipsis agar pagination tidak terlalu panjang.
Ganti blok pagination di `berita/index.blade.php` dengan logic ini:

```blade
@php
    $current  = $beritas->currentPage();
    $last     = $beritas->lastPage();
    $window   = 2; // jumlah halaman di kiri-kanan current
    $pages    = [];

    for ($i = 1; $i <= $last; $i++) {
        if ($i === 1 || $i === $last || ($i >= $current - $window && $i <= $current + $window)) {
            $pages[] = $i;
        }
    }
    $pages = array_unique($pages);
    sort($pages);
@endphp

<nav class="flex items-center gap-1">
  {{-- Prev --}}
  @if(!$beritas->onFirstPage())
    <a href="{{ $beritas->previousPageUrl() }}" class="pagination-btn">«</a>
  @endif

  @php $prev = null; @endphp
  @foreach($pages as $page)
    @if($prev !== null && $page - $prev > 1)
      <span class="px-2 text-gray-400">...</span>
    @endif

    @if($page == $current)
      <span class="pagination-btn active">{{ $page }}</span>
    @else
      <a href="{{ $beritas->url($page) }}" class="pagination-btn">{{ $page }}</a>
    @endif

    @php $prev = $page; @endphp
  @endforeach

  {{-- Next --}}
  @if($beritas->hasMorePages())
    <a href="{{ $beritas->nextPageUrl() }}" class="pagination-btn">»</a>
  @endif
</nav>
```

CSS untuk `.pagination-btn`:
```css
.pagination-btn {
    display: inline-flex; align-items: center; justify-content: center;
    min-width: 36px; height: 36px; padding: 0 8px;
    border: 1px solid #D1D5DB; border-radius: 8px;
    font-size: 0.875rem; color: #4B5563;
    transition: all 0.2s;
}
.pagination-btn:hover    { background: #2D6A4F; color: white; border-color: #2D6A4F; }
.pagination-btn.active   { background: #2D6A4F; color: white; border-color: #2D6A4F; font-weight: 600; }
```

---

## Checklist Akhir

- [ ] Route `berita.index` → `/berita` bisa diakses (HTTP 200)
- [ ] Route `berita.show` → `/berita/{slug}` bisa diakses
- [ ] Grid 3 kolom tampil benar di desktop, 2 kolom di tablet, 1 kolom di mobile
- [ ] Badge kategori tampil dengan warna berbeda (hijau/kuning/biru)
- [ ] Tanggal format `dd-mm-yyyy` sesuai referensi screenshot
- [ ] Filter kategori (pill tab) berfungsi — klik "Berita" hanya tampil berita berkategori Berita
- [ ] Search box berfungsi — pencarian by judul
- [ ] Filter dan search bisa dikombinasikan
- [ ] Pagination muncul jika data > 9
- [ ] Pagination elipsis bekerja jika halaman > 7
- [ ] Teks "Menampilkan X–Y dari Z berita" tampil di bawah pagination
- [ ] Gambar tampil dari storage jika ada; gradient placeholder jika tidak
- [ ] Halaman detail (`/berita/{slug}`) tampil lengkap
- [ ] Views increment setiap kali halaman detail dibuka
- [ ] Sidebar "Berita Terbaru" tampil 5 item dengan thumbnail
- [ ] Sidebar "Kategori" tampil dengan highlight kategori aktif
- [ ] Share button (FB, Twitter, WA) berfungsi dengan URL yang benar
- [ ] Berita terkait tampil di bawah artikel (max 3, kategori sama)
- [ ] Link "Kembali ke Daftar Berita" berfungsi
- [ ] Navbar link "Berita" highlight saat di halaman berita
- [ ] Tombol "Lihat Semua Berita" di homepage mengarah ke `berita.index`
- [ ] CSS `.article-content` styling konten HTML dari DB tampil rapi
- [ ] `line-clamp` judul dan ringkasan tidak overflow
- [ ] Minimal 12 berita ter-seed (agar pagination bisa ditest)
