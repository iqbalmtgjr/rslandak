@extends('layouts.app')
@section('title', 'Struktur Organisasi — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Struktur Organisasi', 'parent' => 'Profil RS'])

<style>
    /* Styling scrollbar tipis yang cantik */
    .scrollbar-thin::-webkit-scrollbar {
        height: 6px;
    }
    .scrollbar-thin::-webkit-scrollbar-track {
        background: transparent;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background-color: #cbd5e1;
        border-radius: 20px;
    }
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background-color: #94a3b8;
    }
    .scrollbar-hidden::-webkit-scrollbar {
        display: none;
    }
    .scrollbar-hidden {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<section class="py-16 bg-gray-50" x-data="{ activeBidang: 'all' }">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="text-center mb-10 reveal">
            <span class="text-primary font-bold text-xs uppercase tracking-wider bg-blue-50 px-3 py-1.5 rounded-full">Manajemen & Staff</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mt-3 mb-2">Struktur Organisasi</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Susunan pejabat struktural dan organisasi {{ $nama_rs }}</p>
            <div class="w-16 h-1 rounded bg-gold mx-auto mt-4"></div>
        </div>

        @if($bidangs->isEmpty() || $bidangs->every(fn($b) => $b->strukturs->isEmpty()))
        <div class="text-center py-20 reveal">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 bg-blue-100">
                <i class="fas fa-sitemap text-4xl text-primary"></i>
            </div>
            <p class="text-gray-500 text-lg font-medium">Data struktur organisasi sedang diperbarui.</p>
        </div>
        @else
            {{-- Navigation / Bidang Filter Tab --}}
            <div class="flex gap-2 mb-12 overflow-x-auto pb-3 justify-start md:justify-center scrollbar-hidden reveal">
                <button @click="activeBidang = 'all'"
                    :class="activeBidang === 'all' ? 'bg-primary text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-200 border border-gray-200'"
                    class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all duration-300">
                    Semua Bidang
                </button>
                @foreach($bidangs as $bidang)
                    @if($bidang->strukturs->isNotEmpty())
                    <button @click="activeBidang = {{ $bidang->id }}"
                        :class="activeBidang === {{ $bidang->id }} ? 'bg-primary text-white shadow-md' : 'bg-white text-gray-600 hover:bg-gray-200 border border-gray-200'"
                        class="px-5 py-2.5 rounded-full text-sm font-semibold whitespace-nowrap transition-all duration-300">
                        {{ $bidang->nama }}
                    </button>
                    @endif
                @endforeach
            </div>

            {{-- Divisions Lists --}}
            @foreach($bidangs as $bidang)
                @if($bidang->strukturs->isNotEmpty())
                <div x-show="activeBidang === 'all' || activeBidang === {{ $bidang->id }}"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 transform translate-y-4"
                     x-transition:enter-end="opacity-100 transform translate-y-0"
                     class="mb-20 reveal">
                    
                    {{-- Bidang Header --}}
                    <div class="flex items-center gap-4 mb-8">
                        <h3 class="font-playfair text-xl md:text-2xl font-bold text-dark">{{ $bidang->nama }}</h3>
                        <div class="flex-1 h-0.5 bg-gray-200"></div>
                    </div>

                    {{-- Members Display --}}
                    @php
                        $count = $bidang->strukturs->count();
                    @endphp
                    
                    {{-- If only 1 member (e.g. Direktur), display as centered single card --}}
                    @if($count === 1)
                        <div class="flex justify-center">
                            @foreach($bidang->strukturs as $item)
                                <div class="w-full max-w-sm bg-white rounded-2xl border border-gray-100 shadow-md hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                                    <div class="aspect-[3/4] relative bg-gray-100 overflow-hidden">
                                        @if($item->foto)
                                            <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        @else
                                            <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 text-primary">
                                                <i class="fas fa-user text-6xl opacity-30 mb-2"></i>
                                                <span class="text-xs font-semibold tracking-wider uppercase opacity-40">{{ $nama_rs }}</span>
                                            </div>
                                        @endif
                                        <div class="absolute inset-0 bg-gradient-to-t from-dark/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                    </div>
                                    <div class="p-6 text-center">
                                        <h4 class="font-playfair text-lg font-bold text-dark mb-1 group-hover:text-primary transition-colors">{{ $item->nama }}</h4>
                                        <p class="text-gold font-semibold text-sm mb-3 tracking-wide">{{ $item->jabatan }}</p>
                                        @if($item->nip)
                                            <div class="inline-block bg-gray-50 border border-gray-100 rounded-lg px-3 py-1">
                                                <p class="text-xs text-gray-500 font-mono">NIP. {{ $item->nip }}</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        {{-- Horizontal Scroll container --}}
                        <div class="relative group/scroll">
                            <!-- Left Arrow shadow indicator -->
                            <div class="absolute left-0 top-0 bottom-0 w-12 bg-gradient-to-r from-gray-50 to-transparent pointer-events-none z-10 opacity-0 group-hover/scroll:opacity-100 transition-opacity duration-300"></div>
                            
                            <!-- Scrollable Row -->
                            <div class="flex overflow-x-auto gap-6 pb-6 pt-2 snap-x snap-mandatory scroll-smooth scrollbar-thin">
                                @foreach($bidang->strukturs as $item)
                                    <div class="w-72 md:w-80 flex-shrink-0 snap-start bg-white rounded-2xl border border-gray-100 shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 overflow-hidden group">
                                        <div class="aspect-[3/4] relative bg-gray-100 overflow-hidden">
                                            @if($item->foto)
                                                <img src="{{ Storage::url($item->foto) }}" alt="{{ $item->nama }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                            @else
                                                <div class="w-full h-full flex flex-col items-center justify-center bg-gradient-to-br from-blue-50 to-blue-100 text-primary">
                                                    <i class="fas fa-user text-5xl opacity-30 mb-2"></i>
                                                    <span class="text-xs font-semibold tracking-wider uppercase opacity-40">{{ $nama_rs }}</span>
                                                </div>
                                            @endif
                                            <div class="absolute inset-0 bg-gradient-to-t from-dark/60 via-transparent to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                                        </div>
                                        <div class="p-5 text-center">
                                            <h4 class="font-playfair text-base font-bold text-dark mb-1 group-hover:text-primary transition-colors leading-snug">{{ $item->nama }}</h4>
                                            <p class="text-gold font-semibold text-xs mb-3 tracking-wide">{{ $item->jabatan }}</p>
                                            @if($item->nip)
                                                <div class="inline-block bg-gray-50 border border-gray-100 rounded-lg px-2.5 py-0.5">
                                                    <p class="text-[10px] text-gray-500 font-mono">NIP. {{ $item->nip }}</p>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            
                            <!-- Right Arrow shadow indicator -->
                            <div class="absolute right-0 top-0 bottom-0 w-12 bg-gradient-to-l from-gray-50 to-transparent pointer-events-none z-10 opacity-0 group-hover/scroll:opacity-100 transition-opacity duration-300"></div>
                        </div>
                    @endif
                </div>
                @endif
            @endforeach
        @endif

    </div>
</section>

@endsection
