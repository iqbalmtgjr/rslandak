@extends('layouts.admin')
@section('title', isset($kamar->id) ? 'Edit Kamar' : 'Tambah Kamar')
@section('breadcrumb') / <a href="{{ route('admin.kamar.index') }}" class="hover:text-green-700">Kamar</a> / <span class="text-gray-700">{{ isset($kamar->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($kamar->id) ? 'Edit Kamar' : 'Tambah Kamar' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($kamar->id) ? route('admin.kamar.update', $kamar) : route('admin.kamar.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($kamar->id)) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Kamar <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $kamar->nama) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Badge</label>
                <input type="text" name="badge" value="{{ old('badge', $kamar->badge) }}" placeholder="VIP, BPJS, Premium..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
            <textarea name="deskripsi" rows="4" required data-no-wysiwyg class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $kamar->deskripsi) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
            @if(isset($kamar->gambar) && $kamar->gambar)
            <img id="preview-existing" src="{{ Storage::url($kamar->gambar) }}" class="w-48 h-28 object-cover rounded mb-2">
            @endif
            <img id="preview-new" class="w-48 h-28 object-cover rounded mb-2 hidden">
            <input type="file" name="gambar" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600">
        </div>

        {{-- Tarif --}}
        <div class="mb-5" x-data="{ tarif: '{{ old('tarif', $kamar->tarif ?? '') }}' }">
            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tarif per Hari (Rp)</label>
            <input type="number" name="tarif" x-model="tarif" min="0" step="1000"
                   placeholder="1020000"
                   class="w-full border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <p class="text-xs text-gray-400 mt-1">Kosongkan jika tidak ingin ditampilkan</p>
            <p class="text-xs text-green-700 mt-1 font-medium" x-show="tarif"
               x-text="tarif ? 'Rp. ' + Number(tarif).toLocaleString('id-ID') + ',00 / Hari' : ''"></p>
        </div>

        {{-- Gallery 5 Foto --}}
        <div class="mb-5">
            <label class="block text-sm font-semibold text-gray-700 mb-1">Foto Gallery (maks 5 foto)</label>
            <p class="text-xs text-gray-400 mb-3">Foto pertama = foto utama. Format: JPG, PNG. Maks 2MB per foto.</p>
            <div class="grid grid-cols-2 sm:grid-cols-3 gap-3">
                @for ($i = 1; $i <= 5; $i++)
                <div class="border-2 border-dashed border-gray-200 rounded-xl overflow-hidden hover:border-green-400 transition-colors group">
                    @if(isset($kamar) && $kamar->id && $kamar->{"foto_{$i}"})
                        <div class="relative">
                            <img src="{{ asset('storage/' . $kamar->{"foto_{$i}"}) }}"
                                 id="preview_foto_{{ $i }}" class="w-full h-32 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center gap-2">
                                <label class="cursor-pointer bg-white text-green-700 rounded-lg px-2 py-1 text-xs font-medium hover:bg-green-50">
                                    Ganti
                                    <input type="file" name="foto_{{ $i }}" class="hidden" accept=".jpg,.jpeg,.png"
                                           onchange="previewKamarFoto(this, 'preview_foto_{{ $i }}')">
                                </label>
                                <label class="flex items-center gap-1 bg-red-100 text-red-600 rounded-lg px-2 py-1 text-xs font-medium cursor-pointer">
                                    <input type="checkbox" name="hapus_foto_{{ $i }}" value="1" class="w-3 h-3"> Hapus
                                </label>
                            </div>
                        </div>
                    @else
                        <label class="flex flex-col items-center justify-center h-32 cursor-pointer hover:bg-green-50 transition-colors" id="label_foto_{{ $i }}">
                            <i class="fas fa-camera text-2xl text-gray-300 mb-1"></i>
                            <span class="text-xs text-gray-400">Foto {{ $i }}</span>
                            @if($i === 1)<span class="text-xs text-green-600 font-medium">★ Utama</span>@endif
                            <input type="file" name="foto_{{ $i }}" class="hidden" accept=".jpg,.jpeg,.png"
                                   onchange="previewKamarFotoSlot(this, {{ $i }})">
                        </label>
                    @endif
                    <div class="text-center py-1.5 border-t border-gray-100 bg-gray-50">
                        <span class="text-xs text-gray-500">{{ $i === 1 ? 'Foto Utama' : "Foto $i" }}</span>
                    </div>
                </div>
                @endfor
            </div>
        </div>

        <!-- Fasilitas Dinamis -->
        <div x-data="{ items: {{ json_encode(old('fasilitas', $kamar->fasilitas ?? [])) }} }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Fasilitas</label>
            <template x-for="(item, i) in items" :key="i">
                <div class="flex gap-2 mb-2">
                    <input :name="`fasilitas[${i}]`" x-model="items[i]" placeholder="Nama fasilitas..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <button type="button" @click="items.splice(i,1)" class="text-red-500 hover:text-red-700 px-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="items.push('')" class="text-sm text-green-700 border border-green-300 px-4 py-1.5 rounded-lg hover:bg-green-50 mt-1">
                <i class="fas fa-plus mr-1"></i> Tambah Fasilitas
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $kamar->urutan ?? 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $kamar->aktif ?? true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.kamar.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
function previewImg(input) {
    const prev = document.getElementById('preview-new');
    const existing = document.getElementById('preview-existing');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { prev.src = e.target.result; prev.classList.remove('hidden'); if(existing) existing.classList.add('hidden'); };
        reader.readAsDataURL(input.files[0]);
    }
}
function previewKamarFoto(input, imgId) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = e => document.getElementById(imgId).src = e.target.result;
    reader.readAsDataURL(input.files[0]);
}
function previewKamarFotoSlot(input, i) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const label = document.getElementById('label_foto_' + i);
        if (label) {
            label.innerHTML = '<img src="' + e.target.result + '" class="w-full h-32 object-cover">' +
                '<input type="file" name="foto_' + i + '" class="hidden" accept=".jpg,.jpeg,.png">';
        }
    };
    reader.readAsDataURL(input.files[0]);
}
</script>
@endsection
