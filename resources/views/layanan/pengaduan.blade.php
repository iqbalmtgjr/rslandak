@extends('layouts.app')

@section('title', 'Informasi & Pengaduan — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Informasi & Pengaduan', 'parent' => 'Layanan'])

<section class="py-14 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-5xl">

        <div class="text-center mb-10 reveal">
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Sampaikan Informasi & Pengaduan Anda</h2>
            <p class="text-gray-500 max-w-2xl mx-auto text-sm">
                {{ $settings['nama_rs'] ?? 'RSUD Landak' }} membuka kanal pengaduan bagi masyarakat.
                Setiap masukan, saran, maupun keluhan akan kami tindak lanjuti sesuai ketentuan yang berlaku.
            </p>
        </div>

        {{-- Kanal Pengaduan --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-10">
            @if(!empty($settings['pengaduan_wa']))
            <a href="https://wa.me/{{ $settings['pengaduan_wa'] }}" target="_blank" rel="noopener"
               class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all p-6 flex items-start gap-4 reveal">
                <div class="w-12 h-12 rounded-xl bg-green-50 flex items-center justify-center flex-shrink-0">
                    <i class="fab fa-whatsapp text-2xl text-green-600"></i>
                </div>
                <div>
                    <div class="font-bold text-gray-800 mb-1">WhatsApp Pengaduan</div>
                    <div class="text-gray-500 text-sm">{{ $settings['pengaduan_wa'] }}</div>
                </div>
            </a>
            @endif

            @if(!empty($settings['telepon']))
            <a href="tel:{{ preg_replace('/[^0-9+]/', '', $settings['telepon']) }}"
               class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all p-6 flex items-start gap-4 reveal">
                <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-phone-alt text-xl text-primary"></i>
                </div>
                <div>
                    <div class="font-bold text-gray-800 mb-1">Telepon</div>
                    <div class="text-gray-500 text-sm">{{ $settings['telepon'] }}</div>
                </div>
            </a>
            @endif

            @if(!empty($settings['email']))
            <a href="mailto:{{ $settings['email'] }}"
               class="bg-white rounded-2xl shadow-sm hover:shadow-lg transition-all p-6 flex items-start gap-4 reveal">
                <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-envelope text-xl text-gold"></i>
                </div>
                <div>
                    <div class="font-bold text-gray-800 mb-1">Email</div>
                    <div class="text-gray-500 text-sm break-all">{{ $settings['email'] }}</div>
                </div>
            </a>
            @endif

            <div class="bg-white rounded-2xl shadow-sm p-6 flex items-start gap-4 reveal">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-inbox text-xl text-gray-600"></i>
                </div>
                <div>
                    <div class="font-bold text-gray-800 mb-1">Kotak Saran</div>
                    <div class="text-gray-500 text-sm">Tersedia di area pendaftaran dan ruang tunggu rumah sakit.</div>
                </div>
            </div>
        </div>

        {{-- Kanal Nasional --}}
        <div class="rounded-2xl overflow-hidden reveal mb-10" style="background: linear-gradient(135deg, #1E3A8A, #2563EB)">
            <div class="p-8 text-center">
                <h3 class="font-playfair text-xl md:text-2xl font-bold text-white mb-2">Kanal Pengaduan Nasional</h3>
                <p class="text-blue-100 text-sm mb-6 max-w-lg mx-auto">
                    Anda juga dapat menyampaikan pengaduan pelayanan publik melalui kanal resmi Kementerian PANRB.
                </p>
                <div class="flex flex-col sm:flex-row gap-3 justify-center">
                    <a href="{{ $settings['lapor_url'] ?? 'https://www.lapor.go.id' }}" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl font-bold text-sm transition-all"
                       style="background: #D97706; color: #fff">
                        <i class="fas fa-bullhorn"></i> SP4N-LAPOR!
                    </a>
                    <a href="{{ $settings['sippn_url'] ?? 'https://sippn.menpan.go.id' }}" target="_blank" rel="noopener"
                       class="inline-flex items-center justify-center gap-2 px-7 py-3 rounded-xl font-bold text-sm border-2 border-white text-white hover:bg-white hover:text-primary transition-all">
                        <i class="fas fa-clipboard-check"></i> SIPPN
                    </a>
                </div>
            </div>
        </div>

        {{-- Keterangan tambahan dari admin --}}
        @if(!empty($settings['pengaduan_teks']))
        <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 reveal">
            <h3 class="font-bold text-gray-800 mb-1">Alur & Ketentuan Pengaduan</h3>
            <div class="w-12 h-1 bg-gold mb-4"></div>
            <div class="text-gray-600 text-sm leading-relaxed prose prose-sm max-w-none">
                {!! $settings['pengaduan_teks'] !!}
            </div>
        </div>
        @endif

    </div>
</section>

@endsection
