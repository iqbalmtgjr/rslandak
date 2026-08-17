@extends('layouts.app')

@section('title', $item->judul . ' — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))
@section('meta_description', Str::limit(strip_tags($item->konten), 150))

@section('content')

@include('partials.page-header', ['judul' => 'Detail Edukasi PKRS', 'parent' => 'Layanan'])

<section class="py-12 bg-gray-50">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            {{-- Artikel Utama --}}
            <article class="lg:col-span-2">
                <div class="bg-white rounded-2xl shadow-sm overflow-hidden">
                    
                    {{-- Banner --}}
                    @if($item->gambar)
                        <img src="{{ Storage::url($item->gambar) }}" alt="{{ $item->judul }}" class="w-full h-80 object-cover">
                    @else
                        <div class="w-full h-64 bg-gradient-to-br from-primary to-blue-700 flex items-center justify-center">
                            <i class="fas fa-hand-holding-medical text-7xl text-white opacity-30"></i>
                        </div>
                    @endif

                    <div class="p-8">
                        {{-- Meta --}}
                        <div class="flex flex-wrap items-center gap-4 text-xs text-gray-400 mb-4 pb-4 border-b border-gray-100">
                            <span class="bg-blue-50 text-blue-700 px-3 py-1 rounded-full font-bold">Edukasi PKRS</span>
                            <span><i class="far fa-calendar-alt mr-1"></i> {{ $item->created_at->format('d M Y') }}</span>
                            <span><i class="far fa-user mr-1"></i> {{ $item->penulis ?? 'Admin PKRS' }}</span>
                            <span class="ml-auto"><i class="far fa-eye mr-1"></i> {{ number_format($item->views) }} kali dibaca</span>
                        </div>

                        {{-- Judul --}}
                        <h1 class="font-playfair text-2xl md:text-3xl font-bold text-gray-800 leading-tight mb-6">
                            {{ $item->judul }}
                        </h1>

                        {{-- Konten --}}
                        <div class="prose prose-blue max-w-none text-gray-700 leading-relaxed article-content">
                            {!! $item->konten !!}
                        </div>

                        {{-- Share Buttons --}}
                        <div class="mt-8 pt-6 border-t border-gray-100 flex items-center gap-3 flex-wrap">
                            <span class="text-sm text-gray-500 font-semibold">Bagikan Artikel:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->url()) }}"
                               target="_blank" rel="noopener"
                               class="flex items-center gap-2 px-4 py-2 bg-blue-600 text-white rounded-full text-xs font-bold hover:bg-blue-700 transition-colors">
                                <i class="fab fa-facebook-f"></i> Facebook
                            </a>
                            <a href="https://twitter.com/intent/tweet?url={{ urlencode(request()->url()) }}&text={{ urlencode($item->judul) }}"
                               target="_blank" rel="noopener"
                               class="flex items-center gap-2 px-4 py-2 bg-sky-500 text-white rounded-full text-xs font-bold hover:bg-sky-600 transition-colors">
                                <i class="fab fa-twitter"></i> Twitter
                            </a>
                            <a href="https://wa.me/?text={{ urlencode($item->judul . ' ' . request()->url()) }}"
                               target="_blank" rel="noopener"
                               class="flex items-center gap-2 px-4 py-2 bg-green-500 text-white rounded-full text-xs font-bold hover:bg-green-600 transition-colors">
                                <i class="fab fa-whatsapp"></i> WhatsApp
                            </a>
                        </div>

                        {{-- Kembali --}}
                        <div class="mt-8">
                            <a href="{{ route('informasi.pkrs') }}" class="inline-flex items-center gap-2 text-primary font-bold text-sm hover:text-green-700 transition-colors">
                                <i class="fas fa-arrow-left"></i> Kembali ke Daftar PKRS
                            </a>
                        </div>

                    </div>
                </div>
            </article>

            {{-- Sidebar --}}
            <aside class="lg:col-span-1 space-y-6">
                
                {{-- Edukasi PKRS Terbaru --}}
                @if($recent->isNotEmpty())
                <div class="bg-white rounded-2xl shadow-sm p-6">
                    <h3 class="font-playfair text-lg font-bold text-gray-800 mb-4 pb-3 border-b-2 border-primary">
                        Edukasi Terbaru
                    </h3>
                    <ul class="space-y-4">
                        @foreach($recent as $rec)
                        <li class="flex gap-3 group">
                            <a href="{{ route('informasi.pkrs.show', $rec->slug) }}" class="flex-shrink-0 w-20 h-16 rounded-lg overflow-hidden bg-gray-100">
                                @if($rec->gambar)
                                    <img src="{{ Storage::url($rec->gambar) }}" alt="{{ $rec->judul }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                @else
                                    <div class="w-full h-full bg-gradient-to-br from-primary to-blue-600 flex items-center justify-center text-white/40">
                                        <i class="fas fa-hand-holding-medical"></i>
                                    </div>
                                @endif
                            </a>
                            <div class="flex-1 min-w-0">
                                <a href="{{ route('informasi.pkrs.show', $rec->slug) }}" class="text-sm font-semibold text-gray-700 group-hover:text-primary transition-colors leading-snug line-clamp-2 block">
                                    {{ $rec->judul }}
                                </a>
                                <p class="text-xs text-gray-400 mt-1">
                                    {{ $rec->created_at->format('d M Y') }}
                                </p>
                            </div>
                        </li>
                        @endforeach
                    </ul>
                </div>
                @endif

                {{-- Contact Box --}}
                <div class="bg-gradient-to-br from-primary to-blue-700 rounded-2xl p-6 text-white text-center">
                    <i class="fas fa-phone-alt text-3xl mb-3 text-gold"></i>
                    <h3 class="font-playfair text-lg font-bold mb-2">Butuh Informasi?</h3>
                    <p class="text-blue-100 text-sm mb-4">Hubungi kami untuk informasi lebih lanjut seputar pelayanan.</p>
                    <a href="tel:{{ $settings['telepon'] ?? '' }}" class="block bg-gold text-white font-bold py-2.5 px-4 rounded-full text-sm hover:bg-yellow-500 transition-colors shadow">
                        {{ $settings['telepon'] ?? 'Hubungi Kami' }}
                    </a>
                </div>

            </aside>

        </div>
    </div>
</section>

@endsection
