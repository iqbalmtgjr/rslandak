@extends('layouts.app')

@section('title', 'Leaflet & Poster — RSUD Landak')

@section('content')

@include('partials.page-header', ['judul' => 'Leaflet & Poster', 'sub' => 'Informasi dan edukasi kesehatan untuk masyarakat'])

<section class="py-14 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl">

    <div class="grid grid-cols-1 lg:grid-cols-5 gap-10 items-start">

      {{-- Ilustrasi Kiri --}}
      <div class="lg:col-span-2 flex items-center justify-center">
        <div class="relative w-full max-w-xs">
          <div class="bg-white rounded-3xl shadow-lg p-8 flex items-end justify-center" style="min-height: 320px;">
            <svg viewBox="0 0 300 320" xmlns="http://www.w3.org/2000/svg" class="w-full max-w-[260px]">
              <!-- Kaca pembesar -->
              <circle cx="200" cy="140" r="65" fill="none" stroke="#2563EB" stroke-width="14"/>
              <line x1="245" y1="192" x2="280" y2="230" stroke="#2563EB" stroke-width="14" stroke-linecap="round"/>
              <!-- Ikon dokumen di dalam kaca -->
              <rect x="168" y="108" width="64" height="64" rx="6" fill="#E8F5E9"/>
              <line x1="180" y1="125" x2="220" y2="125" stroke="#2563EB" stroke-width="4" stroke-linecap="round"/>
              <line x1="180" y1="138" x2="220" y2="138" stroke="#2563EB" stroke-width="4" stroke-linecap="round"/>
              <line x1="180" y1="151" x2="205" y2="151" stroke="#D97706" stroke-width="4" stroke-linecap="round"/>
              <!-- Orang -->
              <circle cx="100" cy="80" r="28" fill="#FDDCB5"/>
              <rect x="75" y="110" width="50" height="70" rx="8" fill="#E8E8E8"/>
              <rect x="80" y="176" width="18" height="55" rx="8" fill="#333"/>
              <rect x="102" y="176" width="18" height="55" rx="8" fill="#333"/>
              <ellipse cx="89" cy="231" rx="14" ry="7" fill="#222"/>
              <ellipse cx="111" cy="231" rx="14" ry="7" fill="#222"/>
              <line x1="125" y1="130" x2="168" y2="155" stroke="#E8E8E8" stroke-width="16" stroke-linecap="round"/>
              <ellipse cx="100" cy="60" rx="28" ry="18" fill="#5D4037"/>
            </svg>
          </div>
          <div class="absolute -bottom-4 -right-4 w-20 h-20 bg-gradient-to-br from-green-700 to-green-500 rounded-2xl opacity-20"></div>
          <div class="absolute -top-4 -left-4 w-12 h-12 bg-yellow-400 rounded-xl opacity-30"></div>
        </div>
      </div>

      {{-- Tab + Accordion Kanan --}}
      <div class="lg:col-span-3" x-data="{ activeTab: 'Leaflet', openAccordion: null }">

        {{-- Tab --}}
        <div class="flex border-b border-gray-200 mb-0">
          <button @click="activeTab = 'Leaflet'; openAccordion = null"
                  :class="activeTab === 'Leaflet'
                    ? 'border-b-2 border-green-700 text-green-700 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                  class="px-6 py-3 text-sm transition-colors focus:outline-none">
            <i class="fas fa-file-medical mr-1"></i> Leaflet
          </button>
          <button @click="activeTab = 'Poster'; openAccordion = null"
                  :class="activeTab === 'Poster'
                    ? 'border-b-2 border-green-700 text-green-700 font-semibold'
                    : 'text-gray-500 hover:text-gray-700'"
                  class="px-6 py-3 text-sm transition-colors focus:outline-none">
            <i class="fas fa-image mr-1"></i> Poster
          </button>
        </div>

        {{-- Panel Leaflet --}}
        <div x-show="activeTab === 'Leaflet'" x-transition>
          <div class="border border-gray-200 rounded-b-xl rounded-tr-xl overflow-hidden bg-white shadow-sm">
            @forelse($leaflets as $kat)
              <div class="border-b border-gray-100 last:border-b-0">
                <button type="button"
                        @click="openAccordion = (openAccordion === 'L{{ $kat->id }}') ? null : 'L{{ $kat->id }}'"
                        class="w-full flex items-center justify-between px-6 py-4
                               text-left text-sm font-semibold text-gray-700
                               hover:bg-green-50 hover:text-green-700 transition-colors">
                  <span>{{ $kat->nama }}</span>
                  <div class="flex items-center gap-2">
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                      {{ $kat->items->count() }}
                    </span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                       :class="openAccordion === 'L{{ $kat->id }}' ? 'rotate-180 text-green-700' : ''"></i>
                  </div>
                </button>
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
                            <div class="flex-shrink-0 w-8 h-8 bg-red-100 rounded-lg flex items-center justify-center">
                              <i class="fas fa-file-pdf text-red-500 text-sm"></i>
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
                               title="Buka di Google Drive"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-green-700 text-white rounded-lg hover:bg-green-800 transition-colors">
                              <i class="fas fa-eye"></i>
                              <span class="hidden sm:inline">Lihat</span>
                            </a>
                            <a href="{{ str_replace('/view', '/download?usp=sharing', $item->url_open) }}"
                               target="_blank" rel="noopener noreferrer"
                               title="Download"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                              <i class="fas fa-download"></i>
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
                <i class="fas fa-folder-open text-5xl mb-3 block"></i>
                <p class="text-sm">Belum ada leaflet yang tersedia.</p>
              </div>
            @endforelse
          </div>
        </div>

        {{-- Panel Poster --}}
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
                  <div class="flex items-center gap-2">
                    <span class="text-xs bg-gray-100 text-gray-500 px-2 py-0.5 rounded-full">
                      {{ $kat->items->count() }}
                    </span>
                    <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                       :class="openAccordion === 'P{{ $kat->id }}' ? 'rotate-180 text-green-700' : ''"></i>
                  </div>
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
                              <i class="fas fa-image text-blue-500 text-sm"></i>
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
                              <i class="fas fa-eye"></i>
                              <span class="hidden sm:inline">Lihat</span>
                            </a>
                            <a href="{{ str_replace('/view', '/download?usp=sharing', $item->url_open) }}"
                               target="_blank" rel="noopener noreferrer"
                               class="flex items-center gap-1.5 px-3 py-1.5 text-xs font-medium
                                      bg-yellow-500 text-white rounded-lg hover:bg-yellow-600 transition-colors">
                              <i class="fas fa-download"></i>
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
                <i class="fas fa-folder-open text-5xl mb-3 block"></i>
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
