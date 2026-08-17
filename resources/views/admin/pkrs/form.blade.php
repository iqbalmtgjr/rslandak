@extends('layouts.admin')
@section('title', isset($item->id) ? 'Edit Edukasi PKRS' : 'Tambah Edukasi PKRS')
@section('breadcrumb') / <a href="{{ route('admin.pkrs.index') }}" class="hover:text-green-700">Promosi Kesehatan</a> / <span class="text-gray-700">{{ isset($item->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-3xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($item->id) ? 'Edit Edukasi PKRS' : 'Tambah Edukasi PKRS' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($item->id) ? route('admin.pkrs.update', $item->id) : route('admin.pkrs.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($item->id)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Judul Edukasi <span class="text-red-500">*</span></label>
            <input type="text" name="judul" id="judul-input" value="{{ old('judul', $item->judul) }}" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <div class="text-xs text-gray-400 mt-1">Slug: <span id="slug-preview" class="text-gray-600">{{ $item->slug ?? '' }}</span></div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Penulis</label>
            <input type="text" name="penulis" value="{{ old('penulis', $item->penulis ?? 'Admin PKRS') }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Konten / Edukasi <span class="text-red-500">*</span></label>
            <textarea name="konten" rows="12" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500 font-mono">{{ old('konten', $item->konten) }}</textarea>
            <p class="text-xs text-gray-400 mt-1">Mendukung HTML dasar (&lt;p&gt;, &lt;strong&gt;, &lt;ul&gt;, dll)</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Banner / Infografis</label>
            @if(isset($item->gambar) && $item->gambar)
            <img id="preview-existing" src="{{ Storage::url($item->gambar) }}" class="w-48 h-28 object-cover rounded mb-2">
            @endif
            <img id="preview-new" class="w-48 h-28 object-cover rounded mb-2 hidden">
            <input type="file" name="gambar" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600">
        </div>

        <div class="flex items-center gap-3">
            <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $item->id ? $item->aktif : true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
            <label for="aktif" class="text-sm font-medium text-gray-700">Aktif / Tampilkan</label>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.pkrs.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
@section('scripts')
<script>
document.getElementById('judul-input').addEventListener('input', function() {
    const slug = this.value.toLowerCase().replace(/[^a-z0-9\s-]/g, '').replace(/\s+/g, '-').replace(/-+/g, '-');
    document.getElementById('slug-preview').textContent = slug;
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
