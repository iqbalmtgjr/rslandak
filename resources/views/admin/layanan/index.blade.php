@extends('layouts.admin')
@section('title', 'Layanan')
@section('breadcrumb') / <span class="text-gray-700">Layanan</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Layanan</h1>
    <a href="{{ route('admin.layanan.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Layanan
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari layanan..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-3 font-medium">Nama</th>
                <th class="pb-3 font-medium">Ikon</th>
                <th class="pb-3 font-medium">Urutan</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($layanans as $layanan)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 font-medium text-gray-800">{{ $layanan->nama }}</td>
                    <td class="py-3"><i class="fas {{ $layanan->ikon }} text-green-600 mr-1"></i> <span class="text-gray-500 text-xs">{{ $layanan->ikon }}</span></td>
                    <td class="py-3 text-gray-600">{{ $layanan->urutan }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.layanan.toggle', $layanan) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $layanan->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $layanan->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.layanan.edit', $layanan) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.layanan.destroy', $layanan) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="py-8 text-center text-gray-400">Belum ada data layanan.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $layanans->links() }}</div>
</div>
@endsection
