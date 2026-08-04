@extends('layouts.admin')
@section('title', isset($dokter->id) ? 'Edit Dokter' : 'Tambah Dokter')
@section('breadcrumb') / <a href="{{ route('admin.dokter.index') }}" class="hover:text-green-700">Dokter</a> / <span class="text-gray-700">{{ isset($dokter->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($dokter->id) ? 'Edit Dokter' : 'Tambah Dokter' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($dokter->id) ? route('admin.dokter.update', $dokter) : route('admin.dokter.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($dokter->id)) @method('PUT') @endif

        <div class="grid grid-cols-2 gap-4">
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
                <input type="text" name="nama" value="{{ old('nama', $dokter->nama) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="col-span-2 md:col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Spesialisasi <span class="text-red-500">*</span></label>
                <input type="text" name="spesialisasi" value="{{ old('spesialisasi', $dokter->spesialisasi) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto</label>
            @if(isset($dokter->foto) && $dokter->foto)
            <img id="preview-existing" src="{{ Storage::url($dokter->foto) }}" class="w-20 h-20 rounded-full object-cover mb-2">
            @endif
            <img id="preview-new" class="w-20 h-20 rounded-full object-cover mb-2 hidden">
            <input type="file" name="foto" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
            <textarea name="bio" rows="3" data-no-wysiwyg class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('bio', $dokter->bio) }}</textarea>
        </div>

        <!-- Jadwal Dinamis -->
        <div x-data="{ jadwals: @json(old('jadwal') ?? $dokter->jadwal ?? []) }">
            <label class="block text-sm font-medium text-gray-700 mb-2">Jadwal Praktik</label>
            <template x-for="(j, i) in jadwals" :key="i">
                <div class="flex gap-2 mb-2">
                    <select :name="`jadwal[${i}][hari]`" x-model="j.hari" class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                        <option>Senin</option><option>Selasa</option><option>Rabu</option>
                        <option>Kamis</option><option>Jumat</option><option>Sabtu</option><option>Minggu</option>
                    </select>
                    <input :name="`jadwal[${i}][jam]`" x-model="j.jam" placeholder="08:00-12:00" class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
                    <button type="button" @click="jadwals.splice(i,1)" class="text-red-500 hover:text-red-700 px-2">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </template>
            <button type="button" @click="jadwals.push({hari:'Senin',jam:''})" class="text-sm text-green-700 border border-green-300 px-4 py-1.5 rounded-lg hover:bg-green-50 mt-1">
                <i class="fas fa-plus mr-1"></i> Tambah Jadwal
            </button>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $dokter->urutan ?? 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $dokter->aktif ?? true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.dokter.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
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
</script>
@endsection
