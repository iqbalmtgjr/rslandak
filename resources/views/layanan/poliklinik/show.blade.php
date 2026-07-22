@extends('layouts.app')

@section('title', $poli->nama . ' — RSUD Landak')

@section('content')

@include('partials.page-header', ['judul' => $poli->nama, 'parent' => 'Layanan'])

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 max-w-5xl">

        {{-- Header Card: Ikon + Nama + Deskripsi --}}
        <div class="bg-white rounded-2xl shadow p-6 md:p-8 mb-8 reveal">
            <div class="flex flex-col md:flex-row items-start gap-6">

                {{-- Ikon besar --}}
                <div class="flex-shrink-0 flex items-center justify-center w-24 h-24 md:w-32 md:h-32 rounded-2xl"
                     style="background: linear-gradient(135deg, #e8f5e9, #c8e6c9)">
                    @if($poli->tipe_ikon === 'img' && $poli->ikon)
                        <img src="{{ asset('storage/' . $poli->ikon) }}" class="w-16 h-16 object-cover rounded-xl">
                    @elseif($poli->ikon)
                        <i class="fas {{ $poli->ikon }} text-5xl md:text-6xl" style="color: #2563EB"></i>
                    @else
                        <i class="fas fa-clinic-medical text-5xl md:text-6xl" style="color: #2563EB"></i>
                    @endif
                </div>

                {{-- Konten --}}
                <div class="flex-1">
                    <h1 class="font-playfair text-2xl md:text-3xl font-bold text-gray-800 mb-2">{{ $poli->nama }}</h1>
                    {{-- Garis emas --}}
                    <div class="w-16 h-1 rounded-full mb-4" style="background: #D97706"></div>

                    @if($poli->deskripsi)
                    <div class="text-gray-600 leading-relaxed">{!! $poli->deskripsi !!}</div>
                    @else
                    <p class="text-gray-400 italic text-sm">Deskripsi poliklinik sedang diperbarui.</p>
                    @endif

                    @if($poli->jumlah_dokter > 0)
                    <div class="mt-4">
                        <span class="inline-flex items-center gap-2 text-sm font-semibold text-green-700 bg-green-50 border border-green-200 px-3 py-1.5 rounded-full">
                            <i class="fas fa-user-md"></i>
                            {{ $poli->jumlah_dokter }} Dokter Aktif
                        </span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Prosedur / Alur Layanan --}}
        @if($poli->prosedur)
        <div class="bg-blue-50 border border-blue-100 rounded-2xl p-6 md:p-8 mb-8 reveal">
            <h2 class="font-playfair text-xl font-bold text-blue-800 mb-4 flex items-center gap-2">
                <i class="fas fa-list-ol text-blue-600"></i>
                Prosedur Layanan
            </h2>
            <div class="text-gray-700 leading-relaxed text-sm">{!! $poli->prosedur !!}</div>
        </div>
        @endif

        {{-- Tim Dokter --}}
        @if($poli->dokters->isNotEmpty())
        <div class="mb-8 reveal">
            <h2 class="font-playfair text-xl font-bold text-gray-800 mb-6 flex items-center gap-2">
                <i class="fas fa-user-md" style="color: #2563EB"></i>
                Tim Dokter
            </h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5">
                @foreach($poli->dokters as $dokter)
                <div class="bg-white rounded-2xl shadow hover:shadow-md transition-all p-5 flex items-start gap-4">

                    {{-- Avatar --}}
                    @if($dokter->foto)
                        <img src="{{ Storage::url($dokter->foto) }}"
                             class="w-16 h-16 rounded-full object-cover flex-shrink-0 ring-2 ring-green-200">
                    @else
                        <div class="w-16 h-16 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xl font-bold ring-2 ring-green-200"
                             style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                            {{ strtoupper(substr($dokter->nama, 0, 1)) }}
                        </div>
                    @endif

                    {{-- Info --}}
                    <div class="flex-1 min-w-0">
                        <h3 class="font-bold text-gray-800 text-sm">{{ $dokter->nama }}</h3>
                        <span class="inline-block text-xs font-semibold px-2 py-0.5 rounded-full mt-1 mb-2"
                              style="background: #fff8e6; color: #D97706; border: 1px solid #f3d98b">
                            {{ $dokter->spesialisasi }}
                        </span>

                        @if($dokter->jadwal && count($dokter->jadwal) > 0)
                        <div class="space-y-1">
                            @foreach($dokter->jadwal as $jadwal)
                            <div class="flex items-center gap-1.5 text-xs text-gray-500">
                                <i class="fas fa-calendar-alt text-green-500 w-3"></i>
                                <span class="font-medium">{{ $jadwal['hari'] ?? '' }}</span>
                                <span class="text-gray-400">|</span>
                                <span>{{ $jadwal['jam'] ?? '' }}</span>
                            </div>
                            @endforeach
                        </div>
                        @else
                        <p class="text-xs text-gray-400">Jadwal akan segera diumumkan.</p>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
        @else
        <div class="bg-white rounded-2xl shadow p-8 text-center mb-8 reveal">
            <i class="fas fa-user-md text-5xl text-gray-200 block mb-3"></i>
            <p class="text-gray-500 font-medium">Informasi dokter sedang diperbarui.</p>
            <p class="text-gray-400 text-sm mt-1">Hubungi kami untuk informasi dokter praktek.</p>
        </div>
        @endif

        {{-- CTA --}}
        <div class="rounded-2xl overflow-hidden mb-8 reveal" style="background: linear-gradient(135deg, #1E3A8A, #2563EB)">
            <div class="p-8 text-center">
                <h3 class="font-playfair text-2xl font-bold text-white mb-2">Butuh Konsultasi?</h3>
                <p class="text-green-200 mb-6 text-sm max-w-md mx-auto">
                    Hubungi kami untuk informasi jadwal dokter, pendaftaran, dan layanan poliklinik {{ $poli->nama }}.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="tel:+62565-2025100"
                       class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm transition-all"
                       style="background: #D97706; color: #1E3A8A">
                        <i class="fas fa-phone"></i> Hubungi Kami
                    </a>
                    <a href="{{ route('layanan.poliklinik.index') }}"
                       class="inline-flex items-center justify-center gap-2 px-6 py-2.5 rounded-xl font-semibold text-sm border-2 border-white text-white hover:bg-white hover:text-green-800 transition-all">
                        <i class="fas fa-th-large"></i> Semua Poliklinik
                    </a>
                </div>
            </div>
        </div>

        {{-- Link kembali --}}
        <div class="text-center">
            <a href="{{ route('layanan.poliklinik.index') }}"
               class="inline-flex items-center gap-2 text-sm font-semibold transition-colors"
               style="color: #2563EB">
                <i class="fas fa-arrow-left"></i> Kembali ke Daftar Poliklinik
            </a>
        </div>

    </div>
</section>

@endsection
