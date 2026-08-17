@extends('layouts.admin')
@section('title', isset($bidang->id) ? 'Edit Bidang' : 'Tambah Bidang')
@section('breadcrumb') / <a href="{{ route('admin.bidang.index') }}" class="hover:text-green-700">Bidang</a> / <span class="text-gray-700">{{ isset($bidang->id) ? 'Edit' : 'Tambah' }}</span>@endsection

@section('content')
<div class="max-w-2xl">
    <h1 class="text-2xl font-bold text-gray-800 mb-6">{{ isset($bidang->id) ? 'Edit Bidang' : 'Tambah Bidang' }}</h1>

    @if($errors->any())
    <div class="bg-red-50 border border-red-300 text-red-700 px-4 py-3 rounded-lg mb-4 text-sm">
        <ul class="list-disc list-inside">@foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach</ul>
    </div>
    @endif

    <form method="POST" action="{{ isset($bidang->id) ? route('admin.bidang.update', $bidang) : route('admin.bidang.store') }}" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @if(isset($bidang->id)) @method('PUT') @endif

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Nama Bidang / Divisi <span class="text-red-500">*</span></label>
            <input type="text" name="nama" value="{{ old('nama', $bidang->nama) }}" required placeholder="Contoh: Direksi, Bidang Keperawatan, Bagian Tata Usaha" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <p class="text-xs text-gray-400 mt-1">Nama ini akan menjadi judul kelompok pada tampilan struktur organisasi di website.</p>
        </div>

        <div>
            <label class="block text-sm font-medium text-gray-700 mb-1">Urutan Tampilan</label>
            <input type="number" name="urutan" value="{{ old('urutan', $bidang->urutan) }}" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            <p class="text-xs text-gray-400 mt-1">Gunakan angka untuk menentukan urutan kelompok (misal: 1 untuk paling atas, lalu 2, dst).</p>
        </div>

        <div class="flex gap-3 pt-2">
            <button type="submit" class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">Simpan</button>
            <a href="{{ route('admin.bidang.index') }}" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">Batal</a>
        </div>
    </form>
</div>
@endsection
