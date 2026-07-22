@extends('layouts.app')

@section('title', 'Pelayanan 24 Jam — RSUD Landak')

@section('content')

@include('partials.page-header', ['judul' => 'Pelayanan 24 Jam', 'parent' => 'Layanan'])

<section class="py-14 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-7xl">

        {{-- Intro --}}
        <div class="text-center mb-10 reveal">
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Layanan Non-Stop 24 Jam</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                RSUD Landak beroperasi penuh 24 jam sehari, 7 hari seminggu, 365 hari setahun.
                Kami selalu siaga untuk memberikan pelayanan kesehatan terbaik kapanpun Anda membutuhkan.
            </p>
            <div class="flex items-center justify-center gap-2 mt-4">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-sm font-semibold text-green-700">Siaga 24 Jam</span>
            </div>
        </div>

        @if($items->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20 reveal">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4"
                 style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9)">
                <i class="fas fa-clock text-4xl" style="color: #2563EB"></i>
            </div>
            <p class="text-gray-500 text-lg font-medium">Informasi sedang diperbarui.</p>
            <p class="text-gray-400 text-sm mt-1">Silakan hubungi kami untuk informasi lebih lanjut.</p>
        </div>
        @else
        {{-- Grid Layanan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($items as $item)
            <div class="bg-white rounded-2xl shadow hover:shadow-lg transition-all group overflow-hidden reveal">

                {{-- Foto / Gradient placeholder --}}
                <div class="relative h-52 overflow-hidden">
                    @if($item->foto)
                        <img src="{{ asset('storage/' . $item->foto) }}"
                             alt="{{ $item->nama }}"
                             class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                    @else
                        <div class="w-full h-full group-hover:scale-105 transition-transform duration-500"
                             style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #60A5FA 100%)">
                            <div class="w-full h-full flex items-center justify-center">
                                <i class="fas fa-hospital text-white/30 text-8xl"></i>
                            </div>
                        </div>
                    @endif

                    {{-- Overlay gradient bawah --}}
                    <div class="absolute inset-0"
                         style="background: linear-gradient(to top, rgba(30,58,138,0.7) 0%, transparent 60%)"></div>

                    {{-- Badge 24 Jam --}}
                    <div class="absolute top-3 right-3">
                        <span class="inline-flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full text-white"
                              style="background: rgba(201,168,76,0.9)">
                            <i class="fas fa-clock text-xs"></i> 24 Jam
                        </span>
                    </div>
                </div>

                {{-- Konten --}}
                <div class="p-5 relative">
                    <h3 class="font-bold text-gray-800 text-base uppercase tracking-wide mb-3 leading-tight">
                        {{ $item->nama }}
                    </h3>
                    <p class="text-gray-500 text-sm leading-relaxed line-clamp-4">
                        {{ $item->deskripsi }}
                    </p>

                    {{-- Garis aksen hijau --}}
                    <div class="absolute bottom-0 left-0 right-0 h-1 rounded-b-2xl"
                         style="background: linear-gradient(to right, #2563EB, #60A5FA)"></div>
                </div>
            </div>
            @endforeach
        </div>
        @endif

        {{-- CTA --}}
        <div class="mt-14 rounded-2xl overflow-hidden reveal" style="background: linear-gradient(135deg, #1E3A8A, #2563EB)">
            <div class="p-8 md:p-10 text-center">
                <i class="fas fa-phone-alt text-4xl text-green-300 mb-4 block"></i>
                <h3 class="font-playfair text-2xl md:text-3xl font-bold text-white mb-3">Butuh Bantuan Segera?</h3>
                <p class="text-green-200 mb-6 max-w-md mx-auto text-sm">
                    Tim kami siap 24 jam untuk membantu Anda. Hubungi IGD atau hotline RSUD Landak.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="tel:+62565-2025100"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-bold text-sm transition-all"
                       style="background: #D97706; color: #1E3A8A">
                        <i class="fas fa-phone"></i> (0563) 2022170
                    </a>
                    <a href="{{ route('home') }}#kontak"
                       class="inline-flex items-center justify-center gap-2 px-8 py-3 rounded-xl font-bold text-sm border-2 border-white text-white hover:bg-white hover:text-green-800 transition-all">
                        <i class="fas fa-map-marker-alt"></i> Lokasi Kami
                    </a>
                </div>
            </div>
        </div>

    </div>
</section>

@endsection
