@extends('layouts.app')
@section('title', 'Profil Rumah Sakit — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Profil Rumah Sakit', 'parent' => 'Profil RS'])

{{-- BAGIAN 1: Sejarah --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-start">

            {{-- Gambar --}}
            <div class="photo-deco relative">
                @if($foto)
                    <img src="{{ Storage::url($foto) }}" alt="{{ $nama_rs }}"
                         class="w-full h-80 object-cover rounded-xl shadow-xl">
                @else
                    <div class="w-full h-80 rounded-xl shadow-xl flex items-center justify-center relative overflow-hidden"
                         style="background: linear-gradient(135deg, #1E3A8A, #2563EB)">
                        <i class="fas fa-hospital text-white/20 text-9xl"></i>
                        <div class="absolute bottom-4 left-4 right-4 text-center">
                            <div class="text-white font-bold">{{ $nama_rs }}</div>
                        </div>
                    </div>
                @endif
                <div class="absolute -top-4 -left-4 w-16 h-16 bg-gold rounded-lg z-10 opacity-80"></div>
                <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-lg z-10" style="background:#1E3A8A; opacity:0.7"></div>
            </div>

            {{-- Sejarah + Legalitas --}}
            <div>
                <h2 class="font-playfair text-2xl font-bold text-dark mb-2">Sejarah Singkat</h2>
                <div class="w-12 h-1 bg-gold mb-5"></div>
                <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                    {!! $sejarah !!}
                </div>

                @if($legalitas)
                <div class="mt-6 bg-gray-50 rounded-xl p-5 border border-gray-100">
                    <div class="text-xs font-semibold uppercase tracking-widest text-primary mb-3">
                        <i class="fas fa-certificate mr-1"></i> Legalitas
                    </div>
                    <div class="text-gray-700 text-sm prose prose-sm max-w-none [&_ul]:list-none [&_ul]:space-y-1 [&_li]:flex [&_li]:items-start [&_li]:gap-2">
                        {!! $legalitas !!}
                    </div>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

{{-- BAGIAN 2: Nilai-Nilai --}}
@if(count($nilai) > 0)
<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-10">
            <h2 class="font-playfair text-3xl font-bold text-dark mb-2">Nilai-Nilai Kami</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($nilai as $n)
            <div class="bg-white rounded-xl p-6 text-center shadow hover:shadow-xl hover:-translate-y-1 transition-all duration-300 border-t-4 border-transparent hover:border-gold">
                <div class="text-4xl text-primary mb-4">
                    <i class="fas {{ $n['ikon'] ?? 'fa-star' }}"></i>
                </div>
                <h3 class="font-bold text-dark text-lg mb-2">{{ $n['judul'] ?? '' }}</h3>
                <p class="text-gray-500 text-sm leading-relaxed">{{ $n['teks'] ?? '' }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>
@endif

{{-- BAGIAN 3: Legalitas & Akreditasi --}}
<section class="py-14" style="background: #1E3A8A">
    <div class="container mx-auto px-4 max-w-4xl text-center">
        <h2 class="font-playfair text-2xl font-bold text-white mb-2">Legalitas & Akreditasi</h2>
        <div class="w-12 h-1 bg-gold mx-auto mb-8"></div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-xl p-5 text-center shadow">
                <div class="text-3xl text-primary mb-2"><i class="fas fa-certificate"></i></div>
                <div class="font-bold text-dark text-sm">Izin Operasional</div>
                <div class="text-gray-500 text-xs mt-1">Dinas Kesehatan Kab. Landak</div>
            </div>
            <div class="bg-white rounded-xl p-5 text-center shadow">
                <div class="text-3xl text-primary mb-2"><i class="fas fa-award"></i></div>
                <div class="font-bold text-dark text-sm">Akreditasi KARS</div>
                <div class="text-gray-500 text-xs mt-1">Komisi Akreditasi Rumah Sakit</div>
            </div>
            <div class="bg-white rounded-xl p-5 text-center shadow">
                <div class="text-3xl text-primary mb-2"><i class="fas fa-hospital-symbol"></i></div>
                <div class="font-bold text-dark text-sm">Kemenkes RI</div>
                <div class="text-gray-500 text-xs mt-1">Kementerian Kesehatan Republik Indonesia</div>
            </div>
        </div>

        @if($legalitas)
        <div class="mt-8 text-white/80 text-sm prose prose-invert prose-sm max-w-none [&_ul]:list-none [&_ul]:space-y-2 text-center">
            {!! $legalitas !!}
        </div>
        @endif
    </div>
</section>

@endsection
