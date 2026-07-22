@extends('layouts.admin')

@section('title', $file ? 'Edit File Download' : 'Upload File Download')

@section('breadcrumb')
  <span class="mx-2">/</span>
  <a href="{{ route('admin.download.index') }}" class="hover:text-green-700">Download</a>
  <span class="mx-2">/</span>
  <span class="text-gray-800">{{ $file ? 'Edit File' : 'Upload File' }}</span>
@endsection

@section('content')
<div class="max-w-2xl mx-auto">
  <div class="bg-white rounded-2xl shadow-sm p-8">
    <h1 class="text-xl font-bold text-gray-800 mb-6">
      {{ $file ? 'Edit File Download' : 'Upload File Download' }}
    </h1>

    @if($file)
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6">
      <div class="flex items-start justify-between">
        <div>
          <p class="text-sm text-blue-700 font-semibold">File saat ini:</p>
          <p class="text-sm text-blue-600 mt-0.5">{{ $file->nama_file }} ({{ $file->ukuran_readable }})</p>
        </div>
        <a href="{{ route('download.unduh', $file->id) }}" target="_blank"
           class="text-xs px-3 py-1.5 bg-blue-700 text-white rounded-lg hover:bg-blue-800 transition-colors flex items-center gap-1">
          <i class="fas fa-external-link-alt"></i> Buka File
        </a>
      </div>
      <p class="text-xs text-blue-500 mt-2">Kosongkan field upload di bawah jika tidak ingin mengganti file.</p>
    </div>
    @endif

    <form method="POST" enctype="multipart/form-data"
          action="{{ $file ? route('admin.download.file.update', $file->id) : route('admin.download.file.store') }}">
      @csrf
      @if($file) @method('PUT') @endif

      {{-- Kategori --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Kategori <span class="text-red-500">*</span>
        </label>
        <select name="kategori_id" required
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('kategori_id') border-red-400 @enderror">
          <option value="">-- Pilih Kategori --</option>
          @foreach($kategoris as $k)
            <option value="{{ $k->id }}"
                    {{ old('kategori_id', $file->kategori_id ?? $selectedKat) == $k->id ? 'selected' : '' }}>
              {{ $k->nama }}
            </option>
          @endforeach
        </select>
        @error('kategori_id')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Judul --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          Judul Tampilan <span class="text-red-500">*</span>
        </label>
        <input type="text" name="judul" value="{{ old('judul', $file->judul ?? '') }}"
               required placeholder="Formulir Pendaftaran Rawat Inap"
               class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 @error('judul') border-red-400 @enderror">
        <p class="text-xs text-gray-400 mt-1">Nama yang ditampilkan kepada pengunjung website</p>
        @error('judul')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
      </div>

      {{-- Upload File --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">
          File {{ $file ? '(opsional — kosongkan jika tidak ingin mengganti)' : '*' }}
        </label>
        <div x-data="{
          fileName: '',
          fileSize: '',
          dragOver: false,
          onFileChange(e) {
            const f = e.target.files[0];
            if (!f) return;
            this.fileName = f.name;
            const mb = f.size / 1048576;
            this.fileSize = mb >= 1 ? mb.toFixed(1) + ' MB' : Math.round(f.size/1024) + ' KB';
          },
          handleDrop(e) {
            const f = e.dataTransfer.files[0];
            if (!f) return;
            this.fileName = f.name;
            const mb = f.size / 1048576;
            this.fileSize = mb >= 1 ? mb.toFixed(1) + ' MB' : Math.round(f.size/1024) + ' KB';
            document.querySelector('input[name=file_upload]').files = e.dataTransfer.files;
          }
        }">
          <div class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
               :class="dragOver ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-400'"
               @dragover.prevent="dragOver = true"
               @dragleave="dragOver = false"
               @drop.prevent="dragOver = false; handleDrop($event)">
            <i class="fas fa-cloud-upload-alt text-5xl mb-3 block transition-colors"
               :class="fileName ? 'text-green-500' : 'text-gray-300'"></i>
            <template x-if="!fileName">
              <div>
                <p class="text-sm text-gray-500 mb-1">Drag & drop file di sini atau</p>
                <label class="cursor-pointer text-green-700 font-semibold hover:underline text-sm">
                  Pilih File
                  <input type="file" name="file_upload" class="hidden"
                         @change="onFileChange($event)"
                         accept=".{{ implode(',.', explode(', ', $allowedExt)) }}">
                </label>
                <p class="text-xs text-gray-400 mt-2">Diizinkan: {{ $allowedExt }}</p>
                <p class="text-xs text-gray-400">Ukuran maksimal: {{ $maxMb }} MB</p>
              </div>
            </template>
            <template x-if="fileName">
              <div>
                <p class="text-green-700 font-semibold text-sm" x-text="'✓ ' + fileName"></p>
                <p class="text-gray-400 text-xs mt-1" x-text="fileSize"></p>
                <button type="button" @click="fileName=''; fileSize=''"
                        class="mt-2 text-xs text-red-500 hover:underline">
                  Ganti file
                </button>
              </div>
            </template>
          </div>
          @error('file_upload')
            <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
          @enderror
        </div>
      </div>

      {{-- Deskripsi --}}
      <div class="mb-5">
        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Deskripsi</label>
        <textarea name="deskripsi" rows="2" placeholder="Keterangan singkat tentang file ini"
                  class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $file->deskripsi ?? '') }}</textarea>
      </div>

      {{-- Status --}}
      <div class="mb-6">
        <label class="flex items-center gap-3 cursor-pointer">
          <input type="checkbox" name="aktif" value="1"
                 {{ old('aktif', $file->aktif ?? true) ? 'checked' : '' }}
                 class="w-4 h-4 accent-green-700">
          <span class="text-sm font-semibold text-gray-700">Aktif (tampil di website)</span>
        </label>
      </div>

      <div class="flex gap-3">
        <button type="submit"
                class="flex-1 py-2.5 bg-green-700 text-white font-semibold rounded-xl hover:bg-green-800 transition-colors">
          {{ $file ? 'Simpan Perubahan' : 'Upload & Simpan' }}
        </button>
        <a href="{{ route('admin.download.index') }}"
           class="flex-1 py-2.5 border border-gray-300 text-gray-600 text-center font-semibold rounded-xl hover:bg-gray-50 transition-colors">
          Batal
        </a>
      </div>
    </form>
  </div>
</div>
@endsection
