@extends('layouts.admin')

@section('title', $kategori ? 'Edit Kategori' : 'Tambah Kategori Leaflet/Poster')

@section('breadcrumb')
  <span class="mx-2">/</span>
  <a href="{{ route('admin.leaflet.index') }}" class="hover:text-green-700">Leaflet & Poster</a>
  <span class="mx-2">/</span>
  <span class="text-gray-800">{{ $kategori ? 'Edit Kategori' : 'Tambah Kategori' }}</span>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
  <div class="bg-white rounded-2xl shadow-sm p-8">
    <h1 class="text-xl font-bold text-gray-800 mb-6">
      {{ $kategori ? 'Edit Kategori' : 'Tambah Kategori Leaflet/Poster' }}
    </h1>

    <form method="POST"
          action="{{ $kategori ? route('admin.leaflet.kategori.update', $kategori->id) : route('admin.leaflet.kategori.store') }}">
      @csrf
      @if($kategori) @method('PUT') @endif

      {{-- Tipe --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-2">
          Tipe <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-3">
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="tipe" value="Leaflet"
                   {{ old('tipe', $kategori->tipe ?? 'Leaflet') === 'Leaflet' ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="border-2 rounded-xl px-4 py-3 text-sm text-center font-semibold transition-all
                        peer-checked:border-green-700 peer-checked:bg-green-50 peer-checked:text-green-700
                        border-gray-200 text-gray-500 hover:border-gray-300">
              <i class="fas fa-file-medical block text-2xl mb-1"></i>
              Leaflet
            </div>
          </label>
          <label class="flex-1 cursor-pointer">
            <input type="radio" name="tipe" value="Poster"
                   {{ old('tipe', $kategori->tipe ?? '') === 'Poster' ? 'checked' : '' }}
                   class="sr-only peer">
            <div class="border-2 rounded-xl px-4 py-3 text-sm text-center font-semibold transition-all
                        peer-checked:border-blue-600 peer-checked:bg-blue-50 peer-checked:text-blue-600
                        border-gray-200 text-gray-500 hover:border-gray-300">
              <i class="fas fa-image block text-2xl mb-1"></i>
              Poster
            </div>
          </label>
        </div>
        @error('tipe')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Nama --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Nama Kategori <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nama" value="{{ old('nama', $kategori->nama ?? '') }}"
               required placeholder="PENYAKIT DALAM"
               style="text-transform: uppercase"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('nama') border-red-400 @enderror">
        <p class="text-xs text-gray-400 mt-1">Nama akan otomatis ditampilkan kapital semua</p>
        @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Urutan --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Urutan</label>
        <input type="number" name="urutan" value="{{ old('urutan', $kategori->urutan ?? 0) }}"
               class="w-32 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <p class="text-xs text-gray-400 mt-1">Semakin kecil angka, semakin atas posisinya</p>
      </div>

      {{-- Status --}}
      <div class="mb-6">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" name="aktif" value="1"
                 {{ old('aktif', $kategori->aktif ?? true) ? 'checked' : '' }}
                 class="w-4 h-4 accent-green-700">
          <span class="text-sm font-semibold text-gray-700">Aktif (tampil di website)</span>
        </label>
      </div>

      <div class="flex gap-3">
        <button type="submit"
                class="flex-1 py-2.5 bg-green-700 text-white font-semibold rounded-xl hover:bg-green-800 transition-colors">
          Simpan
        </button>
        <a href="{{ route('admin.leaflet.index') }}"
           class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-center font-semibold rounded-xl hover:bg-gray-50 transition-colors">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
