@extends('layouts.app')

@section('title', 'Promosi Kesehatan (PKRS) — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))
@section('meta_description', 'Edukasi dan penyuluhan kesehatan resmi dari Promosi Kesehatan Rumah Sakit (PKRS) RSUD Landak.')

@section('content')

@include('partials.page-header', ['judul' => 'Promosi Kesehatan (PKRS)', 'parent' => 'Layanan'])

<section class="py-14 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-6xl">

        <div class="text-center mb-12 reveal">
            <h2 class="font-playfair text-3xl font-bold text-gray-800 mb-2">Edukasi & Promosi Kesehatan</h2>
            <p class="text-gray-500 max-w-xl mx-auto text-sm">
                Informasi, penyuluhan, dan artikel kesehatan terpercaya yang disusun oleh tim medis {{ $settings['nama_rs'] ?? 'RSUD Landak' }}.
            </p>
        </div>

        @if($items->isEmpty())
        <div class="text-center py-20 bg-white rounded-2xl shadow-sm reveal">
            <div class="w-20 h-20 rounded-full bg-blue-50 flex items-center justify-center mx-auto mb-4 text-primary">
                <i class="fas fa-heartbeat text-3xl"></i>
            </div>
            <p class="text-gray-500 text-lg font-semibold">Materi Edukasi Belum Tersedia</p>
            <p class="text-gray-400 text-sm mt-1">Kami sedang mempersiapkan artikel edukasi terbaik untuk Anda.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-10">
            @foreach($items as $item)
            <div class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-lg transition-all border border-gray-100 flex flex-col justify-between reveal group">
                <div>
                    {{-- Gambar --}}
                    <div class="h-48 overflow-hidden relative bg-gray-100">
                        @if($item->gambar)
                            <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300">
                        @else
                            <div class="w-full h-full flex items-center justify-center bg-gradient-to-br from-primary to-blue-600 text-white/30 text-5xl">
                                <i class="fas fa-hand-holding-medical"></i>
                            </div>
                        @endif
                    </div>

                    {{-- Konten --}}
                    <div class="p-6">
                        <div class="flex items-center gap-3 text-xs text-gray-400 mb-3">
                            <span class="flex items-center gap-1"><i class="far fa-user"></i> {{ $item->penulis ?? 'Admin PKRS' }}</span>
                            <span class="flex items-center gap-1"><i class="far fa-eye"></i> {{ number_format($item->views) }} views</span>
                        </div>
                        <h3 class="font-bold text-gray-800 text-lg mb-2 line-clamp-2 leading-snug group-hover:text-primary transition-colors">
                            {{ $item->judul }}
                        </h3>
                        <div class="text-gray-500 text-sm line-clamp-3 leading-relaxed mb-4">
                            {{ strip_tags($item->konten) }}
                        </div>
                    </div>
                </div>

                {{-- Footer Card --}}
                <div class="px-6 pb-6 pt-2 border-t border-gray-50 flex items-center justify-between">
                    <span class="text-xs text-gray-400"><i class="far fa-calendar-alt"></i> {{ $item->created_at->format('d M Y') }}</span>
                    <a href="{{ route('informasi.pkrs.show', $item->slug) }}" class="text-xs font-bold text-primary hover:text-green-700 transition-colors flex items-center gap-1">
                        Baca Selengkapnya <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>

        {{-- Pagination --}}
        <div class="mt-8">
            {{ $items->links() }}
        </div>
        @endif

    </div>
</section>

@endsection
