@extends('layouts.admin')
@section('title', 'Berita')
@section('breadcrumb') / <span class="text-gray-700">Berita</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Berita</h1>
    <a href="{{ route('admin.berita.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-plus mr-1"></i> Tambah Berita
    </a>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari judul berita..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-3 font-medium">Thumbnail</th>
                <th class="pb-3 font-medium">Judul</th>
                <th class="pb-3 font-medium">Kategori</th>
                <th class="pb-3 font-medium">Views</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium">Tanggal</th>
                <th class="pb-3 font-medium">Aksi</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($beritas as $berita)
                <tr class="hover:bg-gray-50">
                    <td class="py-3">
                        @if($berita->gambar)
                            <img src="{{ Storage::url($berita->gambar) }}" class="w-16 h-10 object-cover rounded">
                        @else
                            <div class="w-16 h-10 rounded" style="background: linear-gradient(135deg,#2563EB,#60A5FA)"></div>
                        @endif
                    </td>
                    <td class="py-3 font-medium text-gray-800">{{ Str::limit($berita->judul, 45) }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $berita->kategori === 'Berita' ? 'bg-blue-100 text-blue-700' : ($berita->kategori === 'Pengumuman' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">{{ $berita->kategori }}</span>
                    </td>
                    <td class="py-3 text-gray-600">{{ number_format($berita->views) }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.berita.toggle', $berita) }}">
                            @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $berita->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $berita->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3 text-gray-500">{{ $berita->created_at->format('d/m/Y') }}</td>
                    <td class="py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('admin.berita.edit', $berita) }}" class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">Edit</a>
                            <form method="POST" action="{{ route('admin.berita.destroy', $berita) }}" onsubmit="return confirm('Hapus?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="py-8 text-center text-gray-400">Belum ada data berita.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $beritas->links() }}</div>
</div>
@endsection
