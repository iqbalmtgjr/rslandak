@extends('layouts.admin')
@section('title', 'Dashboard')
@section('breadcrumb')@endsection

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-6">Dashboard</h1>

<!-- Stats Cards -->
<div class="grid grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
    <div class="bg-white rounded-xl p-5 shadow">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-blue-100 flex items-center justify-center"><i class="fas fa-images text-blue-600"></i></div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['hero_aktif'] }}</div>
        </div>
        <div class="text-sm text-gray-500">Hero Aktif</div>
        <div class="text-xs text-gray-400 mt-1">Total: {{ $stats['hero_total'] }}</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-green-100 flex items-center justify-center"><i class="fas fa-hand-holding-medical text-green-600"></i></div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['layanan_total'] }}</div>
        </div>
        <div class="text-sm text-gray-500">Layanan</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-purple-100 flex items-center justify-center"><i class="fas fa-user-md text-purple-600"></i></div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['dokter_aktif'] }}</div>
        </div>
        <div class="text-sm text-gray-500">Dokter Aktif</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-yellow-100 flex items-center justify-center"><i class="fas fa-newspaper text-yellow-600"></i></div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['berita_total'] }}</div>
        </div>
        <div class="text-sm text-gray-500">Berita</div>
        <div class="text-xs text-gray-400 mt-1">{{ number_format($stats['berita_views']) }} views</div>
    </div>
    <div class="bg-white rounded-xl p-5 shadow">
        <div class="flex items-center gap-3 mb-2">
            <div class="w-10 h-10 rounded-lg bg-red-100 flex items-center justify-center"><i class="fas fa-bed text-red-600"></i></div>
            <div class="text-2xl font-bold text-gray-800">{{ $stats['kamar_total'] }}</div>
        </div>
        <div class="text-sm text-gray-500">Kamar</div>
    </div>
</div>

<!-- Berita Terbaru -->
<div class="bg-white rounded-xl shadow p-6">
    <h2 class="font-bold text-gray-800 mb-4">Berita Terbaru</h2>
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead><tr class="text-left text-gray-500 border-b">
                <th class="pb-3 font-medium">Judul</th>
                <th class="pb-3 font-medium">Kategori</th>
                <th class="pb-3 font-medium">Views</th>
                <th class="pb-3 font-medium">Status</th>
                <th class="pb-3 font-medium">Tanggal</th>
            </tr></thead>
            <tbody class="divide-y divide-gray-50">
                @foreach($berita_terbaru as $b)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 font-medium text-gray-800">{{ Str::limit($b->judul, 50) }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2 py-0.5 rounded font-semibold
                            {{ $b->kategori === 'Berita' ? 'bg-blue-100 text-blue-700' : ($b->kategori === 'Pengumuman' ? 'bg-yellow-100 text-yellow-700' : 'bg-green-100 text-green-700') }}">
                            {{ $b->kategori }}
                        </span>
                    </td>
                    <td class="py-3 text-gray-600">{{ number_format($b->views) }}</td>
                    <td class="py-3">
                        <span class="text-xs px-2 py-0.5 rounded font-semibold {{ $b->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                            {{ $b->aktif ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td class="py-3 text-gray-500">{{ $b->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
