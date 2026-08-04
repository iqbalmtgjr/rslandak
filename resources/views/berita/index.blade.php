@extends('layouts.app')

@section('title', 'Berita & Event — RSUD Landak')
@section('meta_description', 'Informasi dan berita terkini dari RSUD Landak, Kalimantan Barat.')

@section('content')

@include('partials.page-header', ['judul' => 'Berita & Event', 'parent' => null])

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-7xl">

    {{-- Filter & Search Bar --}}
    <div class="flex flex-col md:flex-row items-center justify-between gap-4 mb-10">

      {{-- Pill Tabs --}}
      <div class="flex flex-wrap gap-2">
        <a href="{{ route('berita.index', $search ? ['q' => $search] : []) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === '' ? 'bg-green-700 text-white border-green-700 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-green-600 hover:text-green-700' }}">
          Semua <span class="ml-1 text-xs opacity-75">({{ $countAll }})</span>
        </a>

        <a href="{{ route('berita.index', array_filter(['kategori' => 'Berita', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Berita' ? 'bg-green-700 text-white border-green-700 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-green-600 hover:text-green-700' }}">
          Berita <span class="ml-1 text-xs opacity-75">({{ $countBerita }})</span>
        </a>

        <a href="{{ route('berita.index', array_filter(['kategori' => 'Pengumuman', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Pengumuman' ? 'bg-yellow-500 text-white border-yellow-500 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-yellow-500 hover:text-yellow-600' }}">
          Pengumuman <span class="ml-1 text-xs opacity-75">({{ $countPengumuman }})</span>
        </a>

        <a href="{{ route('berita.index', array_filter(['kategori' => 'Kegiatan', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Kegiatan' ? 'bg-blue-600 text-white border-blue-600 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-blue-500 hover:text-blue-600' }}">
          Kegiatan <span class="ml-1 text-xs opacity-75">({{ $countKegiatan }})</span>
        </a>

        <a href="{{ route('berita.index', array_filter(['kategori' => 'Promosi Kesehatan', 'q' => $search ?: null])) }}"
           class="px-5 py-2 rounded-full text-sm font-medium transition-all border
                  {{ $kategori === 'Promosi Kesehatan' ? 'bg-purple-600 text-white border-purple-600 shadow' : 'bg-white text-gray-600 border-gray-300 hover:border-purple-500 hover:text-purple-600' }}">
          Promosi Kesehatan (PKRS) <span class="ml-1 text-xs opacity-75">({{ $countPkrs }})</span>
        </a>
      </div>

      {{-- Search --}}
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

    {{-- Grid Berita --}}
    @if($beritas->isEmpty())
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

      {{-- Pagination --}}
      @if($beritas->hasPages())
        @php
          $current = $beritas->currentPage();
          $last    = $beritas->lastPage();
          $window  = 2;
          $pages   = [];
          for ($i = 1; $i <= $last; $i++) {
              if ($i === 1 || $i === $last || ($i >= $current - $window && $i <= $current + $window)) {
                  $pages[] = $i;
              }
          }
          $pages = array_unique($pages);
          sort($pages);
        @endphp

        <div class="mt-12 flex justify-center">
          <nav class="flex items-center gap-1">
            @if(!$beritas->onFirstPage())
              <a href="{{ $beritas->previousPageUrl() }}" class="pagination-btn">«</a>
            @else
              <span class="pagination-btn opacity-40 cursor-not-allowed">«</span>
            @endif

            @php $prev = null; @endphp
            @foreach($pages as $page)
              @if($prev !== null && $page - $prev > 1)
                <span class="px-2 text-gray-400 text-sm">...</span>
              @endif

              @if($page == $current)
                <span class="pagination-btn active">{{ $page }}</span>
              @else
                <a href="{{ $beritas->url($page) }}" class="pagination-btn">{{ $page }}</a>
              @endif

              @php $prev = $page; @endphp
            @endforeach

            @if($beritas->hasMorePages())
              <a href="{{ $beritas->nextPageUrl() }}" class="pagination-btn">»</a>
            @else
              <span class="pagination-btn opacity-40 cursor-not-allowed">»</span>
            @endif
          </nav>
        </div>

        <p class="text-center text-gray-400 text-sm mt-4">
          Menampilkan {{ $beritas->firstItem() }}–{{ $beritas->lastItem() }} dari {{ $beritas->total() }} berita
        </p>
      @endif
    @endif

  </div>
</section>

@endsection
