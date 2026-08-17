@extends('layouts.admin')
@section('title', 'Struktur Organisasi')
@section('breadcrumb') / <span class="text-gray-700">Struktur Organisasi</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Struktur Organisasi</h1>
    <a href="{{ route('admin.struktur.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Anggota
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-3">
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Cari Anggota</label>
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, jabatan, NIP..." class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-gray-500 mb-1">Filter Bidang</label>
            <select name="bidang_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
                <option value="">Semua Bidang</option>
                @foreach($bidangs as $bidang)
                <option value="{{ $bidang->id }}" {{ request('bidang_id') == $bidang->id ? 'selected' : '' }}>{{ $bidang->nama }}</option>
                @endforeach
            </select>
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-medium px-4 py-2 rounded-lg h-[38px]">Filter</button>
            @if(request('search') || request('bidang_id'))
            <a href="{{ route('admin.struktur.index') }}" class="text-gray-500 hover:text-gray-700 text-sm font-medium py-2">Reset</a>
            @endif
        </div>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-3 font-medium">Foto</th>
                    <th class="pb-3 font-medium">Nama</th>
                    <th class="pb-3 font-medium">Bidang / Kelompok</th>
                    <th class="pb-3 font-medium">Jabatan</th>
                    <th class="pb-3 font-medium">NIP / NRP</th>
                    <th class="pb-3 font-medium">Urutan</th>
                    <th class="pb-3 font-medium">Status</th>
                    <th class="pb-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($strukturs as $item)
                <tr class="hover:bg-gray-50">
                    <td class="py-3">
                        @if($item->foto)
                            <img src="{{ Storage::url($item->foto) }}" class="w-10 h-10 rounded-full object-cover border border-gray-100">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: linear-gradient(135deg,#2563EB,#60A5FA)">
                                {{ strtoupper(substr($item->nama, 0, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td class="py-3 font-medium text-gray-800">{{ $item->nama }}</td>
                    <td class="py-3 text-gray-600">{{ $item->bidang->nama ?? '-' }}</td>
                    <td class="py-3 text-gray-600">{{ $item->jabatan }}</td>
                    <td class="py-3 text-gray-500">{{ $item->nip ?: '-' }}</td>
                    <td class="py-3 text-gray-600">{{ $item->urutan }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.struktur.toggle', $item->id) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $item->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.struktur.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.struktur.destroy', $item->id) }}" onsubmit="return confirm('Hapus anggota ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-8 text-center text-gray-400">Belum ada data anggota struktur organisasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $strukturs->links() }}</div>
</div>
@endsection
