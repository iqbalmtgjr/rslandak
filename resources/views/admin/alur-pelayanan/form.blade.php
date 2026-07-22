@extends('layouts.admin')
@section('title', $alur ? 'Edit Alur Pelayanan' : 'Tambah Alur Pelayanan')
@section('content')

<div class="mb-6">
  <h1 class="text-2xl font-bold text-gray-800">{{ $alur ? 'Edit Alur Pelayanan' : 'Tambah Alur Pelayanan' }}</h1>
  <p class="text-sm text-gray-500 mt-1">
    <a href="{{ route('admin.alur-pelayanan.index') }}" class="text-primary hover:underline">Alur Pelayanan</a>
    &rsaquo; {{ $alur ? $alur->judul : 'Tambah Baru' }}
  </p>
</div>

<div class="max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-8">
  <form method="POST"
        action="{{ $alur ? route('admin.alur-pelayanan.update', $alur->id) : route('admin.alur-pelayanan.store') }}"
        enctype="multipart/form-data">
    @csrf
    @if($alur) @method('PUT') @endif

    {{-- Judul --}}
    <div class="mb-5">
      <label class="block text-sm font-semibold text-gray-700 mb-1">
        Judul / Nama Alur <span class="text-red-500">*</span>
      </label>
      <input type="text" name="judul" required maxlength="200"
             value="{{ old('judul', $alur->judul ?? '') }}"
             placeholder="Alur Pendaftaran Pasien Lama"
             class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary @error('judul') border-red-400 @enderror">
      <p class="text-xs text-gray-400 mt-1">Contoh: Alur Pendaftaran Pasien Baru, Alur Pasien BPJS, Alur IGD, dll</p>
      @error('judul') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
    </div>

    {{-- Upload Gambar --}}
    <div class="mb-5"
         x-data="{
           fileName: '',
           preview: null,
           onFileChange(e) {
             const f = e.target.files[0];
             if (!f) return;
             this.fileName = f.name;
             const reader = new FileReader();
             reader.onload = (ev) => { this.preview = ev.target.result; };
             reader.readAsDataURL(f);
           },
           onDrop(e) {
             const f = e.dataTransfer.files[0];
             if (!f) return;
             this.$refs.fileInput.files = e.dataTransfer.files;
             this.onFileChange({ target: { files: [f] } });
           }
         }">

      <label class="block text-sm font-semibold text-gray-700 mb-1">
        Gambar Alur Pelayanan
        @if(!$alur)
          <span class="ml-1 px-2 py-0.5 text-xs bg-red-100 text-red-600 rounded-full">Wajib</span>
        @else
          <span class="ml-1 px-2 py-0.5 text-xs bg-gray-100 text-gray-500 rounded-full">Opsional — kosongkan jika tidak ganti</span>
        @endif
      </label>

      {{-- Preview gambar existing (edit mode) --}}
      @if($alur && $alur->gambar_url)
        <div class="mb-4 rounded-xl overflow-hidden border border-gray-200 shadow-sm">
          <img src="{{ $alur->gambar_url }}"
               alt="{{ $alur->judul }}"
               class="w-full h-auto max-h-64 object-contain bg-gray-50">
        </div>
        <p class="text-xs text-gray-500 mb-3">
          <i class="fas fa-info-circle text-blue-500"></i>
          Gambar saat ini. Upload gambar baru di bawah untuk menggantinya.
        </p>
      @endif

      {{-- Drop zone --}}
      <div class="border-2 border-dashed rounded-2xl transition-colors p-8 text-center"
           :class="preview ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-green-400 hover:bg-green-50'"
           @dragover.prevent @drop.prevent="onDrop($event)">

        <template x-if="preview">
          <div>
            <img :src="preview" class="max-h-48 mx-auto rounded-xl shadow mb-3 object-contain">
            <p class="text-sm text-green-700 font-medium" x-text="fileName"></p>
            <button type="button"
                    @click="preview=null; fileName=''; $refs.fileInput.value=''"
                    class="text-xs text-red-500 hover:underline mt-2 block mx-auto">
              Ganti file
            </button>
          </div>
        </template>

        <template x-if="!preview">
          <div>
            <i class="fas fa-cloud-upload-alt text-5xl text-gray-300 mb-3 block"></i>
            <p class="text-sm text-gray-500 mb-1">Drag &amp; drop atau</p>
            <label class="cursor-pointer font-semibold text-green-700 hover:underline text-sm">
              Pilih File Gambar
              <input type="file" name="gambar" x-ref="fileInput" class="hidden"
                     accept=".jpg,.jpeg,.png"
                     @change="onFileChange($event)">
            </label>
            <p class="text-xs text-gray-400 mt-2">JPG, PNG — Maksimal 5 MB</p>
            <p class="text-xs text-gray-400">Rekomendasikan resolusi minimal 1200px lebar (landscape)</p>
          </div>
        </template>

      </div>
      @error('gambar') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror
    </div>

    {{-- Keterangan --}}
    <div class="mb-5">
      <label class="block text-sm font-semibold text-gray-700 mb-1">
        Keterangan Tambahan <span class="text-gray-400 font-normal">(Opsional)</span>
      </label>
      <textarea name="keterangan" rows="3" data-no-wysiwyg
                placeholder="Catatan atau penjelasan singkat tentang alur ini (ditampilkan di bawah gambar)"
                class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary resize-none">{{ old('keterangan', $alur->keterangan ?? '') }}</textarea>
    </div>

    {{-- Urutan --}}
    <div class="mb-5">
      <label class="block text-sm font-semibold text-gray-700 mb-1">Urutan Tampil</label>
      <input type="number" name="urutan" min="0"
             value="{{ old('urutan', $alur->urutan ?? 0) }}"
             class="w-32 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-primary/30 focus:border-primary">
      <p class="text-xs text-gray-400 mt-1">Semakin kecil angka → tampil lebih atas. Gunakan kelipatan 10 (10, 20, 30) agar mudah menyisipkan urutan baru.</p>
    </div>

    {{-- Status --}}
    <div class="mb-7">
      <label class="flex items-center gap-3 cursor-pointer">
        <input type="hidden" name="aktif" value="0">
        <input type="checkbox" name="aktif" value="1"
               {{ old('aktif', $alur->aktif ?? true) ? 'checked' : '' }}
               class="w-4 h-4 text-primary rounded focus:ring-primary">
        <span class="text-sm font-semibold text-gray-700">Tampilkan di halaman publik</span>
      </label>
    </div>

    {{-- Tombol --}}
    <div class="flex gap-3">
      <button type="submit"
              class="bg-primary hover:bg-dark text-white px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
        <i class="fas fa-save mr-1"></i> Simpan Alur Pelayanan
      </button>
      <a href="{{ route('admin.alur-pelayanan.index') }}"
         class="bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-2.5 rounded-xl text-sm font-semibold transition-colors">
        Batal
      </a>
    </div>

  </form>
</div>

@endsection
