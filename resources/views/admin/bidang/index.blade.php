@extends('layouts.admin')
@section('title', 'Bidang Struktur')
@section('breadcrumb') / <span class="text-gray-700">Bidang Struktur</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Bidang Struktur</h1>
    <a href="{{ route('admin.bidang.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Bidang
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama bidang..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>

    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-3 font-medium">Nama Bidang</th>
                    <th class="pb-3 font-medium">Urutan</th>
                    <th class="pb-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($bidangs as $bidang)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 font-medium text-gray-800">{{ $bidang->nama }}</td>
                    <td class="py-3 text-gray-600">{{ $bidang->urutan }}</td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.bidang.edit', $bidang) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.bidang.destroy', $bidang) }}" onsubmit="return confirm('Hapus bidang ini? Semua anggota struktur di dalamnya juga akan terhapus.')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="3" class="py-8 text-center text-gray-400">Belum ada data bidang.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $bidangs->links() }}</div>
</div>
@endsection
