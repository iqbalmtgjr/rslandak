@extends('layouts.app')

@section('title', 'Fasilitas Difabel — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Fasilitas Difabel', 'parent' => 'Layanan'])

<section class="py-14 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">

        <div class="text-center mb-10 reveal">
            <div class="w-16 h-16 rounded-full flex items-center justify-center mx-auto mb-4"
                 style="background: linear-gradient(135deg, #1E3A8A, #2563EB)">
                <i class="fas fa-wheelchair text-white text-2xl"></i>
            </div>
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Fasilitas Ramah Difabel</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                {{ $settings['nama_rs'] ?? 'RSUD Landak' }} berkomitmen memberikan pelayanan yang setara dan mudah
                diakses oleh penyandang disabilitas serta lansia.
            </p>
        </div>

        @forelse($items as $item)
        <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all overflow-hidden mb-6 reveal">
            <div class="grid grid-cols-1 md:grid-cols-5">
                <div class="md:col-span-2 h-56 md:h-auto min-h-[14rem]">
                    @if($item->gambar)
                        <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->nama }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center"
                             style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 50%, #60A5FA 100%)">
                            <i class="fas fa-wheelchair text-white/30 text-7xl"></i>
                        </div>
                    @endif
                </div>
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
                <i class="fas fa-wheelchair text-4xl" style="color: #2563EB"></i>
            </div>
            <p class="text-gray-500 text-lg font-medium">Informasi sedang diperbarui.</p>
            <p class="text-gray-400 text-sm mt-1">Silakan hubungi kami untuk informasi lebih lanjut.</p>
        </div>
        @endforelse

    </div>
</section>

@endsection
