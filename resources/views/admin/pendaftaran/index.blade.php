@extends('layouts.admin')
@section('title', 'Kelola Pendaftaran Online')
@section('breadcrumb') / <span class="text-gray-700">Pendaftaran Online</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-gray-800">Pendaftaran Online</h1>
    <p class="text-sm text-gray-500 mt-1">Kelola dan pantau data pendaftaran pasien online</p>
  </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
  <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-gray-100 rounded-lg flex items-center justify-center">
      <i class="fas fa-clipboard-list text-gray-500"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-gray-800">{{ $stats['total'] }}</p>
      <p class="text-xs text-gray-500">Total</p>
    </div>
  </div>
  <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3 {{ $stats['menunggu'] > 0 ? 'ring-2 ring-yellow-300' : '' }}">
    <div class="w-10 h-10 bg-yellow-100 rounded-lg flex items-center justify-center">
      <i class="fas fa-clock text-yellow-600"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-yellow-700">{{ $stats['menunggu'] }}</p>
      <p class="text-xs text-gray-500">Menunggu</p>
    </div>
  </div>
  <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
      <i class="fas fa-check text-blue-600"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-blue-700">{{ $stats['dikonfirmasi'] }}</p>
      <p class="text-xs text-gray-500">Dikonfirmasi</p>
    </div>
  </div>
  <div class="bg-white rounded-xl shadow-sm p-4 flex items-center gap-3">
    <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center">
      <i class="fas fa-check-double text-green-600"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-green-700">{{ $stats['selesai'] }}</p>
      <p class="text-xs text-gray-500">Selesai</p>
    </div>
  </div>
</div>

{{-- Filter --}}
<form method="GET" action="{{ route('admin.pendaftaran.index') }}"
      class="bg-white rounded-xl shadow-sm p-4 mb-4 flex flex-wrap gap-3 items-end">
  <div class="flex-1 min-w-48">
    <label class="block text-xs text-gray-500 mb-1">Cari</label>
    <input type="text" name="q" value="{{ request('q') }}"
           placeholder="Nama, kode, NIK, telepon..."
           class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
  </div>
  <div class="w-40">
    <label class="block text-xs text-gray-500 mb-1">Status</label>
    <select name="status" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
      <option value="">Semua Status</option>
      @foreach(['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'] as $s)
        <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ $s }}</option>
      @endforeach
    </select>
  </div>
  <div class="w-40">
    <label class="block text-xs text-gray-500 mb-1">Layanan</label>
    <select name="layanan" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
      <option value="">Semua Layanan</option>
      @foreach(['Umum', 'BPJS', 'Asuransi Lain', 'TNI/POLRI'] as $l)
        <option value="{{ $l }}" {{ request('layanan') === $l ? 'selected' : '' }}>{{ $l }}</option>
      @endforeach
    </select>
  </div>
  <div class="flex gap-2">
    <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
      <i class="fas fa-filter mr-1"></i> Filter
    </button>
    <a href="{{ route('admin.pendaftaran.index') }}" class="border border-gray-200 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm">
      Reset
    </a>
  </div>
</form>

{{-- Tabel --}}
<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b border-gray-100">
      <tr>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold w-8">#</th>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Kode</th>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Nama Pasien</th>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Poli</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-24">Layanan</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-28">Tgl Daftar</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-28">Status</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-28">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      @forelse($pendaftarans as $i => $p)
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-4 py-3 text-gray-400 text-xs">{{ $pendaftarans->firstItem() + $i }}</td>
          <td class="px-4 py-3">
            <span class="font-mono text-xs font-bold text-green-700">{{ $p->kode }}</span>
          </td>
          <td class="px-4 py-3">
            <p class="font-medium text-gray-800">{{ $p->nama_lengkap }}</p>
            <p class="text-xs text-gray-400">{{ $p->jenis_kelamin }} · {{ $p->nomor_telepon }}</p>
          </td>
          <td class="px-4 py-3 text-gray-600 text-xs">{{ $p->poli_tujuan }}</td>
          <td class="px-4 py-3 text-center">
            @php
              $lBadge = match($p->jenis_layanan) {
                'BPJS'         => 'bg-blue-100 text-blue-700',
                'TNI/POLRI'    => 'bg-green-100 text-green-800',
                'Asuransi Lain'=> 'bg-purple-100 text-purple-700',
                default        => 'bg-gray-100 text-gray-600',
              };
            @endphp
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $lBadge }}">{{ $p->jenis_layanan }}</span>
          </td>
          <td class="px-4 py-3 text-center text-gray-500 text-xs">{{ $p->created_at->format('d/m/Y H:i') }}</td>
          <td class="px-4 py-3 text-center">
            <span class="px-2 py-0.5 rounded-full text-xs font-semibold {{ $p->status_badge['bg'] }} {{ $p->status_badge['text'] }}">
              {{ $p->status }}
            </span>
          </td>
          <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-1">
              <a href="{{ route('admin.pendaftaran.show', $p->id) }}"
                 class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-2.5 py-1.5 rounded-lg font-medium">
                <i class="fas fa-eye"></i> Detail
              </a>
              <form method="POST" action="{{ route('admin.pendaftaran.destroy', $p->id) }}"
                    onsubmit="return confirm('Hapus pendaftaran {{ $p->kode }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-2.5 py-1.5 rounded-lg font-medium">
                  <i class="fas fa-trash"></i>
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="8" class="px-4 py-16 text-center text-gray-400">
            <i class="fas fa-clipboard-list text-4xl mb-3 block"></i>
            Belum ada data pendaftaran.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($pendaftarans->hasPages())
  <div class="mt-4">{{ $pendaftarans->links() }}</div>
@endif

@endsection
