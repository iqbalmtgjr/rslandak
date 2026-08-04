@extends('layouts.app')

@section('title', 'Informasi & Pengaduan — ' . ($settings['nama_rs'] ?? 'RSUD Landak'))

@section('content')

@include('partials.page-header', ['judul' => 'Informasi & Pengaduan', 'parent' => 'Layanan'])

<section class="py-14 bg-gray-50 min-h-screen">
    <div class="container mx-auto px-4 max-w-4xl">

        <div class="text-center mb-10 reveal">
            <h2 class="font-playfair text-2xl font-bold text-gray-800 mb-2">Sampaikan Informasi & Pengaduan Anda</h2>
            <p class="text-gray-500 max-w-2xl mx-auto text-sm">
                {{ $settings['nama_rs'] ?? 'RSUD Landak' }} membuka kanal pengaduan bagi masyarakat.
                Setiap masukan, saran, maupun keluhan akan kami tindak lanjuti sesuai ketentuan yang berlaku.
            </p>
        </div>

        {{-- Grid Kanal & Barcode --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-10 items-stretch">
            <div class="md:col-span-2 space-y-4">
                @if(!empty($settings['pengaduan_wa']))
                <a href="https://wa.me/{{ $settings['pengaduan_wa'] }}" target="_blank" rel="noopener"
                   class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all p-6 flex items-start gap-4 reveal border border-gray-100 block">
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
                   class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all p-6 flex items-start gap-4 reveal border border-gray-100 block">
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
                   class="bg-white rounded-2xl shadow-sm hover:shadow-md transition-all p-6 flex items-start gap-4 reveal border border-gray-100 block">
                    <div class="w-12 h-12 rounded-xl bg-amber-50 flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-xl text-gold"></i>
                    </div>
                    <div>
                        <div class="font-bold text-gray-800 mb-1">Email</div>
                        <div class="text-gray-500 text-sm break-all">{{ $settings['email'] }}</div>
                    </div>
                </a>
                @endif
            </div>

            {{-- Barcode area --}}
            <div class="col-span-1">
                @if(!empty($settings['pengaduan_barcode']))
                <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center text-center reveal border border-gray-100 h-full justify-center">
                    <div class="font-bold text-gray-800 mb-3 text-sm">QR Code Pengaduan Resmi</div>
                    <img src="{{ Storage::url($settings['pengaduan_barcode']) }}" alt="QR Code Pengaduan" class="w-32 h-32 object-contain rounded-lg border p-1.5 bg-gray-50 mb-3">
                    <p class="text-[11px] text-gray-400 leading-snug">Scan barcode untuk mengakses formulir pengaduan rumah sakit.</p>
                </div>
                @else
                <div class="bg-white rounded-2xl shadow-sm p-6 flex flex-col items-center text-center reveal border border-gray-100 h-full justify-center text-gray-400">
                    <i class="fas fa-qrcode text-4xl mb-2"></i>
                    <p class="text-xs">QR Code tidak tersedia.</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Kotak Saran / Feedback Online --}}
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6 md:p-8 reveal mb-10" x-data="saranForm()">
            <div class="border-l-4 border-gold pl-4 mb-6">
                <h3 class="font-playfair text-xl font-bold text-gray-800">Kotak Saran & Feedback Online</h3>
                <p class="text-gray-500 text-xs">Pilih respon kepuasan Anda dan berikan kritik/saran.</p>
            </div>

            <form @submit.prevent="submitForm" class="space-y-5">
                <div class="flex gap-4 justify-center">
                    <button type="button" @click="tipe = 'like'"
                            :class="tipe === 'like' ? 'border-green-500 bg-green-50 text-green-700 ring-2 ring-green-500' : 'border-gray-200 text-gray-400 hover:text-green-600'"
                            class="flex-1 max-w-[12rem] py-4 border rounded-2xl flex flex-col items-center justify-center gap-2 font-bold transition-all shadow-sm">
                        <i class="fas fa-thumbs-up text-2xl"></i>
                        <span>Puas (Like)</span>
                    </button>
                    <button type="button" @click="tipe = 'dislike'"
                            :class="tipe === 'dislike' ? 'border-red-500 bg-red-50 text-red-700 ring-2 ring-red-500' : 'border-gray-200 text-gray-400 hover:text-red-600'"
                            class="flex-1 max-w-[12rem] py-4 border rounded-2xl flex flex-col items-center justify-center gap-2 font-bold transition-all shadow-sm">
                        <i class="fas fa-thumbs-down text-2xl"></i>
                        <span>Kurang Puas (Dislike)</span>
                    </button>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-gray-600 mb-1">Masukan / Saran Anda</label>
                    <textarea x-model="pesan" rows="4" placeholder="Tulis masukan Anda demi peningkatan layanan kami..." required
                              class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500"></textarea>
                </div>

                <div class="flex justify-end">
                    <button type="submit" :disabled="submitting || !tipe"
                            class="bg-green-700 hover:bg-green-800 disabled:bg-gray-300 disabled:cursor-not-allowed text-white px-6 py-2.5 rounded-lg text-sm font-semibold flex items-center gap-2">
                        <i class="fas fa-paper-plane"></i>
                        <span x-text="submitting ? 'Mengirim...' : 'Kirim Feedback'"></span>
                    </button>
                </div>
            </form>

            <div x-show="message" x-cloak
                 :class="status === 'success' ? 'bg-green-50 text-green-800 border-green-200' : 'bg-red-50 text-red-800 border-red-200'"
                 class="mt-4 p-3 rounded-lg border text-sm text-center font-medium">
                <span x-text="message"></span>
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

        {{-- Alur Pengaduan --}}
        @if(!empty($settings['pengaduan_teks']))
        <div class="bg-white rounded-2xl shadow-sm p-6 md:p-8 reveal border border-gray-100">
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
function saranForm() {
    return {
        tipe: '',
        pesan: '',
        submitting: false,
        message: '',
        status: '',
        submitForm() {
            this.submitting = true;
            this.message = '';
            
            fetch("{{ route('layanan.saran.store') }}", {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    tipe: this.tipe,
                    pesan: this.pesan
                })
            })
            .then(res => res.json())
            .then(data => {
                this.submitting = false;
                this.status = data.status;
                this.message = data.message;
                if (data.status === 'success') {
                    this.tipe = '';
                    this.pesan = '';
                }
            })
            .catch(err => {
                this.submitting = false;
                this.status = 'error';
                this.message = 'Terjadi kesalahan sistem, silakan coba lagi.';
            });
        }
    }
}
</script>
@endsection
