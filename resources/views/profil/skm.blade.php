@extends('layouts.app')
@section('title', 'Indeks Kepuasan Masyarakat (SKM) — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Hasil Penilaian SKM', 'parent' => 'Profil RS'])

<section class="py-16 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">

        <div class="text-center mb-12 reveal">
            <h2 class="font-playfair text-3xl font-bold text-dark mb-3">Hasil Survei Kepuasan Masyarakat (SKM)</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                Transparansi hasil penilaian tingkat kepuasan masyarakat terhadap pelayanan {{ $settings['nama_rs'] ?? 'RSUD Landak' }} dari tahun ke tahun.
            </p>
            <div class="w-24 h-1 bg-gold rounded-full mx-auto mt-4"></div>
        </div>

        @if($skmsByYear->isEmpty())
            <div class="text-center py-20 bg-white rounded-2xl shadow-sm border border-gray-100 reveal">
                <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4"
                     style="background: linear-gradient(135deg, #dbeafe, #bfdbfe)">
                    <i class="fas fa-poll text-4xl" style="color: #2563EB"></i>
                </div>
                <p class="text-gray-500 text-lg font-medium">Data penilaian SKM belum tersedia.</p>
                <p class="text-gray-400 text-sm mt-1">Silakan kunjungi kembali beberapa saat lagi.</p>
            </div>
        @else
            {{-- Tabs for Years --}}
            <div x-data="{ activeYear: '{{ $skmsByYear->keys()->first() }}' }" class="space-y-8">
                <div class="flex justify-center gap-2 flex-wrap bg-white p-2 rounded-xl shadow-sm border border-gray-100 max-w-md mx-auto reveal">
                    @foreach($skmsByYear->keys() as $year)
                        <button type="button" @click="activeYear = '{{ $year }}'"
                                :class="activeYear === '{{ $year }}' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                                class="px-5 py-2 rounded-lg text-sm font-semibold transition-all">
                            Tahun {{ $year }}
                        </button>
                    @endforeach
                </div>

                {{-- Years Content --}}
                @foreach($skmsByYear as $year => $skms)
                    <div x-show="activeYear === '{{ $year }}'" x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0 translate-y-4"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         class="grid grid-cols-1 md:grid-cols-2 gap-8 reveal">
                        @foreach($skms as $skm)
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden flex flex-col hover:shadow-md transition-shadow">
                                {{-- Image area --}}
                                <div class="bg-gray-100 relative group overflow-hidden cursor-pointer"
                                     @click="$dispatch('open-lightbox', { src: '{{ Storage::url($skm->gambar) }}', title: '{{ $skm->judul }}' })">
                                    <img src="{{ Storage::url($skm->gambar) }}" alt="{{ $skm->judul }}"
                                         class="w-full h-auto object-cover group-hover:scale-102 transition-transform duration-300">
                                    
                                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                        <div class="bg-white/90 p-3 rounded-full text-primary shadow-lg">
                                            <i class="fas fa-search-plus text-lg"></i>
                                        </div>
                                    </div>
                                </div>

                                {{-- Details --}}
                                <div class="p-5 border-t border-gray-50 flex-1 flex flex-col justify-between">
                                    <div>
                                        <span class="inline-block text-[10px] uppercase font-bold tracking-wider text-gold bg-amber-50 px-2 py-0.5 rounded mb-2">
                                            Tahun {{ $skm->tahun }}
                                        </span>
                                        <h3 class="font-bold text-gray-800 text-base leading-snug">
                                            {{ $skm->judul }}
                                        </h3>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endforeach
            </div>
        @endif

        {{-- Lightbox Modal --}}
        <div x-data="{ open: false, src: '', title: '' }"
             @open-lightbox.window="open = true; src = $event.detail.src; title = $event.detail.title"
             x-show="open"
             x-transition
             class="fixed inset-0 z-50 bg-black/80 flex items-center justify-center p-4"
             style="display: none;">
            <div @click.away="open = false" class="max-w-4xl w-full flex flex-col gap-3 relative">
                <button type="button" @click="open = false" class="absolute -top-10 right-0 text-white text-3xl hover:text-gray-300">
                    <i class="fas fa-times"></i>
                </button>
                <div class="bg-white p-2 rounded-2xl shadow-2xl overflow-hidden">
                    <img :src="src" class="w-full h-auto max-h-[80vh] object-contain rounded-lg">
                    <div class="p-3 text-center border-t border-gray-100 font-bold text-gray-800 text-sm" x-text="title"></div>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
