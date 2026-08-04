@extends('layouts.app')

<<<<<<< HEAD
@section('title', 'Klinik — RSUD Landak')

@section('content')

@include('partials.page-header', ['judul' => 'Klinik', 'parent' => 'Layanan'])
=======
@section('title', 'Poliklinik — RSUD Landak')

@section('content')

@include('partials.page-header', ['judul' => 'Poliklinik', 'parent' => 'Layanan'])
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7

<section class="py-14 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-7xl">

        {{-- Intro --}}
        <div class="text-center mb-10 reveal">
<<<<<<< HEAD
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Daftar Klinik</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                RSUD Landak menyediakan berbagai klinik untuk memenuhi kebutuhan kesehatan masyarakat Landak dan sekitarnya.
=======
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Daftar Poliklinik</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                RSUD Landak menyediakan berbagai poliklinik spesialis untuk memenuhi kebutuhan kesehatan masyarakat Landak dan sekitarnya.
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            </p>
        </div>

        @if($polikliniks->isEmpty())
        {{-- Empty state --}}
        <div class="text-center py-20 reveal">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9)">
                <i class="fas fa-clinic-medical text-4xl" style="color: #2563EB"></i>
            </div>
<<<<<<< HEAD
            <p class="text-gray-500 text-lg font-medium">Informasi klinik sedang diperbarui.</p>
=======
            <p class="text-gray-500 text-lg font-medium">Informasi poliklinik sedang diperbarui.</p>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            <p class="text-gray-400 text-sm mt-1">Silakan hubungi kami untuk informasi lebih lanjut.</p>
        </div>
        @else
        {{-- Grid Poliklinik --}}
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-5">
            @foreach($polikliniks as $poli)
            <a href="{{ route('layanan.poliklinik.show', $poli->slug) }}"
               class="bg-white rounded-2xl shadow hover:shadow-lg hover:-translate-y-1 transition-all group reveal block overflow-hidden">

                {{-- Ikon area --}}
                <div class="h-28 flex items-center justify-center relative overflow-hidden" style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9)">
                    {{-- Decorative circle --}}
                    <div class="absolute -top-4 -right-4 w-20 h-20 rounded-full opacity-20" style="background: #2563EB"></div>
                    <div class="absolute -bottom-4 -left-4 w-16 h-16 rounded-full opacity-20" style="background: #60A5FA"></div>

                    @if($poli->tipe_ikon === 'img' && $poli->ikon)
                        <img src="{{ asset('storage/' . $poli->ikon) }}"
                             class="w-16 h-16 object-cover rounded-full relative z-10 group-hover:scale-105 transition-transform">
                    @elseif($poli->ikon)
                        <i class="fas {{ $poli->ikon }} text-5xl relative z-10 group-hover:scale-105 transition-transform"
                           style="color: #2563EB"></i>
                    @else
                        <i class="fas fa-clinic-medical text-5xl relative z-10" style="color: #2563EB"></i>
                    @endif
                </div>

                {{-- Info --}}
                <div class="p-4 text-center">
                    <h3 class="font-bold text-gray-800 text-sm leading-snug group-hover:text-green-700 transition-colors mb-1">
                        {{ $poli->nama }}
                    </h3>
                    @if($poli->jumlah_dokter > 0)
                    <span class="inline-flex items-center gap-1 text-xs text-green-700 bg-green-50 px-2 py-0.5 rounded-full">
                        <i class="fas fa-user-md text-xs"></i>
                        {{ $poli->jumlah_dokter }} Dokter
                    </span>
                    @else
                    <span class="text-xs text-gray-400">
                        <i class="fas fa-stethoscope mr-1"></i>Informasi dokter
                    </span>
                    @endif
                </div>
            </a>
            @endforeach
        </div>
        @endif

    </div>
</section>

@endsection
