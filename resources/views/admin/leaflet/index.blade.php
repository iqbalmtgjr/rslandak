@extends('layouts.admin')

@section('title', 'Kelola Leaflet & Poster')

@section('breadcrumb')
  <span class="mx-2">/</span> <span class="text-gray-800">Leaflet & Poster</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-gray-800">Leaflet & Poster</h1>
  <div class="flex gap-2">
    <a href="{{ route('admin.leaflet.item.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 text-white text-sm font-semibold rounded-xl hover:bg-green-800 transition-colors">
      <i class="fas fa-plus"></i> Tambah Item
    </a>
    <a href="{{ route('admin.leaflet.kategori.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-green-700 text-green-700 text-sm font-semibold rounded-xl hover:bg-green-50 transition-colors">
      <i class="fas fa-folder-plus"></i> Tambah Kategori
    </a>
  </div>
</div>

{{-- Tab Filter --}}
<div class="flex border-b border-gray-200 mb-6">
  <a href="{{ route('admin.leaflet.index', ['tipe' => 'Leaflet']) }}"
     class="px-6 py-3 text-sm font-medium transition-colors
            {{ $tipe === 'Leaflet' ? 'border-b-2 border-green-700 text-green-700' : 'text-gray-500 hover:text-gray-700' }}">
    <i class="fas fa-file-medical mr-1"></i> Leaflet
    <span class="ml-1 text-xs {{ $tipe === 'Leaflet' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full">
      {{ $countLeaflet }}
    </span>
  </a>
  <a href="{{ route('admin.leaflet.index', ['tipe' => 'Poster']) }}"
     class="px-6 py-3 text-sm font-medium transition-colors
            {{ $tipe === 'Poster' ? 'border-b-2 border-green-700 text-green-700' : 'text-gray-500 hover:text-gray-700' }}">
    <i class="fas fa-image mr-1"></i> Poster
    <span class="ml-1 text-xs {{ $tipe === 'Poster' ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full">
      {{ $countPoster }}
    </span>
  </a>
</div>

{{-- Daftar Kategori + Items --}}
@forelse($kategoris as $kat)
<div class="bg-white rounded-xl shadow-sm mb-4 overflow-hidden">
  {{-- Header Kategori --}}
  <div class="flex items-center justify-between px-5 py-4 border-b border-gray-100 bg-gray-50">
    <div class="flex items-center gap-3">
      <span class="px-2.5 py-1 text-xs font-bold rounded-lg
                   {{ $tipe === 'Leaflet' ? 'bg-green-100 text-green-700' : 'bg-blue-100 text-blue-700' }}">
        {{ $kat->tipe }}
      </span>
      <h3 class="font-bold text-gray-800">{{ $kat->nama }}</h3>
      <span class="text-xs text-gray-400">Urutan: {{ $kat->urutan }}</span>
    </div>
    <div class="flex items-center gap-2">
      <a href="{{ route('admin.leaflet.item.create', ['kategori_id' => $kat->id]) }}"
         class="px-3 py-1.5 text-xs bg-green-100 text-green-700 rounded-lg hover:bg-green-200 transition-colors">
        <i class="fas fa-plus mr-1"></i> Item
      </a>
      <a href="{{ route('admin.leaflet.kategori.edit', $kat->id) }}"
         class="px-3 py-1.5 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
        Edit
      </a>
      <form method="POST" action="{{ route('admin.leaflet.kategori.toggle', $kat->id) }}" class="inline">
        @csrf
        <button type="submit"
                class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors
                       {{ $kat->aktif ? 'bg-green-500' : 'bg-gray-300' }}"
                title="{{ $kat->aktif ? 'Non-aktifkan' : 'Aktifkan' }}">
          <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                       {{ $kat->aktif ? 'translate-x-4' : 'translate-x-1' }}"></span>
        </button>
      </form>
      <form method="POST" action="{{ route('admin.leaflet.kategori.destroy', $kat->id) }}"
            onsubmit="return confirm('Hapus kategori {{ addslashes($kat->nama) }} beserta semua isinya?')">
        @csrf @method('DELETE')
        <button type="submit"
                class="px-3 py-1.5 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
          Hapus
        </button>
      </form>
    </div>
  </div>

  {{-- Tabel Items --}}
  @if($kat->allItems->isEmpty())
    <div class="py-8 text-center text-gray-400 text-sm">
      <i class="fas fa-inbox text-3xl mb-2 block text-gray-200"></i>
      Belum ada item.
      <a href="{{ route('admin.leaflet.item.create', ['kategori_id' => $kat->id]) }}"
         class="text-green-700 hover:underline ml-1">+ Tambah Item Pertama</a>
    </div>
  @else
    <table class="w-full text-sm">
      <thead class="border-b border-gray-100">
        <tr class="text-xs text-gray-500 uppercase">
          <th class="px-5 py-2 text-left w-8">#</th>
          <th class="px-5 py-2 text-left">Nama</th>
          <th class="px-5 py-2 text-left">Deskripsi</th>
          <th class="px-5 py-2 text-left">URL GDrive</th>
          <th class="px-5 py-2 text-left w-16">Urutan</th>
          <th class="px-5 py-2 text-left w-16">Status</th>
          <th class="px-5 py-2 text-left">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-50">
        @foreach($kat->allItems as $i => $item)
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-5 py-3 text-gray-400 text-xs">{{ $i + 1 }}</td>
          <td class="px-5 py-3">
            <span class="font-medium text-gray-800">{{ $item->nama }}</span>
            @if(!$item->aktif)
              <span class="ml-1 text-xs bg-gray-100 text-gray-400 px-1.5 py-0.5 rounded">nonaktif</span>
            @endif
          </td>
          <td class="px-5 py-3 text-gray-500 text-xs max-w-[150px] truncate">
            {{ $item->deskripsi ?: '-' }}
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-1.5">
              <span class="text-xs text-gray-400 truncate max-w-[120px]">
                {{ Str::limit($item->url_gdrive, 40) }}
              </span>
              <a href="{{ $item->url_gdrive }}" target="_blank" rel="noopener"
                 class="text-green-700 hover:text-green-900 flex-shrink-0" title="Buka di GDrive">
                <i class="fas fa-external-link-alt text-xs"></i>
              </a>
            </div>
          </td>
          <td class="px-5 py-3 text-gray-500 text-center">{{ $item->urutan }}</td>
          <td class="px-5 py-3">
            <form method="POST" action="{{ route('admin.leaflet.item.toggle', $item->id) }}" class="inline">
              @csrf
              <button type="submit"
                      class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors
                             {{ $item->aktif ? 'bg-green-500' : 'bg-gray-300' }}">
                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                             {{ $item->aktif ? 'translate-x-4' : 'translate-x-1' }}"></span>
              </button>
            </form>
          </td>
          <td class="px-5 py-3">
            <div class="flex items-center gap-1">
              <a href="{{ route('admin.leaflet.item.edit', $item->id) }}"
                 class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                Edit
              </a>
              <form method="POST" action="{{ route('admin.leaflet.item.destroy', $item->id) }}"
                    onsubmit="return confirm('Hapus item {{ addslashes($item->nama) }}?')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                  Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>
        @endforeach
      </tbody>
    </table>
  @endif
</div>
@empty
<div class="bg-white rounded-xl shadow-sm py-16 text-center text-gray-400">
  <i class="fas fa-folder-open text-5xl mb-3 block"></i>
  <p>Belum ada kategori {{ $tipe }}.</p>
  <a href="{{ route('admin.leaflet.kategori.create') }}" class="text-green-700 hover:underline mt-2 inline-block">
    + Tambah Kategori Pertama
  </a>
</div>
@endforelse

@endsection
