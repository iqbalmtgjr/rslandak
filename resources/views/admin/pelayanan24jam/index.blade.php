@extends('layouts.admin')
@section('title', 'Pelayanan 24 Jam')
@section('breadcrumb') / <span class="text-gray-700">Pelayanan 24 Jam</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Pelayanan 24 Jam</h1>
    <a href="{{ route('admin.pelayanan24jam.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Layanan
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-3 font-medium w-8">#</th>
                    <th class="pb-3 font-medium">Foto</th>
                    <th class="pb-3 font-medium">Nama</th>
                    <th class="pb-3 font-medium text-center">Urutan</th>
                    <th class="pb-3 font-medium text-center">Status</th>
                    <th class="pb-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($items as $item)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3">
                        @if($item->foto)
                            <img src="{{ asset('storage/' . $item->foto) }}" class="w-20 h-12 object-cover rounded-lg">
                        @else
                            <div class="w-20 h-12 rounded-lg" style="background: linear-gradient(135deg,#1E3A8A,#2563EB,#60A5FA)"></div>
                        @endif
                    </td>
                    <td class="py-3 font-semibold text-gray-800 uppercase tracking-wide">{{ $item->nama }}</td>
                    <td class="py-3 text-center text-gray-600">{{ $item->urutan }}</td>
                    <td class="py-3 text-center">
                        <form method="POST" action="{{ route('admin.pelayanan24jam.toggle', $item->id) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $item->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $item->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.pelayanan24jam.edit', $item->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.pelayanan24jam.destroy', $item->id) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="py-12 text-center text-gray-400">
                        <i class="fas fa-clock text-4xl mb-3 block text-gray-200"></i>
                        Belum ada data pelayanan 24 jam.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $items->links() }}</div>
</div>
@endsection
