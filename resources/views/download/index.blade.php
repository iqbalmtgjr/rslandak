@extends('layouts.app')

@section('title', 'Download — RSUD Landak')

@section('content')

@include('partials.page-header', ['judul' => 'Download', 'sub' => 'Unduh dokumen dan formulir yang tersedia'])

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-7xl">

    {{-- Search + Stats Bar --}}
    <div class="bg-white rounded-2xl shadow-sm p-5 mb-8">
      <div class="flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4">
        <form method="GET" action="{{ route('download.index') }}" class="flex items-center gap-2 flex-1">
          @if($slug)
            <input type="hidden" name="kategori" value="{{ $slug }}">
          @endif
          <div class="relative flex-1 max-w-md">
            <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
            <input type="text" name="q" value="{{ $search }}"
                   placeholder="Cari nama file atau dokumen..."
                   class="w-full pl-9 pr-4 py-2.5 border border-gray-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-green-500 focus:border-transparent">
          </div>
          <button type="submit"
                  class="px-4 py-2.5 bg-green-700 text-white text-sm font-medium rounded-xl hover:bg-green-800 transition-colors">
            Cari
          </button>
          @if($search || $slug)
            <a href="{{ route('download.index') }}"
               class="px-4 py-2.5 border border-gray-300 text-gray-600 text-sm rounded-xl hover:bg-gray-50 transition-colors">
              Reset
            </a>
          @endif
        </form>
        <div class="flex items-center gap-2 text-sm text-gray-500 flex-shrink-0">
          <i class="fas fa-file-alt text-green-600"></i>
          <span>{{ $totalFile }} file tersedia</span>
          @if($search || $slug)
            <span class="text-green-700 font-medium">| {{ $files->total() }} hasil ditemukan</span>
          @endif
        </div>
      </div>
    </div>

    {{-- Layout 2 Kolom --}}
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-6">

      {{-- Sidebar Kategori --}}
      <div class="lg:col-span-1">
        <div class="bg-white rounded-2xl shadow-sm p-5 sticky top-24">
          <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide border-b-2 border-green-700 pb-2 mb-3">
            Kategori
          </h3>
          <ul class="space-y-1">
            <li>
              <a href="{{ route('download.index', $search ? ['q' => $search] : []) }}"
                 class="flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-colors
                        {{ !$slug ? 'bg-green-700 text-white font-semibold' : 'text-gray-600 hover:bg-green-50 hover:text-green-700' }}">
                <span><i class="fas fa-folder-open mr-2"></i>Semua File</span>
                <span class="text-xs {{ !$slug ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full">
                  {{ $totalFile }}
                </span>
              </a>
            </li>
            @foreach($kategoris as $kat)
            <li>
              <a href="{{ route('download.index', array_filter(['kategori' => $kat->slug, 'q' => $search])) }}"
                 class="flex items-center justify-between px-3 py-2 rounded-xl text-sm transition-colors
                        {{ $slug === $kat->slug ? 'bg-green-700 text-white font-semibold' : 'text-gray-600 hover:bg-green-50 hover:text-green-700' }}">
                <span>
                  <i class="fas {{ $kat->ikon }} mr-2" style="{{ $slug === $kat->slug ? '' : 'color:' . $kat->warna }}"></i>
                  {{ $kat->nama }}
                </span>
                <span class="text-xs {{ $slug === $kat->slug ? 'bg-white/20' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full">
                  {{ $kat->jumlah_file }}
                </span>
              </a>
            </li>
            @endforeach
          </ul>
        </div>
      </div>

      {{-- Konten Utama --}}
      <div class="lg:col-span-3">

        @if($search || $slug)
          <div class="flex flex-wrap gap-2 mb-4">
            @if($slug)
              @php $katAktif = $kategoris->firstWhere('slug', $slug); @endphp
              @if($katAktif)
                <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-green-100 text-green-700 text-xs font-medium rounded-full">
                  <i class="fas {{ $katAktif->ikon }}"></i> {{ $katAktif->nama }}
                </span>
              @endif
            @endif
            @if($search)
              <span class="inline-flex items-center gap-1.5 px-3 py-1 bg-blue-100 text-blue-700 text-xs font-medium rounded-full">
                <i class="fas fa-search"></i> "{{ $search }}"
              </span>
            @endif
          </div>
        @endif

        @if($files->isEmpty())
          <div class="bg-white rounded-2xl py-24 text-center shadow-sm">
            <i class="fas fa-folder-open text-6xl text-gray-200 mb-4 block"></i>
            @if($search || $slug)
              <p class="text-gray-500 font-medium">File tidak ditemukan</p>
              <p class="text-sm text-gray-400 mt-1">Coba kata kunci lain atau <a href="{{ route('download.index') }}" class="text-green-700 hover:underline">lihat semua file</a></p>
            @else
              <p class="text-gray-500 font-medium">Belum ada file tersedia</p>
            @endif
          </div>
        @else
          <div class="grid sm:grid-cols-2 xl:grid-cols-3 gap-5">
            @foreach($files as $item)
            <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all group flex flex-col">
              <div class="p-5 flex-1">
                {{-- Ikon + Badge --}}
                <div class="flex items-start justify-between mb-3">
                  <div class="w-14 h-14 rounded-xl {{ $item->bg_ikon }} flex items-center justify-center flex-shrink-0">
                    <i class="fas {{ $item->ikon_file }} text-2xl {{ $item->warna_ikon }}"></i>
                  </div>
                  <span class="text-xs font-bold uppercase px-2 py-1 rounded-lg {{ $item->bg_ikon }} {{ $item->warna_ikon }}">
                    .{{ $item->tipe_file }}
                  </span>
                </div>
                {{-- Kategori --}}
                <p class="text-xs text-gray-400 mb-1">
                  <i class="fas fa-folder mr-1"></i>{{ $item->kategori->nama ?? '-' }}
                </p>
                {{-- Judul --}}
                <h4 class="font-semibold text-gray-800 text-sm leading-snug mb-1 line-clamp-2 group-hover:text-green-700 transition-colors">
                  {{ $item->judul }}
                </h4>
                @if($item->deskripsi)
                  <p class="text-xs text-gray-400 line-clamp-2 mb-2">{{ $item->deskripsi }}</p>
                @endif
                {{-- Meta --}}
                <div class="flex items-center gap-3 text-xs text-gray-400 mt-auto pt-2">
                  <span><i class="fas fa-hdd mr-1"></i>{{ $item->ukuran_readable }}</span>
                  <span><i class="fas fa-download mr-1"></i>{{ $item->jumlah_download }}x</span>
                  <span class="ml-auto">{{ $item->created_at->format('d/m/Y') }}</span>
                </div>
              </div>
              {{-- Tombol Download --}}
              <div class="px-5 pb-5">
                <a href="{{ $item->url_download }}"
                   class="flex items-center justify-center gap-2 w-full py-2.5 bg-green-700 hover:bg-green-800 text-white text-sm font-semibold rounded-xl transition-colors">
                  <i class="fas fa-download"></i> Download
                </a>
              </div>
            </div>
            @endforeach
          </div>

          {{-- Pagination --}}
          @if($files->hasPages())
          <div class="mt-10 flex flex-col items-center gap-3">
            <div class="flex items-center gap-1">
              {{-- Prev --}}
              @if($files->onFirstPage())
                <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 text-sm cursor-not-allowed">
                  <i class="fas fa-chevron-left"></i>
                </span>
              @else
                <a href="{{ $files->previousPageUrl() }}"
                   class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-green-700 hover:text-white hover:border-green-700 text-sm transition-colors">
                  <i class="fas fa-chevron-left"></i>
                </a>
              @endif

              {{-- Pages --}}
              @foreach($files->getUrlRange(1, $files->lastPage()) as $page => $url)
                @if($page == $files->currentPage())
                  <span class="px-3 py-2 rounded-lg bg-green-700 text-white text-sm font-semibold">{{ $page }}</span>
                @elseif(abs($page - $files->currentPage()) <= 2 || $page == 1 || $page == $files->lastPage())
                  <a href="{{ $url }}"
                     class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-green-700 hover:text-white hover:border-green-700 text-sm transition-colors">
                    {{ $page }}
                  </a>
                @elseif(abs($page - $files->currentPage()) == 3)
                  <span class="px-2 text-gray-400 text-sm">...</span>
                @endif
              @endforeach

              {{-- Next --}}
              @if($files->hasMorePages())
                <a href="{{ $files->nextPageUrl() }}"
                   class="px-3 py-2 rounded-lg border border-gray-200 text-gray-600 hover:bg-green-700 hover:text-white hover:border-green-700 text-sm transition-colors">
                  <i class="fas fa-chevron-right"></i>
                </a>
              @else
                <span class="px-3 py-2 rounded-lg border border-gray-200 text-gray-300 text-sm cursor-not-allowed">
                  <i class="fas fa-chevron-right"></i>
                </span>
              @endif
            </div>
            <p class="text-xs text-gray-500">
              Menampilkan {{ $files->firstItem() }}–{{ $files->lastItem() }} dari {{ $files->total() }} file
            </p>
          </div>
          @endif

        @endif
      </div>
    </div>
  </div>
</section>

@endsection
