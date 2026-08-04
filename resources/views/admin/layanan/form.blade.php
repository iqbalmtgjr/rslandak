@extends('layouts.admin')
@section('title', isset($layanan->id) ? 'Edit Layanan' : 'Tambah Layanan')
@section('breadcrumb') / <a href="{{ route('admin.layanan.index') }}" class="hover:text-green-700">Layanan</a> / <span class="text-gray-700">{{ isset($layanan->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($layanan->id) ? 'Edit Layanan' : 'Tambah Layanan' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($layanan->id) ? route('admin.layanan.update', $layanan) : route('admin.layanan.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($layanan->id)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $layanan->nama) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi <span class="text-red-500">*</span></label>
            <textarea name="deskripsi" rows="4" required data-no-wysiwyg class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('deskripsi', $layanan->deskripsi) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Ikon Font Awesome <span class="text-red-500">*</span></label>
            <div class="flex gap-2 items-center">
                <input type="text" name="ikon" id="ikon-input" value="{{ old('ikon', $layanan->ikon) }}" required placeholder="fa-heartbeat" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <div class="w-10 h-10 bg-gray-50 border rounded-lg flex items-center justify-center">
                    <i id="ikon-preview" class="fas {{ old('ikon', $layanan->ikon ?? 'fa-star') }} text-green-600"></i>
                </div>
            </div>
            <p class="text-xs text-gray-400 mt-1">Contoh: fa-heartbeat, fa-ambulance, fa-stethoscope</p>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar (opsional)</label>
            @if(isset($layanan->gambar) && $layanan->gambar)
            <img id="preview-existing" src="{{ Storage::url($layanan->gambar) }}" class="w-32 h-20 object-cover rounded mb-2">
            @endif
            <img id="preview-new" class="w-32 h-20 object-cover rounded mb-2 hidden">
            <input type="file" name="gambar" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $layanan->urutan ?? 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $layanan->aktif ?? true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.layanan.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('ikon-input').addEventListener('input', function() {
    document.getElementById('ikon-preview').className = 'fas ' + this.value + ' text-green-600';
});
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
