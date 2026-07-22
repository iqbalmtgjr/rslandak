@extends('layouts.app')
@section('title', 'Pendaftaran Berhasil — RSUD Landak')
@section('content')

<section class="py-16 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-2xl">

    <div class="bg-white rounded-3xl shadow-lg overflow-hidden">

      {{-- Header hijau --}}
      <div class="bg-gradient-to-r from-green-700 to-green-500 px-8 py-10 text-center">
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
          <i class="fas fa-check-circle text-green-600 text-4xl"></i>
        </div>
        <h1 class="font-playfair text-2xl font-bold text-white mb-2">Pendaftaran Berhasil!</h1>
        <p class="text-green-100 text-sm">Data Anda telah kami terima. Simpan nomor pendaftaran berikut.</p>
      </div>

      <div class="px-8 py-8">

        {{-- Nomor Pendaftaran --}}
        <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-5 text-center mb-6">
          <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">Nomor Pendaftaran Anda</p>
          <p class="font-mono text-3xl font-bold text-green-800 tracking-widest">{{ $pendaftaran->kode }}</p>
          <p class="text-xs text-gray-400 mt-2">{{ now()->translatedFormat('l, d F Y') }}</p>
        </div>

        {{-- Ringkasan Data --}}
        <div class="mb-6">
          <h3 class="font-semibold text-gray-700 text-sm mb-3 pb-2 border-b border-gray-100">Ringkasan Pendaftaran</h3>
          <div class="space-y-2">
            @php
            $rows = [
              ['Nama Lengkap',  $pendaftaran->nama_lengkap],
              ['NIK',           $pendaftaran->nik ?: '-'],
              ['Jenis Kelamin', $pendaftaran->jenis_kelamin],
              ['Nomor Telepon', $pendaftaran->nomor_telepon],
              ['Status Pasien', $pendaftaran->status_pasien],
              ['Jenis Layanan', $pendaftaran->jenis_layanan],
              ['Poli Tujuan',   $pendaftaran->poli_tujuan],
            ];
            @endphp
            @foreach($rows as [$label, $value])
              <div class="flex justify-between items-start py-2 border-b border-gray-50 text-sm">
                <span class="text-gray-500 w-36 flex-shrink-0">{{ $label }}</span>
                <span class="text-gray-800 font-medium text-right">{{ $value }}</span>
              </div>
            @endforeach
            @if($pendaftaran->catatan)
              <div class="py-2 text-sm">
                <p class="text-gray-500 mb-1">Catatan</p>
                <p class="text-gray-700 bg-gray-50 rounded-lg p-3 text-xs">{{ $pendaftaran->catatan }}</p>
              </div>
            @endif
          </div>
        </div>

        {{-- Langkah selanjutnya --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 mb-6">
          <p class="text-sm font-semibold text-yellow-800 mb-2 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            Langkah Selanjutnya — WAJIB
          </p>
          <ol class="text-sm text-yellow-700 space-y-1.5 list-decimal list-inside">
            <li>Klik tombol <strong>"Konfirmasi via WhatsApp"</strong> di bawah</li>
            <li>Kirim pesan yang sudah otomatis terisi ke bagian pendaftaran RS</li>
            <li>Tunggu konfirmasi dari petugas sebelum datang ke RS</li>
            <li>Bawa <strong>KTP asli</strong> dan kartu BPJS (jika ada) saat datang</li>
          </ol>
        </div>

        {{-- Tombol WA --}}
        <a href="{{ $pendaftaran->pesan_wa }}"
           target="_blank" rel="noopener noreferrer"
           class="block w-full py-4 px-6 rounded-2xl text-center font-bold text-white text-base
                  transition-all duration-200 hover:scale-[1.02] active:scale-[0.98] shadow-lg hover:shadow-xl"
           style="background: linear-gradient(135deg, #25D366, #128C7E);">
          <i class="fab fa-whatsapp text-2xl mr-2 align-middle"></i>
          Konfirmasi via WhatsApp
          <span class="block text-xs font-normal mt-0.5 opacity-80">Klik untuk membuka WhatsApp bagian pendaftaran</span>
        </a>

        <p class="text-center text-xs text-gray-400 mt-3">
          Atau hubungi langsung:
          <a href="tel:+6283830331205" class="text-green-600 font-medium hover:underline">0838-3033-1205</a>
        </p>

        {{-- Tombol kembali --}}
        <div class="mt-6 pt-4 border-t border-gray-100 flex gap-3">
          <a href="{{ route('pendaftaran.form') }}"
             class="flex-1 text-center py-3 border border-gray-300 text-gray-600 rounded-xl text-sm hover:bg-gray-50 transition-colors">
            <i class="fas fa-plus mr-1"></i> Daftar Lagi
          </a>
          <a href="{{ route('home') }}"
             class="flex-1 text-center py-3 bg-green-700 text-white rounded-xl text-sm hover:bg-green-800 transition-colors font-medium">
            <i class="fas fa-home mr-1"></i> Kembali ke Beranda
          </a>
        </div>

      </div>
    </div>

  </div>
</section>

@endsection
