@extends('layouts.admin')
@section('title', 'Kelola Hasil Penilaian SKM')
@section('breadcrumb') / <span class="text-gray-700">SKM</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Kelola Hasil Penilaian SKM</h1>
    <div class="flex gap-2">
        <a href="{{ route('admin.skm.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
            <i class="fas fa-plus mr-1"></i> Tambah Hasil SKM
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-3 font-medium w-8">#</th>
                    <th class="pb-3 font-medium w-32">Gambar</th>
                    <th class="pb-3 font-medium w-24">Tahun</th>
                    <th class="pb-3 font-medium">Judul Penilaian</th>
                    <th class="pb-3 font-medium text-center">Urutan</th>
                    <th class="pb-3 font-medium text-center">Status</th>
                    <th class="pb-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($skms as $skm)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3">
                        <img src="{{ Storage::url($skm->gambar) }}" class="w-24 h-16 object-cover rounded-lg border">
                    </td>
                    <td class="py-3 font-bold text-gray-700">{{ $skm->tahun }}</td>
                    <td class="py-3 font-medium text-gray-800">{{ $skm->judul }}</td>
                    <td class="py-3 text-center text-gray-600">{{ $skm->urutan }}</td>
                    <td class="py-3 text-center">
                        <form method="POST" action="{{ route('admin.skm.toggle', $skm->id) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $skm->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $skm->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-1 flex-wrap">
                            <a href="{{ route('admin.skm.edit', $skm->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.skm.destroy', $skm->id) }}" onsubmit="return confirm('Hapus hasil SKM ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        <i class="fas fa-poll text-4xl mb-3 block text-gray-200"></i>
                        Belum ada data penilaian SKM.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $skms->links() }}</div>
</div>
@endsection
