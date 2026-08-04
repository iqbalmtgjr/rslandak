@extends('layouts.app')
@section('title', 'Struktur Organisasi — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Struktur Organisasi', 'parent' => 'Profil RS'])

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="text-center mb-12 reveal">
            <h2 class="font-playfair text-3xl font-bold text-dark mb-3">Struktur Organisasi</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                Susunan kepengurusan dan pembagian bidang pelayanan di {{ $settings['nama_rs'] ?? 'RSUD Landak' }}
            </p>
            <div class="w-24 h-1 bg-gold rounded-full mx-auto mt-4"></div>
        </div>

        @php
            $strukturJson = $settings['struktur_organisasi_json'] ?? null;
            $bidangList = $strukturJson ? json_decode($strukturJson, true) : [];
        @endphp

        @if(!empty($bidangList))
            {{-- Dynamic grid per division --}}
            <div class="space-y-12">
                @foreach($bidangList as $bidang)
                    @if(!empty($bidang['nama_bidang']) && !empty($bidang['anggota']))
                        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 reveal">
                            <h3 class="font-playfair text-xl font-bold text-primary mb-6 flex items-center gap-2 border-b pb-3">
                                <i class="fas fa-sitemap text-gold"></i>
                                {{ $bidang['nama_bidang'] }}
                            </h3>

                            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
                                @foreach($bidang['anggota'] as $anggota)
                                    <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 text-center hover:shadow-md transition-shadow group">
                                        {{-- Photo --}}
                                        <div class="aspect-[3/4] w-28 md:w-32 mx-auto rounded-lg overflow-hidden mb-3 border border-gray-200 bg-white flex items-center justify-center relative">
                                            @if(!empty($anggota['foto']))
                                                <img src="{{ Storage::url($anggota['foto']) }}" alt="{{ $anggota['nama'] }}"
                                                     class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                                            @else
                                                <div class="w-full h-full flex flex-col items-center justify-center" style="background: linear-gradient(135deg, #f0fdf4, #dcfce7)">
                                                    <i class="fas fa-user-md text-3xl text-green-600"></i>
                                                </div>
                                            @endif
                                        </div>

                                        {{-- Detail --}}
                                        <h4 class="font-bold text-gray-800 text-xs md:text-sm leading-snug line-clamp-2 min-h-[2.5rem] flex items-center justify-center">
                                            {{ $anggota['nama'] ?? '-' }}
                                        </h4>
                                        <p class="text-gold font-semibold text-[10px] md:text-xs uppercase tracking-wider mt-1 mb-0.5">
                                            {{ $anggota['jabatan'] ?? '-' }}
                                        </p>
                                        @if(!empty($anggota['nip']))
                                            <p class="text-gray-400 text-[10px] md:text-xs">
                                                {{ $anggota['nip'] }}
                                            </p>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        @elseif($gambar)
            {{-- Fallback: Bagan Struktur (Gambar) --}}
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 p-4 overflow-x-auto reveal">
                <img src="{{ Storage::url($gambar) }}" alt="Struktur Organisasi {{ $nama_rs }}"
                     class="mx-auto rounded-lg" style="min-width: 640px; max-width: 100%">
            </div>
            <p class="text-center text-gray-400 text-xs mt-3">Geser ke samping untuk melihat bagan penuh pada layar kecil.</p>
        @else
            <div class="text-center py-20 reveal">
                <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4"
                     style="background: linear-gradient(135deg, #dbeafe, #bfdbfe)">
                    <i class="fas fa-sitemap text-4xl" style="color: #2563EB"></i>
                </div>
                <p class="text-gray-500 text-lg font-medium">Bagan struktur organisasi sedang diperbarui.</p>
            </div>
        @endif

        @if($keterangan)
            <div class="mt-10 bg-gray-50 rounded-2xl p-6 md:p-8 border border-gray-100 reveal">
                <h3 class="font-bold text-dark mb-1">Keterangan</h3>
                <div class="w-12 h-1 bg-gold mb-4"></div>
                <div class="text-gray-700 text-sm leading-relaxed prose prose-sm max-w-none">
                    {!! $keterangan !!}
                </div>
            </div>
        @endif

    </div>
</section>

@endsection
