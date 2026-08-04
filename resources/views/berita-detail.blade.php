@extends('layouts.app')
@section('title', $berita->judul . ' - ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')
<div class="py-12 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Artikel -->
            <article class="lg:col-span-2">
                <div class="bg-white rounded-xl shadow p-6 md:p-8">
                    <span class="text-xs px-2 py-0.5 rounded font-semibold
                        {{ $berita->kategori === 'Berita' ? 'bg-blue-100 text-blue-700' : ($berita->kategori === 'Pengumuman' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                        {{ $berita->kategori }}
                    </span>
                    <h1 class="font-playfair text-2xl md:text-3xl font-bold text-dark mt-3 mb-3">{{ $berita->judul }}</h1>
                    <div class="flex items-center gap-4 text-sm text-gray-500 mb-6 pb-4 border-b">
                        <span><i class="fas fa-user mr-1"></i>{{ $berita->penulis }}</span>
                        <span><i class="fas fa-calendar mr-1"></i>{{ $berita->created_at->format('d F Y') }}</span>
                        <span><i class="fas fa-eye mr-1"></i>{{ number_format($berita->views) }} views</span>
                    </div>
                    @if($berita->gambar)
                    <img src="{{ Storage::url($berita->gambar) }}" alt="{{ $berita->judul }}" class="w-full h-64 object-cover rounded-xl mb-6">
                    @endif
                    <div class="prose prose-green max-w-none text-gray-700 leading-relaxed">
                        {!! $berita->konten !!}
                    </div>
                </div>
            </article>

            <!-- Sidebar -->
            <aside>
                <div class="bg-white rounded-xl shadow p-5 mb-6">
                    <h3 class="font-bold text-gray-800 mb-4">Berita Lainnya</h3>
                    <div class="space-y-4">
                        @foreach($beritaLain as $b)
                        <a href="{{ route('berita.detail', $b->slug) }}" class="flex gap-3 hover:bg-gray-50 rounded-lg p-2 -mx-2 transition-colors">
                            @if($b->gambar)
                            <img src="{{ Storage::url($b->gambar) }}" class="w-16 h-12 object-cover rounded flex-shrink-0">
                            @else
                            <div class="w-16 h-12 rounded flex-shrink-0" style="background: linear-gradient(135deg,#2563EB,#60A5FA)"></div>
                            @endif
                            <div>
                                <div class="text-sm font-medium text-gray-800 line-clamp-2">{{ $b->judul }}</div>
                                <div class="text-xs text-gray-400 mt-1">{{ $b->created_at->format('d M Y') }}</div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>
                <a href="{{ route('home') }}" class="block text-center bg-primary hover:bg-dark text-white px-4 py-2 rounded-lg text-sm font-semibold transition-colors">
                    <i class="fas fa-arrow-left mr-1"></i> Kembali ke Beranda
                </a>
            </aside>
        </div>
    </div>
</div>
@endsection
