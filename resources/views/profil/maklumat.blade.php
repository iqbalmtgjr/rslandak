@extends('layouts.app')
@section('title', 'Maklumat Pelayanan — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Maklumat Pelayanan', 'parent' => 'Profil RS'])

<section class="py-16 bg-gray-50">
    <div class="container mx-auto px-4 max-w-5xl">
        @if(!empty($maklumat_gambar))
            {{-- Centered A4 Image Display --}}
            <div class="text-center mb-8">
                <p class="text-gray-500 text-sm">Berikut adalah Maklumat Pelayanan resmi {{ $settings['nama_rs'] ?? 'RSUD Landak' }}</p>
            </div>
            <div class="bg-white rounded-2xl shadow-xl border border-gray-150 p-4 max-w-2xl mx-auto reveal">
                <img src="{{ Storage::url($maklumat_gambar) }}" alt="Maklumat Pelayanan {{ $nama_rs }}"
                     class="mx-auto rounded-lg w-full h-auto shadow-md">
            </div>
            
            <div class="mt-8 p-5 rounded-xl border-l-4 border-gold bg-amber-50 max-w-2xl mx-auto">
                <p class="text-dark text-sm">
                    <i class="fas fa-info-circle text-gold mr-1"></i>
                    Apabila pelayanan kami tidak sesuai maklumat ini, sampaikan melalui
                    <a href="{{ route('layanan.pengaduan') }}" class="text-primary font-semibold hover:underline">kanal pengaduan</a>.
                </p>
            </div>
        @else
            {{-- Old Grid Layout fallback --}}
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
                {{-- KIRI: Foto RS --}}
                <div class="photo-deco relative">
                    @if($gambar)
                        <img src="{{ Storage::url($gambar) }}" alt="{{ $nama_rs }}"
                             class="w-full h-96 object-cover rounded-xl shadow-xl">
                    @else
                        <div class="w-full h-96 rounded-xl shadow-xl flex items-center justify-center relative overflow-hidden"
                             style="background: linear-gradient(135deg, #1E3A8A, #2563EB, #60A5FA)">
                            <i class="fas fa-hospital text-white/20 text-9xl"></i>
                            <div class="absolute bottom-6 left-6 right-6 text-center">
                                <div class="text-white font-playfair text-xl font-bold">{{ $nama_rs }}</div>
                            </div>
                        </div>
                    @endif
                    <div class="absolute -top-4 -left-4 w-16 h-16 bg-gold rounded-lg z-10 opacity-80"></div>
                    <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-lg z-10" style="background:#1E3A8A; opacity:0.7"></div>
                </div>

                {{-- KANAN: Isi Maklumat --}}
                <div>
                    <div class="border-l-4 border-gold pl-5 mb-8">
                        <h2 class="font-playfair text-3xl font-bold text-dark">Maklumat Pelayanan</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $nama_rs }}</p>
                    </div>

                    @if($teks)
                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        {!! $teks !!}
                    </div>
                    @else
                    <p class="text-gray-400 italic">Isi maklumat pelayanan sedang diperbarui.</p>
                    @endif

                    <div class="mt-8 p-5 rounded-xl border-l-4 border-gold bg-amber-50">
                        <p class="text-dark text-sm">
                            <i class="fas fa-info-circle text-gold mr-1"></i>
                            Apabila pelayanan kami tidak sesuai maklumat ini, sampaikan melalui
                            <a href="{{ route('layanan.pengaduan') }}" class="text-primary font-semibold hover:underline">kanal pengaduan</a>.
                        </p>
                    </div>
                </div>
            </div>
        @endif
    </div>
</section>

@endsection
