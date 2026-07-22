@extends('layouts.app')
@section('title', 'Direktur RS — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Direktur Rumah Sakit', 'parent' => 'Profil RS'])

<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-5 gap-10">

            {{-- KIRI: Foto + Info --}}
            <div class="lg:col-span-2">
                {{-- Foto --}}
                <div class="direktur-frame w-full max-w-xs mx-auto lg:mx-0 shadow-xl">
                    @if($foto)
                        <img src="{{ Storage::url($foto) }}" alt="{{ $nama }}"
                             class="w-full h-96 object-cover rounded-xl">
                    @else
                        <div class="w-full h-96 rounded-xl flex flex-col items-center justify-center relative"
                             style="background: linear-gradient(160deg, #1E3A8A, #2563EB)">
                            {{-- Bintang dekoratif --}}
                            <svg class="w-16 h-16 text-gold/40 mb-4" viewBox="0 0 24 24" fill="currentColor">
                                <path d="M12 2l2.4 7.4H22l-6.2 4.5 2.4 7.4L12 17l-6.2 4.3 2.4-7.4L2 9.4h7.6z"/>
                            </svg>
                            <div class="text-white text-5xl font-bold font-playfair">
                                {{ strtoupper(substr(trim(explode(' ', $nama)[count(explode(' ', $nama)) - 1] ?? 'D'), 0, 1)) }}
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Info Card --}}
                <div class="mt-5 bg-white border border-gray-100 rounded-xl shadow p-5 max-w-xs mx-auto lg:mx-0">
                    <h3 class="font-playfair font-bold text-dark text-lg leading-tight">{{ $nama }}</h3>
                    <div class="text-gold font-semibold text-sm mt-1">{{ $jabatan }}</div>
                    @if($nrp)
                    <div class="text-gray-400 text-xs mt-1">{{ $nrp }}</div>
                    @endif
                    <div class="mt-3">
                        <span class="text-xs text-white font-semibold px-3 py-1 rounded-full" style="background:#1E3A8A">
                            Direktur RSUD Landak
                        </span>
                    </div>
                </div>
            </div>

            {{-- KANAN: Sambutan + Riwayat --}}
            <div class="lg:col-span-3">
                {{-- Sambutan --}}
                <div class="relative mb-8">
                    <i class="fas fa-quote-left text-6xl text-gold/20 absolute -top-2 -left-2"></i>
                    <h2 class="font-playfair italic text-2xl text-dark mb-5 pl-8">Sambutan Direktur</h2>
                    <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                        {!! $sambutan !!}
                    </div>
                    <div class="mt-6 pt-4 border-t border-gray-100">
                        <p class="text-gray-500 text-sm italic">Hormat kami,</p>
                        <p class="font-playfair font-bold text-dark mt-1">{{ $nama }}</p>
                        <p class="text-gold text-sm">{{ $jabatan }}</p>
                    </div>
                </div>

                {{-- Divider --}}
                <div class="border-t-2 border-gold/30 my-8"></div>

                {{-- Pendidikan & Riwayat --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Pendidikan --}}
                    @if(count($pendidikan) > 0)
                    <div>
                        <h3 class="font-bold text-dark text-sm uppercase tracking-wide mb-4 flex items-center gap-2">
                            <i class="fas fa-graduation-cap text-primary"></i> Riwayat Pendidikan
                        </h3>
                        <ul class="space-y-3">
                            @foreach($pendidikan as $p)
                            <li class="flex gap-3 items-start text-sm text-gray-700">
                                <i class="fas fa-graduation-cap text-primary mt-0.5 flex-shrink-0"></i>
                                <span>{{ $p }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Riwayat Jabatan --}}
                    @if(count($riwayat) > 0)
                    <div>
                        <h3 class="font-bold text-dark text-sm uppercase tracking-wide mb-4 flex items-center gap-2">
                            <i class="fas fa-briefcase text-gold"></i> Riwayat Jabatan
                        </h3>
                        <ul class="space-y-3">
                            @foreach($riwayat as $r)
                            <li class="flex gap-3 items-start text-sm text-gray-700">
                                <i class="fas fa-briefcase text-gold mt-0.5 flex-shrink-0"></i>
                                <span>{{ $r }}</span>
                            </li>
                            @endforeach
                        </ul>
                    </div>
                    @endif
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
