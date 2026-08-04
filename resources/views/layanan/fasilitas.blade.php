@extends('layouts.app')
<<<<<<< HEAD
=======

>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
@section('title', 'Fasilitas — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Fasilitas', 'parent' => 'Layanan'])

<section class="py-14 bg-gray-50 min-h-screen">
<<<<<<< HEAD
    <div class="container mx-auto px-4 max-w-5xl" x-data="{ activeTab: '{{ request('tab', 'klinik') }}' }">

        <div class="text-center mb-10 reveal">
            <h2 class="font-playfair text-3xl font-bold text-gray-800 mb-2">Fasilitas Pelayanan</h2>
=======
    <div class="container mx-auto px-4 max-w-5xl">

        <div class="text-center mb-10 reveal">
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Fasilitas Pelayanan</h2>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                Fasilitas dan sarana penunjang yang tersedia di {{ $settings['nama_rs'] ?? 'RSUD Landak' }}
                untuk mendukung pelayanan kesehatan yang optimal.
            </p>
<<<<<<< HEAD
            <div class="w-24 h-1 bg-gold rounded-full mx-auto mt-4"></div>
        </div>

        {{-- Categories Navigation Tabs --}}
        <div class="flex justify-center gap-2 flex-wrap mb-10 bg-white p-2 rounded-2xl shadow-sm border border-gray-100 max-w-3xl mx-auto reveal">
            @foreach([
                ['klinik', 'Fasilitas Klinik', 'fa-clinic-medical'],
                ['parkir', 'Fasilitas Parkir', 'fa-parking'],
                ['difabel', 'Fasilitas Difabel', 'fa-wheelchair'],
                ['prioritas', 'Fasilitas Prioritas', 'fa-baby-carriage']
            ] as [$key, $label, $icon])
                <button type="button" @click="activeTab = '{{ $key }}'"
                        :class="activeTab === '{{ $key }}' ? 'bg-primary text-white shadow-sm' : 'text-gray-600 hover:bg-gray-100'"
                        class="flex items-center gap-2 px-5 py-3 rounded-xl text-sm font-semibold transition-all">
                    <i class="fas {{ $icon }}"></i>
                    {{ $label }}
                </button>
            @endforeach
        </div>

        {{-- Categories Content --}}
        @foreach(['klinik', 'parkir', 'difabel', 'prioritas'] as $kat)
            <div x-show="activeTab === '{{ $kat }}'" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 class="space-y-6">
                
                @php
                    $items = $fasilitasByKategori->get($kat, collect());
                @endphp

                @forelse($items as $item)
                    <div class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all overflow-hidden border border-gray-100 reveal">
                        <div class="grid grid-cols-1 md:grid-cols-5">
                            {{-- Gambar --}}
                            <div class="md:col-span-2 h-56 md:h-auto min-h-[14rem] bg-gray-50 flex items-center justify-center overflow-hidden">
                                @if($item->gambar)
                                    <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full flex flex-col items-center justify-center"
                                         style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #60A5FA 100%)">
                                        @php
                                            $icon = [
                                                'klinik' => 'fa-clinic-medical',
                                                'parkir' => 'fa-parking',
                                                'difabel' => 'fa-wheelchair',
                                                'prioritas' => 'fa-baby-carriage'
                                            ][$kat] ?? 'fa-hospital';
                                        @endphp
                                        <i class="fas {{ $icon }} text-white/30 text-7xl"></i>
                                    </div>
                                @endif
                            </div>

                            {{-- Konten --}}
                            <div class="md:col-span-3 p-6 md:p-8 flex flex-col justify-center">
                                <h3 class="font-bold text-gray-800 text-lg uppercase tracking-wide mb-1 leading-tight">
                                    {{ $item->nama }}
                                </h3>
                                <div class="w-12 h-1 bg-gold mb-4"></div>
                                <div class="text-gray-600 text-sm leading-relaxed prose prose-sm max-w-none">
                                    {!! $item->deskripsi !!}
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-20 bg-white rounded-2xl border border-gray-100 shadow-sm reveal">
                        <div class="w-20 h-20 rounded-full flex items-center justify-center mx-auto mb-4"
                             style="background: linear-gradient(135deg, #f3f4f6, #e5e7eb)">
                            <i class="fas fa-hospital text-3xl text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-lg font-medium">Informasi sedang diperbarui.</p>
                        <p class="text-gray-400 text-sm mt-1">Belum ada fasilitas di kategori ini.</p>
                    </div>
                @endforelse
            </div>
        @endforeach
=======
        </div>

        @forelse($items as $item)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all overflow-hidden mb-6 reveal">
            <div class="grid grid-cols-1 md:grid-cols-5">
                {{-- Gambar --}}
                <div class="md:col-span-2 h-56 md:h-auto min-h-[14rem]">
                    @if($item->gambar)
                        <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center"
                             style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #60A5FA 100%)">
                            <i class="fas fa-hospital text-white/30 text-7xl"></i>
                        </div>
                    @endif
                </div>

                {{-- Konten --}}
                <div class="md:col-span-3 p-6 md:p-8 flex flex-col justify-center">
                    <h3 class="font-bold text-gray-800 text-lg uppercase tracking-wide mb-1 leading-tight">
                        {{ $item->nama }}
                    </h3>
                    <div class="w-12 h-1 bg-gold mb-4"></div>
                    <div class="text-gray-600 text-sm leading-relaxed prose prose-sm max-w-none">
                        {!! $item->deskripsi !!}
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="text-center py-20 reveal">
            <div class="w-24 h-24 rounded-full flex items-center justify-center mx-auto mb-4"
                 style="background: linear-gradient(135deg, #dbeafe, #bfdbfe)">
                <i class="fas fa-hospital text-4xl" style="color: #2563EB"></i>
            </div>
            <p class="text-gray-500 text-lg font-medium">Informasi sedang diperbarui.</p>
            <p class="text-gray-400 text-sm mt-1">Silakan hubungi kami untuk informasi lebih lanjut.</p>
        </div>
        @endforelse

        <div class="mt-10 text-center reveal">
            <a href="{{ route('layanan.fasilitas-difabel') }}"
               class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-sm border-2 border-primary text-primary hover:bg-primary hover:text-white transition-all">
                <i class="fas fa-wheelchair"></i> Lihat Fasilitas Difabel
            </a>
        </div>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7

    </div>
</section>

@endsection
