@extends('layouts.admin')
@section('title', isset($item) && $item ? 'Edit Pelayanan 24 Jam' : 'Tambah Pelayanan 24 Jam')
@section('breadcrumb')
    / <a href="{{ route('admin.pelayanan24jam.index') }}" class="hover:text-green-700">Pelayanan 24 Jam</a>
    / <span class="text-gray-700">{{ isset($item) && $item ? 'Edit' : 'Tambah' }}</span>
@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">
        {{ isset($item) && $item ? 'Edit Pelayanan: ' . $item->nama : 'Tambah Pelayanan 24 Jam' }}
    </h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST"
          action="{{ isset($item) && $item ? route('admin.pelayanan24jam.update', $item->id) : route('admin.pelayanan24jam.store') }}"
          enctype="multipart/form-data"
          class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($item) && $item) @method('PUT') @endif

        {{-- Nama --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Nama Layanan <span class="text-red-500">*</span>
            </label>
            <input type="text" name="nama"
                   value="{{ old('nama', $item->nama ?? '') }}"
                   placeholder="Instalasi Gawat Darurat (IGD)"
                   required
                   class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <p class="text-xs text-gray-400 mt-1">
                <i class="fas fa-info-circle mr-1"></i>Akan ditampilkan kapital semua (UPPERCASE) di website.
            </p>
        </div>

        {{-- Foto --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
            @if(isset($item) && $item && $item->foto)
                <div class="mb-2">
                    <img id="foto-existing" src="{{ asset('storage/' . $item->foto) }}"
                         class="w-48 h-32 object-cover rounded-lg border border-gray-200">
                    <p class="text-xs text-gray-400 mt-1">Foto saat ini</p>
                </div>
            @endif
            <img id="foto-preview" class="w-48 h-32 object-cover rounded-lg mb-2 hidden border border-gray-200">
            <input type="file" name="foto" accept="image/*" onchange="previewFoto(this)"
                   class="text-sm text-gray-600">
            <p class="text-xs text-gray-400 mt-1">Opsional. Maks. 2MB. Format: JPG, PNG.</p>
        </div>

        {{-- Deskripsi --}}
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">
                Deskripsi <span class="text-red-500">*</span>
            </label>
            <textarea name="deskripsi" rows="5" required
                      class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $item->deskripsi ?? '') }}</textarea>
        </div>

        {{-- Urutan & Status --}}
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $item->urutan ?? 0) }}" min="0"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1"
                       {{ old('aktif', $item->aktif ?? true) ? 'checked' : '' }}
                       class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif / Tampilkan</label>
            </div>
        </div>

        {{-- Tombol --}}
        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">
                <i class="fas fa-save mr-1"></i> Simpan
            </button>
            <a href="{{ route('admin.pelayanan24jam.index') }}"
               class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">
                Batal
            </a>
        </div>
    </form>
</div>
@endsection

@section('scripts')
<script>
function previewFoto(input) {
    const prev = document.getElementById('foto-preview');
    const existing = document.getElementById('foto-existing');
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
