@extends('layouts.admin')
<<<<<<< HEAD
@section('title', isset($poli) && $poli ? 'Edit Klinik' : 'Tambah Klinik')
@section('breadcrumb')
    / <a href="{{ route('admin.poliklinik.index') }}" class="hover:text-green-700">Klinik</a>
=======
@section('title', isset($poli) && $poli ? 'Edit Poliklinik' : 'Tambah Poliklinik')
@section('breadcrumb')
    / <a href="{{ route('admin.poliklinik.index') }}" class="hover:text-green-700">Poliklinik</a>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
    / <span class="text-gray-700">{{ isset($poli) && $poli ? 'Edit' : 'Tambah' }}</span>
@endsection

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
<<<<<<< HEAD
        {{ isset($poli) && $poli ? 'Edit Klinik: ' . $poli->nama : 'Tambah Klinik' }}
=======
        {{ isset($poli) && $poli ? 'Edit Poliklinik: ' . $poli->nama : 'Tambah Poliklinik' }}
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
    </h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST"
          action="{{ isset($poli) && $poli ? route('admin.poliklinik.update', $poli->id) : route('admin.poliklinik.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($poli) && $poli) @method('PUT') @endif

        {{-- Nama --}}
        <div>
<<<<<<< HEAD
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Klinik <span class="text-red-500">*</span></label>
=======
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Poliklinik <span class="text-red-500">*</span></label>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            <input type="text" name="nama" value="{{ old('nama', $poli->nama ?? '') }}" required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        {{-- Ikon (Alpine.js tabs) --}}
        <div x-data="{ tab: '{{ (isset($poli) && $poli && $poli->tipe_ikon === 'img') ? 'img' : 'fa' }}' }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Ikon</label>

            {{-- Tab switch --}}
            <div class="flex gap-2 mb-3">
                <button type="button" @click="tab = 'fa'"
                        :class="tab === 'fa' ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-icons mr-1"></i> Font Awesome
                </button>
                <button type="button" @click="tab = 'img'"
                        :class="tab === 'img' ? 'bg-green-700 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200'"
                        class="px-4 py-1.5 rounded-lg text-sm font-medium transition-colors">
                    <i class="fas fa-image mr-1"></i> Upload Gambar
                </button>
            </div>

            {{-- Font Awesome input --}}
            <div x-show="tab === 'fa'" x-cloak>
                <div class="flex items-center gap-3">
                    <input type="text" name="ikon_fa" id="ikon-fa-input"
                           value="{{ old('ikon_fa', (isset($poli) && $poli && $poli->tipe_ikon === 'fa') ? $poli->ikon : '') }}"
                           placeholder="fa-stethoscope"
                           class="border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 flex-1">
                    <div class="w-12 h-12 rounded-lg flex items-center justify-center border border-gray-200 bg-green-50" id="fa-preview-box">
                        <i id="fa-preview-icon"
                           class="fas {{ (isset($poli) && $poli && $poli->tipe_ikon === 'fa' && $poli->ikon) ? $poli->ikon : 'fa-question' }} text-green-700 text-xl"></i>
                    </div>
                </div>
                <p class="text-xs text-gray-400 mt-1">Masukkan class ikon FA, contoh: <code>fa-stethoscope</code>, <code>fa-heartbeat</code></p>
            </div>

            {{-- Upload gambar --}}
            <div x-show="tab === 'img'" x-cloak>
                @if(isset($poli) && $poli && $poli->tipe_ikon === 'img' && $poli->ikon)
                <img id="ikon-existing" src="{{ asset('storage/' . $poli->ikon) }}"
                     class="w-16 h-16 object-cover rounded-lg mb-2">
                @endif
                <img id="ikon-preview" class="w-16 h-16 object-cover rounded-lg mb-2 hidden">
                <input type="file" name="ikon_file" accept="image/*"
                       onchange="previewIkon(this)"
                       class="text-sm text-gray-600">
                <p class="text-xs text-gray-400 mt-1">Maks. 1MB. Format: JPG, PNG, SVG.</p>
            </div>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi</label>
            <textarea name="deskripsi" rows="4"
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $poli->deskripsi ?? '') }}</textarea>
        </div>

        {{-- Prosedur --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Prosedur Layanan</label>
            <textarea name="prosedur" rows="4"
<<<<<<< HEAD
                      placeholder="Jelaskan prosedur / alur layanan klinik ini..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('prosedur', $poli->prosedur ?? '') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Opsional. Akan ditampilkan di halaman detail klinik.</p>
=======
                      placeholder="Jelaskan prosedur / alur layanan poliklinik ini..."
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('prosedur', $poli->prosedur ?? '') }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Opsional. Akan ditampilkan di halaman detail poliklinik.</p>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        </div>

        {{-- Urutan & Status --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $poli->urutan ?? 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1"
                       {{ old('aktif', $poli->aktif ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif / Tampilkan</label>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('admin.poliklinik.index') }}"
               class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
<script>
// Live preview Font Awesome icon
document.getElementById('ikon-fa-input')?.addEventListener('input', function() {
    const icon = document.getElementById('fa-preview-icon');
    if (icon) {
        icon.className = 'fas ' + this.value + ' text-green-700 text-xl';
    }
});

function previewIkon(input) {
    const prev = document.getElementById('ikon-preview');
    const existing = document.getElementById('ikon-existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => {
            prev.src = e.target.result;
            prev.classList.remove('hidden');
            if (existing) existing.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
