@extends('layouts.app')
@section('title', 'Survei Kepuasan Masyarakat (SKM) — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Survei Kepuasan Masyarakat (SKM)', 'parent' => 'Profil RS'])

<section class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">

            {{-- KIRI: Foto/Gambar SKM --}}
            <div class="photo-deco relative">
                @if($gambar)
                    <img src="{{ Storage::url($gambar) }}" alt="Survei Kepuasan Masyarakat (SKM)"
                         class="w-full h-auto object-contain rounded-xl shadow-xl">
                @else
                    <div class="w-full h-96 rounded-xl shadow-xl flex items-center justify-center relative overflow-hidden"
                          style="background: linear-gradient(135deg, #1E3A8A, #2563EB, #60A5FA)">
                        <i class="fas fa-chart-bar text-white/20 text-9xl"></i>
                        <div class="absolute bottom-6 left-6 right-6 text-center">
                            <div class="text-white font-playfair text-xl font-bold">Survei Kepuasan Masyarakat</div>
                        </div>
                    </div>
                @endif
                <div class="absolute -top-4 -left-4 w-16 h-16 bg-gold rounded-lg z-10 opacity-80"></div>
                <div class="absolute -bottom-3 -right-3 w-10 h-10 rounded-lg z-10" style="background:#1E3A8A; opacity:0.7"></div>
            </div>

            {{-- KANAN: Isi SKM --}}
            <div>
                <div class="border-l-4 border-gold pl-5 mb-8">
                    <h2 class="font-playfair text-3xl font-bold text-dark">Survei Kepuasan Masyarakat (SKM)</h2>
                    <p class="text-gray-500 text-sm mt-1">{{ $nama_rs }}</p>
                </div>

                @if($teks)
                <div class="text-gray-700 leading-relaxed prose prose-sm max-w-none">
                    {!! $teks !!}
                </div>
                @else
                <p class="text-gray-400 italic">Informasi survei kepuasan masyarakat sedang diperbarui.</p>
                @endif

                <div class="mt-8 p-5 rounded-xl border-l-4 border-gold" style="background: #fffbeb">
                    <p class="text-dark text-sm">
                        <i class="fas fa-info-circle text-gold mr-1"></i>
                        Terima kasih atas partisipasi Anda dalam mengisi Survei Kepuasan Masyarakat. Masukan Anda sangat berharga untuk peningkatan pelayanan kami.
                    </p>
                </div>
            </div>

        </div>
    </div>
</section>

@endsection
