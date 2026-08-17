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

            @if(!empty($settings['pengaduan_barcode']))
            <div class="bg-white rounded-2xl shadow-sm p-6 flex items-start gap-4 reveal">
                <div class="w-16 h-16 border rounded-xl p-1 bg-gray-50 flex-shrink-0 flex items-center justify-center">
                    <img src="{{ Storage::url($settings['pengaduan_barcode']) }}" alt="QR Code Pengaduan" class="w-full h-full object-contain">
                </div>
                <div>
                    <div class="font-bold text-gray-800 mb-1">Scan Barcode Lapor</div>
                    <div class="text-gray-500 text-sm">Imbas barcode untuk masuk ke kanal pengaduan eksternal yang saat ini berjalan.</div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-2xl shadow-sm p-6 flex items-start gap-4 reveal">
                <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
                    <i class="fas fa-inbox text-xl text-gray-600"></i>
                </div>
                <div>
                    <div class="font-bold text-gray-800 mb-1">Kotak Saran Fisik</div>
                    <div class="text-gray-500 text-sm">Tersedia di area pendaftaran dan ruang tunggu RSUD Landak.</div>
                </div>
            </div>
            @endif
        </div>

        {{-- KOTAK SARAN DIGITAL (LIKE & DISLIKE VOTE) --}}
        <div class="bg-white rounded-2xl shadow-sm p-8 text-center reveal mb-10 border border-gray-100">
            <h3 class="font-playfair text-xl md:text-2xl font-bold text-gray-800 mb-2">Kotak Saran Digital</h3>
            <p class="text-gray-500 text-sm max-w-lg mx-auto mb-6">
                Bagaimana penilaian Anda terhadap kualitas pelayanan {{ $settings['nama_rs'] ?? 'RSUD Landak' }}? Sampaikan penilaian Anda secara instan di bawah ini.
            </p>
            
            <div x-data="{ voted: false, status: '' }" class="flex flex-col items-center">
                <div class="flex gap-4 justify-center w-full max-w-md">
                    <button @click="if(!voted) { sendVote('like'); voted=true; status='like' }"
                            :disabled="voted"
                            :class="voted ? (status === 'like' ? 'bg-green-600 text-white border-green-600 shadow-md scale-95' : 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed') : 'bg-green-50 text-green-700 hover:bg-green-100 border border-green-200'"
                            class="flex-1 flex flex-col items-center justify-center gap-3 py-5 px-6 rounded-2xl font-bold transition-all duration-300 shadow-sm border">
                        <i class="fas fa-thumbs-up text-3xl"></i>
                        <span>Puas (Like)</span>
                    </button>
                    <button @click="if(!voted) { sendVote('dislike'); voted=true; status='dislike' }"
                            :disabled="voted"
                            :class="voted ? (status === 'dislike' ? 'bg-red-600 text-white border-red-600 shadow-md scale-95' : 'bg-gray-100 text-gray-400 border-gray-100 cursor-not-allowed') : 'bg-red-50 text-red-700 hover:bg-red-100 border border-red-200'"
                            class="flex-1 flex flex-col items-center justify-center gap-3 py-5 px-6 rounded-2xl font-bold transition-all duration-300 shadow-sm border">
                        <i class="fas fa-thumbs-down text-3xl"></i>
                        <span>Tidak Puas (Dislike)</span>
                    </button>
                </div>
                
                <div x-show="voted" x-transition class="mt-6 text-sm text-green-600 font-bold bg-green-50 px-5 py-2.5 rounded-xl border border-green-100" style="display: none;">
                    <i class="fas fa-check-circle mr-1"></i> Terima kasih atas penilaian yang Anda berikan untuk kemajuan pelayanan kami!
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

@section('scripts')
<script>
function sendVote(type) {
    fetch('{{ route('layanan.saran.vote') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ tipe: type })
    })
    .then(response => response.json())
    .then(data => {
        console.log('Vote submitted:', data);
    })
    .catch(err => console.error('Error submitting vote:', err));
}
</script>
@endsection
