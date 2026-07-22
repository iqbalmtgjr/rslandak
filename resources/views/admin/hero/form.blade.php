@extends('layouts.admin')
@section('title', isset($hero->id) ? 'Edit Hero' : 'Tambah Hero')
@section('breadcrumb') / <a href="{{ route('admin.hero.index') }}" class="hover:text-green-700">Hero</a> / <span class="text-gray-700">{{ isset($hero->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($hero->id) ? 'Edit Hero' : 'Tambah Hero' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($hero->id) ? route('admin.hero.update', $hero) : route('admin.hero.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($hero->id)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul <span class="text-red-500">*</span></label>
            <input type="text" name="judul" value="{{ old('judul', $hero->judul) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Sub Judul</label>
            <textarea name="sub_judul" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">{{ old('sub_judul', $hero->sub_judul) }}</textarea>
        </div>
        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar</label>
            @if(isset($hero->gambar) && $hero->gambar)
            <img id="preview-existing" src="{{ Storage::url($hero->gambar) }}" class="w-48 h-28 object-cover rounded mb-2">
            @endif
            <img id="preview-new" class="w-48 h-28 object-cover rounded mb-2 hidden">
            <input type="file" name="gambar" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600">
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Teks Tombol</label>
                <input type="text" name="tombol_teks" value="{{ old('tombol_teks', $hero->tombol_teks) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">URL Tombol</label>
                <input type="text" name="tombol_url" value="{{ old('tombol_url', $hero->tombol_url) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $hero->urutan ?? 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $hero->aktif ?? true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.hero.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
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
