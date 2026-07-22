@extends('layouts.admin')

@section('title', $kategori ? 'Edit Kategori' : 'Tambah Kategori Download')

@section('breadcrumb')
  <span class="mx-2">/</span>
  <a href="{{ route('admin.download.index', ['tab' => 'kategori']) }}" class="hover:text-green-700">Download</a>
  <span class="mx-2">/</span>
  <span class="text-gray-800">{{ $kategori ? 'Edit Kategori' : 'Tambah Kategori' }}</span>
@endsection

@section('content')
<div class="max-w-lg mx-auto">
  <div class="bg-white rounded-2xl shadow-sm p-8">
    <h1 class="text-xl font-bold text-gray-800 mb-6">
      {{ $kategori ? 'Edit Kategori' : 'Tambah Kategori Download' }}
    </h1>

    <form method="POST"
          action="{{ $kategori ? route('admin.download.kategori.update', $kategori->id) : route('admin.download.kategori.store') }}">
      @csrf
      @if($kategori) @method('PUT') @endif

      {{-- Nama --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Nama Kategori <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nama" value="{{ old('nama', $kategori->nama ?? '') }}"
               required placeholder="Formulir Pendaftaran"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('nama') border-red-400 @enderror">
        @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Ikon + Live Preview --}}
      <div class="mb-5" x-data="{ ikon: '{{ old('ikon', $kategori->ikon ?? 'fa-folder') }}' }">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ikon Font Awesome</label>
        <div class="flex items-center gap-3">
          <input type="text" name="ikon" x-model="ikon"
                 placeholder="fa-folder"
                 class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
          <div class="w-12 h-12 rounded-xl bg-gray-100 flex items-center justify-center flex-shrink-0">
            <i class="fas text-2xl text-green-700" :class="ikon"></i>
          </div>
        </div>
        <p class="text-xs text-gray-400 mt-1">
          Cari ikon di <a href="https://fontawesome.com/icons" target="_blank" class="text-green-700 hover:underline">fontawesome.com/icons</a>
        </p>
      </div>

      {{-- Warna --}}
      <div class="mb-5" x-data="{ warna: '{{ old('warna', $kategori->warna ?? '#2563EB') }}' }">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Warna Ikon</label>
        <div class="flex items-center gap-3">
          <input type="color" x-model="warna" name="warna_picker"
                 class="w-12 h-10 rounded-lg border border-gray-200 cursor-pointer p-1">
          <input type="text" name="warna" x-model="warna" maxlength="7" placeholder="#2563EB"
                 class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
          <div class="w-10 h-10 rounded-xl flex-shrink-0 border border-gray-200"
               :style="'background: ' + warna + '20'">
            <div class="w-full h-full rounded-xl opacity-100" :style="'background: ' + warna"></div>
          </div>
        </div>
      </div>

      {{-- Deskripsi --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
        <textarea name="deskripsi" rows="2" placeholder="Keterangan singkat tentang kategori ini"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $kategori->deskripsi ?? '') }}</textarea>
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
        <a href="{{ route('admin.download.index', ['tab' => 'kategori']) }}"
           class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-center font-semibold rounded-xl hover:bg-gray-50 transition-colors">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
