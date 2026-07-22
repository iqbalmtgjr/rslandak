@extends('layouts.admin')

@section('title', $item ? 'Edit Item Leaflet' : 'Tambah Item Leaflet')

@section('breadcrumb')
  <span class="mx-2">/</span>
  <a href="{{ route('admin.leaflet.index') }}" class="hover:text-green-700">Leaflet & Poster</a>
  <span class="mx-2">/</span>
  <span class="text-gray-800">{{ $item ? 'Edit Item' : 'Tambah Item' }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="bg-white rounded-2xl shadow-sm p-8">
    <h1 class="text-xl font-bold text-gray-800 mb-6">
      {{ $item ? 'Edit Item Leaflet/Poster' : 'Tambah Item Leaflet/Poster' }}
    </h1>

    <form method="POST"
          action="{{ $item ? route('admin.leaflet.item.update', $item->id) : route('admin.leaflet.item.store') }}">
      @csrf
      @if($item) @method('PUT') @endif

      {{-- Kategori --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Kategori <span class="text-red-500">*</span>
        </label>
        <select name="kategori_id" required
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('kategori_id') border-red-400 @enderror">
          <option value="">-- Pilih Kategori --</option>
          @foreach($kategoris->groupBy('tipe') as $tipe => $kats)
            <optgroup label="{{ $tipe }}">
              @foreach($kats as $k)
                <option value="{{ $k->id }}"
                        {{ old('kategori_id', $item->kategori_id ?? $selectedKat) == $k->id ? 'selected' : '' }}>
                  {{ $k->nama }}
                </option>
              @endforeach
            </optgroup>
          @endforeach
        </select>
        @error('kategori_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Nama --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Nama Item <span class="text-red-500">*</span>
        </label>
        <input type="text" name="nama" value="{{ old('nama', $item->nama ?? '') }}"
               required placeholder="Mengenal Diabetes Melitus"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('nama') border-red-400 @enderror">
        @error('nama')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- URL GDrive --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          URL Google Drive <span class="text-red-500">*</span>
        </label>
        <div class="flex gap-2">
          <input type="url" name="url_gdrive" value="{{ old('url_gdrive', $item->url_gdrive ?? '') }}"
                 required placeholder="https://drive.google.com/file/d/xxxx/view?usp=sharing"
                 class="flex-1 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('url_gdrive') border-red-400 @enderror">
          @if($item && $item->url_gdrive)
            <a href="{{ $item->url_gdrive }}" target="_blank" rel="noopener"
               class="px-3 py-2.5 bg-gray-100 text-gray-600 rounded-xl hover:bg-gray-200 transition-colors text-sm"
               title="Buka di GDrive">
              <i class="fas fa-external-link-alt"></i>
            </a>
          @endif
        </div>
        @error('url_gdrive')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror

        <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 text-sm text-blue-700 mt-3">
          <strong><i class="fas fa-info-circle"></i> Cara mendapatkan link Google Drive:</strong>
          <ol class="mt-2 ml-4 space-y-1 list-decimal text-sm">
            <li>Buka Google Drive, klik kanan file → "Get link"</li>
            <li>Set akses ke <strong>"Anyone with the link"</strong></li>
            <li>Klik "Copy link" lalu tempel di kolom di atas</li>
          </ol>
        </div>
      </div>

      {{-- Deskripsi --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi (opsional)</label>
        <input type="text" name="deskripsi" value="{{ old('deskripsi', $item->deskripsi ?? '') }}"
               placeholder="Keterangan singkat tentang konten file ini"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>

      {{-- Urutan --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Urutan</label>
        <input type="number" name="urutan" value="{{ old('urutan', $item->urutan ?? 0) }}"
               class="w-32 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        <p class="text-xs text-gray-400 mt-1">Semakin kecil angka, semakin atas posisinya</p>
      </div>

      {{-- Status --}}
      <div class="mb-6">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" name="aktif" value="1"
                 {{ old('aktif', $item->aktif ?? true) ? 'checked' : '' }}
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
