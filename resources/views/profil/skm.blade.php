@extends('layouts.app')
@section('title', 'Survei Kepuasan Masyarakat (SKM) — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Survei Kepuasan Masyarakat (SKM)', 'parent' => 'Profil RS'])

<section class="py-16 bg-gray-50 min-h-screen" x-data="{ showModal: false, modalImg: '', modalTitle: '' }">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="text-center mb-12 reveal">
            <span class="text-primary font-bold text-xs uppercase tracking-wider bg-blue-50 px-3 py-1.5 rounded-full">Survei Kepuasan</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mt-3 mb-2">Indeks Kepuasan Masyarakat (IKM)</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">Laporan hasil penilaian kualitas pelayanan publik {{ $nama_rs }}</p>
            <div class="w-16 h-1 rounded bg-gold mx-auto mt-4"></div>
        </div>

        @if($skms->isEmpty())
            <div class="text-center py-20 bg-white rounded-3xl border border-gray-100 shadow-sm reveal">
                <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4 bg-blue-50 text-primary">
                    <i class="fas fa-chart-bar text-4xl"></i>
                </div>
                <p class="text-gray-500 text-lg font-medium">Data hasil penilaian SKM sedang diperbarui.</p>
            </div>
        @else
            <div class="space-y-16">
                @foreach($skms as $tahun => $items)
                    <div class="reveal">
                        {{-- Year Header --}}
                        <div class="flex items-center gap-4 mb-8">
                            <span class="bg-primary text-white font-bold px-4 py-1.5 rounded-xl text-lg shadow-sm">
                                Tahun {{ $tahun }}
                            </span>
                            <div class="flex-1 h-0.5 bg-gray-200"></div>
                        </div>

                        {{-- Images Grid --}}
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8">
                            @foreach($items as $item)
                                <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col justify-between">
                                    {{-- Image Container --}}
                                    <div class="relative aspect-[4/3] bg-gray-50 overflow-hidden cursor-pointer"
                                         @click="modalImg = '{{ Storage::url($item->gambar) }}'; modalTitle = '{{ $item->judul }} (Tahun {{ $tahun }})'; showModal = true">
                                        <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->judul }}" 
                                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                        
                                        {{-- Glassmorphic Hover Overlay --}}
                                        <div class="absolute inset-0 bg-dark/40 backdrop-blur-[2px] opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-center justify-center">
                                            <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md border border-white/40 flex items-center justify-center text-white text-xl">
                                                <i class="fas fa-expand-alt"></i>
                                            </div>
                                        </div>
                                    </div>

                                    {{-- Text info --}}
                                    <div class="p-5 border-t border-gray-50 bg-white">
                                        <div class="flex items-center justify-between mb-2">
                                            <span class="text-[10px] bg-gold/10 text-gold border border-gold/20 font-semibold px-2 py-0.5 rounded-full">
                                                Survei Kepuasan
                                            </span>
                                            <span class="text-xs text-gray-400 font-mono">
                                                #{{ $item->urutan }}
                                            </span>
                                        </div>
                                        <h4 class="font-bold text-gray-800 text-sm md:text-base group-hover:text-primary transition-colors line-clamp-2">
                                            {{ $item->judul }}
                                        </h4>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endforeach
            </div>

            {{-- Glassmorphic Lightbox Modal --}}
            <div x-show="showModal" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0"
                 x-transition:enter-end="opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0"
                 class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-dark/80 backdrop-blur-md"
                 style="display: none;"
                 @keydown.escape.window="showModal = false">
                
                {{-- Backdrop Click --}}
                <div class="absolute inset-0 cursor-zoom-out" @click="showModal = false"></div>

                {{-- Modal Box --}}
                <div class="relative bg-white/10 backdrop-blur-lg border border-white/20 rounded-3xl p-3 max-w-4xl w-full max-h-[85vh] flex flex-col shadow-2xl z-10"
                     x-transition:enter="transition ease-out duration-300 transform scale-95"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100"
                     x-transition:leave="transition ease-in duration-200 transform scale-100"
                     x-transition:leave-start="opacity-100 scale-100"
                     x-transition:leave-end="opacity-0 scale-95">
                    
                    {{-- Close Button --}}
                    <button class="absolute -top-12 right-0 text-white hover:text-gray-300 text-3xl focus:outline-none"
                            @click="showModal = false">
                        <i class="fas fa-times"></i>
                    </button>

                    {{-- Image --}}
                    <div class="overflow-y-auto rounded-2xl bg-white flex items-center justify-center p-2">
                        <img :src="modalImg" :alt="modalTitle" class="max-w-full max-h-[70vh] object-contain rounded-lg">
                    </div>

                    {{-- Title/Footer --}}
                    <div class="p-3 text-center text-white">
                        <h4 class="font-bold text-sm md:text-base" x-text="modalTitle"></h4>
                    </div>
                </div>
            </div>
        @endif

        {{-- Feedback Callout --}}
        <div class="max-w-xl mx-auto mt-16 p-5 rounded-2xl border border-gold/20 shadow-sm bg-white text-center reveal">
            <p class="text-gray-600 text-sm">
                <i class="fas fa-info-circle text-gold mr-1"></i>
                Indeks kepuasan diukur berdasarkan survei terhadap pengguna layanan {{ $nama_rs }}.
            </p>
        </div>

    </div>
</section>

@endsection
