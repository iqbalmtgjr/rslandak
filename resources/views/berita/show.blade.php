@extends('layouts.app')

@section('title', $berita->judul . ' — RSUD Landak')
@section('meta_description', $berita->ringkasan)

@section('content')

@include('partials.page-header', ['judul' => 'Berita & Event', 'parent' => null])

<section class="py-12 bg-gray-50">
  <div class="container mx-auto px-4 max-w-7xl">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

      {{-- ===== ARTIKEL UTAMA ===== --}}
      <article class="lg:col-span-2">
        <div class="bg-white rounded-2xl shadow-sm overflow-hidden">

          {{-- Thumbnail --}}
          @if($berita->gambar_url)
            <img src="{{ $berita->gambar_url }}" alt="{{ $berita->judul }}" class="w-full h-80 object-cover">
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
                <i class="fa fa-user-circle mr-1"></i>{{ $berita->penulis }}
              </span>
              <span class="text-xs text-gray-400 ml-auto">
                <i class="fa fa-eye mr-1"></i>{{ number_format($berita->views) }} kali dibaca
              </span>
            </div>

            {{-- Judul --}}
            <h1 class="font-playfair text-2xl md:text-3xl font-bold text-gray-800 leading-tight mb-6">
              {{ $berita->judul }}
            </h1>

            <div class="w-16 h-1 bg-yellow-500 rounded mb-6"></div>

            {{-- Konten --}}
            <div class="prose prose-green max-w-none text-gray-700 leading-relaxed article-content">
              {!! $berita->konten !!}
            </div>

            {{-- Share --}}
            <div class="mt-8 pt-6 border-t border-gray-100 flex items-center gap-3 flex-wrap">
              <span class="text-sm text-gray-500 font-medium">Bagikan:</span>
              <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                 target="_blank" rel="noopener"
                 class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full text-xs hover:bg-blue-700 transition-colors">
                <i class="fab fa-facebook-f"></i> Facebook
              </a>
              <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($berita->judul) }}"
                 target="_blank" rel="noopener"
                 class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-full text-xs hover:bg-sky-600 transition-colors">
                <i class="fab fa-twitter"></i> Twitter
              </a>
              <a href="https://wa.me/?text={{ urlencode($berita->judul . ' ' . request()->url()) }}"
                 target="_blank" rel="noopener"
                 class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-full text-xs hover:bg-green-600 transition-colors">
                <i class="fab fa-whatsapp"></i> WhatsApp
              </a>
            </div>

            {{-- Kembali --}}
            <div class="mt-6">
              <a href="{{ route('berita.index') }}"
                 class="inline-flex items-center gap-2 text-green-700 font-medium text-sm hover:text-green-900 transition-colors">
                <i class="fa fa-arrow-left"></i> Kembali ke Daftar Berita
              </a>
            </div>
          </div>
        </div>

        {{-- Berita Terkait --}}
        @if($terkait->isNotEmpty())
          <div class="mt-8">
            <h2 class="font-playfair text-xl font-bold text-gray-800 mb-5">Berita Terkait</h2>
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
                <a href="{{ route('berita.show', $item->slug) }}"
                   class="flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden">
                  @if($item->gambar_url)
                    <img src="{{ $item->gambar_url }}" alt="{{ $item->judul }}"
                         class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                  @else
                    <div class="w-full h-full bg-gradient-to-br from-green-700 to-green-500 flex items-center justify-center">
                      <i class="fa fa-newspaper text-white text-lg opacity-60"></i>
                    </div>
                  @endif
                </a>
                <div class="flex-1 min-w-0">
                  <a href="{{ route('berita.show', $item->slug) }}"
                     class="text-sm font-medium text-gray-700 group-hover:text-green-700 transition-colors leading-snug line-clamp-2 block">
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

        {{-- Kategori --}}
        <div class="bg-white rounded-2xl shadow-sm p-6">
          <h3 class="font-playfair text-lg font-bold text-gray-800 mb-4 pb-3 border-b-2 border-green-700">
            Kategori
          </h3>
          <ul class="space-y-2">
            <li>
              <a href="{{ route('berita.index') }}"
                 class="flex items-center px-3 py-2 rounded-lg text-sm text-gray-600 hover:bg-green-50 hover:text-green-700 transition-colors">
                <i class="fa fa-newspaper mr-2 text-gray-400"></i> Semua Berita
              </a>
            </li>
            @foreach(['Berita' => 'fa-rss', 'Pengumuman' => 'fa-bullhorn', 'Kegiatan' => 'fa-calendar-check'] as $kat => $icon)
              <li>
                <a href="{{ route('berita.index', ['kategori' => $kat]) }}"
                   class="flex items-center px-3 py-2 rounded-lg text-sm transition-colors
                          {{ $berita->kategori === $kat ? 'bg-green-700 text-white' : 'text-gray-600 hover:bg-green-50 hover:text-green-700' }}">
                  <i class="fa {{ $icon }} mr-2 opacity-70"></i> {{ $kat }}
                </a>
              </li>
            @endforeach
          </ul>
        </div>

        {{-- CTA --}}
        <div class="bg-gradient-to-br from-green-800 to-green-600 rounded-2xl p-6 text-white text-center">
          <i class="fa fa-phone-alt text-3xl mb-3 text-yellow-400"></i>
          <h3 class="font-playfair text-lg font-bold mb-2">Butuh Informasi?</h3>
          <p class="text-green-200 text-sm mb-4">Hubungi kami untuk informasi lebih lanjut</p>
          <a href="tel:{{ \App\Models\SiteSetting::get('telepon') }}"
             class="block bg-yellow-500 text-white font-semibold py-2 px-4 rounded-full text-sm hover:bg-yellow-400 transition-colors">
            {{ \App\Models\SiteSetting::get('telepon', '(0563) 2022170') }}
          </a>
        </div>

      </aside>
    </div>
  </div>
</section>

@endsection
