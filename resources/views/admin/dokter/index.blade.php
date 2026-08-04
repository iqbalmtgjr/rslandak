@extends('layouts.admin')
@section('title', 'Dokter')
@section('breadcrumb') / <span class="text-gray-700">Dokter</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Dokter</h1>
    <a href="{{ route('admin.dokter.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Dokter
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama / spesialisasi..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-3 font-medium">Foto</th>
                <th class="pb-3 font-medium">Nama</th>
                <th class="pb-3 font-medium">Spesialisasi</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($dokters as $dokter)
                <tr class="hover:bg-gray-50">
                    <td class="py-3">
                        @if($dokter->foto)
                            <img src="{{ Storage::url($dokter->foto) }}" class="w-10 h-10 rounded-full object-cover">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center text-white text-sm font-bold" style="background: linear-gradient(135deg,#2563EB,#60A5FA)">
                                {{ strtoupper(substr($dokter->nama, 4, 1)) }}
                            </div>
                        @endif
                    </td>
                    <td class="py-3 font-medium text-gray-800">{{ $dokter->nama }}</td>
                    <td class="py-3 text-gray-600">{{ $dokter->spesialisasi }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.dokter.toggle', $dokter) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $dokter->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $dokter->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.dokter.edit', $dokter) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.dokter.destroy', $dokter) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada data dokter.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $dokters->links() }}</div>
</div>
@endsection
