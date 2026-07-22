@extends('layouts.app')
@section('title', 'Visi & Misi — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Visi & Misi', 'parent' => 'Profil RS'])

<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- KIRI: Foto / Placeholder --}}
            <div class="photo-deco relative">
                @if($foto)
                    <img src="{{ Storage::url($foto) }}" alt="RSUD Landak"
                         class="w-full h-96 object-cover rounded-xl shadow-xl">
                @else
                    <div class="w-full h-96 rounded-xl shadow-xl flex items-center justify-center relative overflow-hidden"
                         style="background: linear-gradient(135deg, #1E3A8A, #2563EB, #60A5FA)">
                        {{-- Ornamen palang medis --}}
                        <svg class="w-32 h-32 text-white/20" viewBox="0 0 100 100" fill="currentColor">
                            <rect x="35" y="10" width="30" height="80" rx="4"/>
                            <rect x="10" y="35" width="80" height="30" rx="4"/>
                        </svg>
                        <div class="absolute bottom-6 left-6 right-6 text-center">
                            <div class="text-white font-playfair text-xl font-bold">{{ $nama_rs }}</div>
                            <div class="text-green-200 text-sm mt-1">Melayani dengan Sepenuh Hati</div>
                        </div>
                    </div>
                @endif
                {{-- Dekoratif elemen --}}
                <div class="absolute -top-4 -left-4 w-16 h-16 bg-gold rounded-lg z-10 opacity-80"></div>
                <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-lg z-10" style="background:#1E3A8A; opacity:0.7"></div>
            </div>

            {{-- KANAN: Konten --}}
            <div>
                <div class="border-l-4 border-gold pl-5 mb-8">
                    <h2 class="font-playfair text-3xl font-bold text-dark">{{ $nama_rs }}</h2>
                    <p class="text-gray-500 text-sm mt-1">Landak, Kalimantan Barat</p>
                </div>

                {{-- Visi --}}
                <div class="mb-6">
                    <div class="text-xs font-semibold uppercase tracking-widest text-gold mb-2">Visi</div>
                    <p class="text-gray-700 italic text-lg leading-relaxed border-l-2 border-gray-200 pl-4">
                        "{{ $visi }}"
                    </p>
                </div>

                <hr class="border-gray-200 my-6">

                {{-- Misi --}}
                <div class="mb-6">
                    <div class="text-xs font-semibold uppercase tracking-widest text-gold mb-4">Misi</div>
                    <ul class="space-y-3">
                        @foreach($misi as $i => $item)
                        <li class="flex gap-3">
                            <span class="flex-shrink-0 w-7 h-7 rounded-full bg-primary text-white text-sm flex items-center justify-center font-bold mt-0.5">{{ $i + 1 }}</span>
                            <span class="text-gray-700 leading-relaxed">{{ $item }}</span>
                        </li>
                        @endforeach
                    </ul>
                </div>

                {{-- Motto --}}
                @if($motto)
                <div class="mt-6 p-4 rounded-xl border-l-4 border-gold relative" style="background: #fffbeb">
                    <i class="fas fa-quote-left text-4xl text-gold/30 absolute top-3 left-3"></i>
                    <p class="text-dark font-playfair italic text-lg pl-6 font-semibold">"{{ $motto }}"</p>
                    <p class="text-gray-500 text-sm pl-6 mt-1">— Motto {{ $nama_rs }}</p>
                </div>
                @endif
            </div>

        </div>
    </div>
</section>

@endsection
