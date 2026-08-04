@extends('layouts.admin')
@section('title', 'Kelola Klinik')
@section('breadcrumb') / <span class="text-gray-700">Klinik</span>@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Kelola Klinik</h1>
    <div class="flex gap-2">
        <a href="{{ route('home') }}" target="_blank" class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold">
            <i class="fas fa-external-link-alt mr-1"></i> Lihat Website
        </a>
        <a href="{{ route('admin.poliklinik.create') }}" class="bg-green-700 hover:bg-green-800 text-white px-4 py-2 rounded-lg text-sm font-semibold">
            <i class="fas fa-plus mr-1"></i> Tambah Klinik
        </a>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-3 font-medium w-8">#</th>
                    <th class="pb-3 font-medium w-12">Ikon</th>
                    <th class="pb-3 font-medium">Nama / Slug</th>
                    <th class="pb-3 font-medium text-center">Dokter</th>
                    <th class="pb-3 font-medium text-center">Urutan</th>
                    <th class="pb-3 font-medium text-center">Status</th>
                    <th class="pb-3 font-medium">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($polikliniks as $poli)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3">
                        @if($poli->tipe_ikon === 'img' && $poli->ikon)
                            <img src="{{ asset('storage/' . $poli->ikon) }}" class="w-10 h-10 object-cover rounded-lg">
                        @elseif($poli->ikon)
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center" style="background: #e8f5e9">
                                <i class="fas {{ $poli->ikon }} text-green-700 text-lg"></i>
                            </div>
                        @else
                            <div class="w-10 h-10 rounded-lg flex items-center justify-center bg-gray-100">
                                <i class="fas fa-hospital text-gray-400"></i>
                            </div>
                        @endif
                    </td>
                    <td class="py-3">
                        <div class="font-medium text-gray-800">{{ $poli->nama }}</div>
                        <div class="text-xs text-gray-400">{{ $poli->slug }}</div>
                    </td>
                    <td class="py-3 text-center">
                        <span class="inline-flex items-center gap-1 text-xs px-2 py-0.5 rounded-full bg-blue-100 text-blue-700 font-semibold">
                            <i class="fas fa-user-md text-xs"></i> {{ $poli->dokters_count ?? 0 }}
                        </span>
                    </td>
                    <td class="py-3 text-center text-gray-600">{{ $poli->urutan }}</td>
                    <td class="py-3 text-center">
                        <form method="POST" action="{{ route('admin.poliklinik.toggle', $poli->id) }}">
                             @csrf
                            <button type="submit" class="text-xs px-3 py-1 rounded-full font-semibold {{ $poli->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }}">
                                {{ $poli->aktif ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </td>
                    <td class="py-3">
                        <div class="flex gap-1 flex-wrap">
                            <a href="{{ route('admin.poliklinik.dokter', $poli->id) }}"
                               class="text-purple-600 hover:text-purple-800 text-xs px-2 py-1 border border-purple-200 rounded">
                                <i class="fas fa-user-md mr-0.5"></i> Dokter
                            </a>
                            <a href="{{ route('admin.poliklinik.edit', $poli->id) }}"
                               class="text-blue-600 hover:text-blue-800 text-xs px-2 py-1 border border-blue-200 rounded">
                                Edit
                            </a>
                            <form method="POST" action="{{ route('admin.poliklinik.destroy', $poli->id) }}" onsubmit="return confirm('Hapus klinik ini?')">
                                @csrf @method('DELETE')
                                <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" class="py-12 text-center text-gray-400">
                        <i class="fas fa-clinic-medical text-4xl mb-3 block text-gray-200"></i>
                        Belum ada data klinik.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $polikliniks->links() }}</div>
</div>
@endsection
