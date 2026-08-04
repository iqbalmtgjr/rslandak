@extends('layouts.admin')
@section('title', $skm->id ? 'Edit Hasil SKM' : 'Tambah Hasil SKM')
@section('breadcrumb') / <a href="{{ route('admin.skm.index') }}" class="hover:text-green-700">SKM</a> / <span class="text-gray-700">{{ $skm->id ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ $skm->id ? 'Edit Hasil SKM' : 'Tambah Hasil SKM' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ $skm->id ? route('admin.skm.update', $skm->id) : route('admin.skm.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if($skm->id) @method('PUT') @endif

        <div class="grid grid-cols-3 gap-4">
            <div class="col-span-1">
                <label class="block text-sm font-medium text-gray-700 mb-1">Tahun <span class="text-red-500">*</span></label>
                <input type="text" name="tahun" value="{{ old('tahun', $skm->tahun) }}" placeholder="2026" required maxlength="4" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="col-span-2">
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Penilaian <span class="text-red-500">*</span></label>
                <input type="text" name="judul" value="{{ old('judul', $skm->judul) }}" placeholder="Contoh: Hasil SKM Triwulan I" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Gambar Penilaian <span class="text-red-500">*</span></label>
            @if($skm->gambar)
            <img id="preview-existing" src="{{ Storage::url($skm->gambar) }}" class="w-64 h-40 object-cover rounded-lg mb-2 border">
            @endif
            <img id="preview-new" class="w-64 h-40 object-cover rounded-lg mb-2 border hidden">
            <input type="file" name="gambar" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600 block" {{ $skm->id ? '' : 'required' }}>
            <p class="text-xs text-gray-400 mt-1">Upload gambar/infografis hasil survey kepuasan. Format: JPG/PNG, maks 3MB.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" value="{{ old('urutan', $skm->urutan ?? 0) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $skm->id ? $skm->aktif : true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif / Tampilkan</label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.skm.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
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
