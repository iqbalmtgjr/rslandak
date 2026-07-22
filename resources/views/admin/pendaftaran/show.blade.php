@extends('layouts.admin')
@section('title', 'Detail Pendaftaran ' . $p->kode)
@section('breadcrumb')
  / <a href="{{ route('admin.pendaftaran.index') }}" class="hover:text-green-700">Pendaftaran Online</a>
  / <span class="text-gray-700">{{ $p->kode }}</span>
@endsection

@section('content')
<div class="flex items-start justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-gray-800 flex items-center gap-3">
      {{ $p->kode }}
      <span class="px-3 py-1 rounded-full text-sm font-semibold {{ $p->status_badge['bg'] }} {{ $p->status_badge['text'] }}">
        {{ $p->status }}
      </span>
    </h1>
    <p class="text-sm text-gray-500 mt-1">Daftar: {{ $p->created_at->format('d/m/Y H:i') }}</p>
  </div>
  <a href="{{ route('admin.pendaftaran.index') }}"
     class="border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm">
    <i class="fas fa-arrow-left mr-1"></i> Kembali
  </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

  {{-- KIRI: Data Pasien --}}
  <div class="lg:col-span-2 space-y-5">

    {{-- Data Pasien --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
      <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b text-sm uppercase tracking-wide">Data Pasien</h2>
      <div class="space-y-3">
        @php
        $fields = [
          ['Nomor Pendaftaran', '<span class="font-mono font-bold text-green-700">' . $p->kode . '</span>'],
          ['Nama Lengkap',      e($p->nama_lengkap)],
          ['NIK',               e($p->nik ?: '-')],
          ['TTL',               e(($p->tempat_lahir ? $p->tempat_lahir . ', ' : '') . $p->tanggal_lahir_readable)],
          ['Jenis Kelamin',     e($p->jenis_kelamin)],
          ['Nomor Telepon',     '<a href="tel:' . e($p->nomor_telepon) . '" class="text-green-600 hover:underline">' . e($p->nomor_telepon) . '</a>'],
          ['Alamat',            e($p->alamat)],
        ];
        @endphp
        @foreach($fields as [$label, $value])
          <div class="flex gap-4 text-sm">
            <span class="text-gray-500 w-36 flex-shrink-0">{{ $label }}</span>
            <span class="text-gray-800">{!! $value !!}</span>
          </div>
        @endforeach
      </div>
    </div>

    {{-- Data Berobat --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
      <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b text-sm uppercase tracking-wide">Data Berobat</h2>
      <div class="space-y-3 text-sm">
        <div class="flex gap-4">
          <span class="text-gray-500 w-36 flex-shrink-0">Status Pasien</span>
          <span class="text-gray-800">{{ $p->status_pasien }}</span>
        </div>
        <div class="flex gap-4">
          <span class="text-gray-500 w-36 flex-shrink-0">Jenis Layanan</span>
          <span class="text-gray-800">{{ $p->jenis_layanan }}</span>
        </div>
        @if($p->nama_asuransi)
        <div class="flex gap-4">
          <span class="text-gray-500 w-36 flex-shrink-0">Nama Asuransi</span>
          <span class="text-gray-800">{{ $p->nama_asuransi }}</span>
        </div>
        @endif
        <div class="flex gap-4">
          <span class="text-gray-500 w-36 flex-shrink-0">Poli Tujuan</span>
          <span class="text-gray-800 font-medium">{{ $p->poli_tujuan }}</span>
        </div>
        @if($p->catatan)
        <div class="flex gap-4">
          <span class="text-gray-500 w-36 flex-shrink-0">Catatan</span>
          <span class="text-gray-700 bg-gray-50 rounded-lg p-3 flex-1 text-xs leading-relaxed">{{ $p->catatan }}</span>
        </div>
        @endif
      </div>
    </div>

    {{-- Foto Dokumen --}}
    <div class="bg-white rounded-xl shadow-sm p-6">
      <h2 class="font-bold text-gray-700 mb-4 pb-2 border-b text-sm uppercase tracking-wide">Foto Dokumen</h2>
      <div class="grid grid-cols-2 gap-4">
        <div>
          <p class="text-xs font-medium text-gray-500 mb-2">Foto KTP</p>
          @if($p->foto_ktp_url)
            <a href="{{ $p->foto_ktp_url }}" target="_blank">
              <img src="{{ $p->foto_ktp_url }}" alt="KTP {{ $p->nama_lengkap }}"
                   class="w-full h-32 object-cover rounded-lg border shadow-sm hover:opacity-80 transition-opacity">
            </a>
            <a href="{{ $p->foto_ktp_url }}" target="_blank"
               class="text-xs text-blue-600 hover:underline mt-1 block">
              <i class="fas fa-external-link-alt mr-1"></i> Lihat KTP
            </a>
          @else
            <div class="w-full h-32 bg-gray-100 rounded-lg flex items-center justify-center text-gray-400">
              <i class="fas fa-image text-2xl"></i>
            </div>
          @endif
        </div>
        @if($p->foto_bpjs_url)
        <div>
          <p class="text-xs font-medium text-gray-500 mb-2">Foto Kartu BPJS</p>
          <a href="{{ $p->foto_bpjs_url }}" target="_blank">
            <img src="{{ $p->foto_bpjs_url }}" alt="BPJS {{ $p->nama_lengkap }}"
                 class="w-full h-32 object-cover rounded-lg border shadow-sm hover:opacity-80 transition-opacity">
          </a>
          <a href="{{ $p->foto_bpjs_url }}" target="_blank"
             class="text-xs text-blue-600 hover:underline mt-1 block">
            <i class="fas fa-external-link-alt mr-1"></i> Lihat BPJS
          </a>
        </div>
        @endif
      </div>
    </div>

  </div>

  {{-- KANAN: Aksi --}}
  <div class="space-y-4">

    {{-- Update Status --}}
    <div class="bg-white rounded-xl shadow-sm p-5 sticky top-20">
      <h2 class="font-bold text-gray-700 mb-4 text-sm">Update Status Pendaftaran</h2>
      <form method="POST" action="{{ route('admin.pendaftaran.status', $p->id) }}" class="space-y-3">
        @csrf
        <div>
          <label class="block text-xs text-gray-500 mb-1">Status</label>
          <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            @foreach(['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'] as $s)
              <option value="{{ $s }}" {{ $p->status === $s ? 'selected' : '' }}>{{ $s }}</option>
            @endforeach
          </select>
        </div>
        <div>
          <label class="block text-xs text-gray-500 mb-1">Catatan Admin</label>
          <textarea name="catatan_admin" rows="3"
                    placeholder="Catatan untuk pasien (opsional)" data-no-wysiwyg
                    class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm resize-none focus:outline-none focus:ring-2 focus:ring-green-500">{{ $p->catatan_admin }}</textarea>
        </div>
        <button type="submit"
                class="w-full bg-green-700 hover:bg-green-800 text-white py-2.5 rounded-lg text-sm font-semibold transition-colors">
          <i class="fas fa-save mr-1"></i> Simpan Status
        </button>
      </form>
    </div>

    {{-- WhatsApp --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
      <h2 class="font-bold text-gray-700 mb-3 text-sm">Hubungi Pasien</h2>
      <a href="https://wa.me/{{ preg_replace('/^0/', '62', preg_replace('/\D/', '', $p->nomor_telepon)) }}"
         target="_blank"
         class="block w-full text-center py-2.5 rounded-lg text-sm font-semibold text-white mb-2 transition-colors"
         style="background:#25D366">
        <i class="fab fa-whatsapp mr-1"></i> WA Pasien ({{ $p->nomor_telepon }})
      </a>
      <a href="{{ $p->pesan_wa }}" target="_blank"
         class="block w-full text-center py-2.5 rounded-lg text-sm font-semibold bg-gray-100 text-gray-700 hover:bg-gray-200 transition-colors">
        <i class="fas fa-paper-plane mr-1"></i> Kirim Pesan Konfirmasi
      </a>
    </div>

    {{-- Info Waktu --}}
    <div class="bg-white rounded-xl shadow-sm p-5">
      <h2 class="font-bold text-gray-700 mb-3 text-sm">Info Waktu</h2>
      <div class="space-y-2 text-xs text-gray-500">
        <div class="flex justify-between">
          <span>Daftar</span>
          <span class="text-gray-700">{{ $p->created_at->format('d/m/Y H:i') }}</span>
        </div>
        <div class="flex justify-between">
          <span>Update terakhir</span>
          <span class="text-gray-700">{{ $p->updated_at->diffForHumans() }}</span>
        </div>
      </div>
    </div>

  </div>
</div>

@endsection
