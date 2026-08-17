@extends('layouts.admin')
@section('title', isset($struktur->id) ? 'Edit Anggota Struktur' : 'Tambah Anggota Struktur')
@section('breadcrumb') / <a href="{{ route('admin.struktur.index') }}" class="hover:text-green-700">Struktur Organisasi</a> / <span class="text-gray-700">{{ isset($struktur->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($struktur->id) ? 'Edit Anggota Struktur' : 'Tambah Anggota Struktur' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($struktur->id) ? route('admin.struktur.update', $struktur->id) : route('admin.struktur.store') }}" enctype="multipart/form-data" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($struktur->id)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Bidang / Kelompok <span class="text-red-500">*</span></label>
            <select name="bidang_id" id="bidang_id" required class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">-- Pilih Bidang --</option>
                @foreach($bidangs as $bidang)
                <option value="{{ $bidang->id }}" {{ old('bidang_id', $struktur->bidang_id) == $bidang->id ? 'selected' : '' }}>{{ $bidang->nama }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap & Gelar <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $struktur->nama) }}" required placeholder="Contoh: dr. John Doe, Sp.B" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Jabatan <span class="text-red-500">*</span></label>
            <input type="text" name="jabatan" value="{{ old('jabatan', $struktur->jabatan) }}" required placeholder="Contoh: Kepala Bidang Pelayanan Medis" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">NIP / NRP (opsional)</label>
            <input type="text" name="nip" value="{{ old('nip', $struktur->nip) }}" placeholder="Contoh: 19800101 200501 1 001" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Foto Profil</label>
            @if(isset($struktur->foto) && $struktur->foto)
            <img id="preview-existing" src="{{ Storage::url($struktur->foto) }}" class="w-32 h-40 object-cover rounded border border-gray-200 mb-2">
            @endif
            <img id="preview-new" class="w-32 h-40 object-cover rounded border border-gray-200 mb-2 hidden">
            <input type="file" name="foto" accept="image/*" onchange="previewImg(this)" class="text-sm text-gray-600 block">
            <p class="text-xs text-gray-400 mt-1">Rekomendasi ukuran rasio 3:4 atau persegi, maks 2MB.</p>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Urutan</label>
                <input type="number" name="urutan" id="urutan" value="{{ old('urutan', $struktur->urutan) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <p class="text-xs text-gray-400 mt-1">Urutan anggota di dalam bidang (1 teratas, lalu 2, dst).</p>
            </div>
            <div class="flex items-center gap-3 pt-6">
                <input type="checkbox" name="aktif" id="aktif" value="1" {{ old('aktif', $struktur->aktif ?? true) ? 'checked' : '' }} class="w-4 h-4 text-green-600">
                <label for="aktif" class="text-sm font-medium text-gray-700">Aktif</label>
            </div>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.struktur.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
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
        reader.onload = e => {
            prev.src = e.target.result;
            prev.classList.remove('hidden');
            if(existing) existing.classList.add('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    }
}

document.addEventListener('DOMContentLoaded', function() {
    const bidangSelect = document.getElementById('bidang_id');
    const urutanInput = document.getElementById('urutan');
    
    function fetchNextUrutan() {
        const bidangId = bidangSelect.value;
        if (bidangId) {
            fetch("{{ route('admin.struktur.next-urutan') }}?bidang_id=" + bidangId)
                .then(response => response.json())
                .then(data => {
                    if (data.urutan !== undefined) {
                        urutanInput.value = data.urutan;
                    }
                })
                .catch(error => console.error('Error fetching next urutan:', error));
        }
    }

    bidangSelect.addEventListener('change', fetchNextUrutan);
    
    // Only auto-fetch on load if creating a new record (urutan is 0 or empty)
    @if(!isset($struktur->id) || old('urutan') === null)
        if (bidangSelect.value && (!urutanInput.value || urutanInput.value == 0)) {
            fetchNextUrutan();
        }
    @endif
});
</script>
@endsection
