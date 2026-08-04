@extends('layouts.admin')
@section('title', 'Fasilitas')
@section('breadcrumb') / <span class="text-gray-700">Fasilitas</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Fasilitas</h1>
    <a href="{{ route('admin.fasilitas.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Fasilitas
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari fasilitas..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-3 font-medium">Gambar</th>
                <th class="pb-3 font-medium">Nama</th>
                <th class="pb-3 font-medium">Tipe</th>
                <th class="pb-3 font-medium">Urutan</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($fasilitas as $item)
                <tr class="hover:bg-gray-50">
                    <td class="py-3">
                        @if($item->gambar)
                            <img src="{{ Storage::url($item->gambar) }}" class="w-12 h-12 object-cover rounded-lg">
                        @else
                            <div class="w-12 h-12 rounded-lg flex items-center justify-center" style="background: linear-gradient(135deg,#1E3A8A,#60A5FA)">
                                <i class="fas fa-hospital text-white text-sm"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-3 font-medium text-gray-800">{{ $item->nama }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2 py-1 rounded-full font-semibold {{ $item->untuk_difabel ? 'bg-amber-100 text-amber-700' : 'bg-blue-100 text-blue-700' }}">
                            {{ $item->untuk_difabel ? 'Difabel' : 'Umum' }}
                        </span>
                    </td>
                    <td class="py-3 text-gray-600">{{ $item->urutan }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.fasilitas.toggle', $item->id) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $item->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.fasilitas.edit', $item->id) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.fasilitas.destroy', $item->id) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="py-8 text-center text-gray-400">Belum ada data fasilitas.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $fasilitas->links() }}</div>
</div>
@endsection
