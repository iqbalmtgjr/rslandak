@extends('layouts.app')
@section('title', 'Struktur Organisasi — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Struktur Organisasi', 'parent' => 'Profil RS'])

<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-5xl">

        <div class="text-center mb-10 reveal">
            <h2 class="font-playfair text-2xl font-bold text-dark mb-2">Bagan Struktur Organisasi</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto"></div>
        </div>

        @if($gambar)
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
