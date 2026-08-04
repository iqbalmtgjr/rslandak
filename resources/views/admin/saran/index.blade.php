@extends('layouts.admin')
@section('title', 'Kotak Saran & Feedback Pasien')
@section('breadcrumb') / <span class="text-gray-700">Kotak Saran</span>@endsection

@section('content')
<div class="mb-6">
    <h1 class="text-2xl font-bold text-gray-800">Kotak Saran & Feedback</h1>
    <p class="text-sm text-gray-500">Kumpulan respon like/dislike dan keluhan/saran pasien.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6">
    <div class="bg-green-50 border border-green-200 rounded-xl p-4 flex items-center justify-between shadow-sm">
        <div>
            <div class="text-sm font-semibold text-green-800">Total Like (Jempol Atas)</div>
            <div class="text-3xl font-bold text-green-700 mt-1">{{ $countLikes }}</div>
        </div>
        <i class="fas fa-thumbs-up text-4xl text-green-500/30"></i>
    </div>
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between shadow-sm">
        <div>
            <div class="text-sm font-semibold text-red-800">Total Dislike (Jempol Bawah)</div>
            <div class="text-3xl font-bold text-red-700 mt-1">{{ $countDislikes }}</div>
        </div>
        <i class="fas fa-thumbs-down text-4xl text-red-500/30"></i>
    </div>
</div>

<div class="bg-white rounded-xl shadow p-6">
    <form method="GET" class="mb-4 flex gap-2">
        <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari isi pesan..." class="border border-gray-300 rounded-lg px-3 py-2 text-sm flex-1 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button class="bg-gray-100 hover:bg-gray-200 px-4 py-2 rounded-lg text-sm">Cari</button>
    </form>
    
    <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead>
                <tr class="text-left text-gray-500 border-b">
                    <th class="pb-3 font-medium w-8">#</th>
                    <th class="pb-3 font-medium w-32">Tipe Respon</th>
                    <th class="pb-3 font-medium">Isi Saran / Keluhan</th>
                    <th class="pb-3 font-medium w-40">Tanggal Masuk</th>
                    <th class="pb-3 font-medium w-20">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($sarans as $saran)
                <tr class="hover:bg-gray-50">
                    <td class="py-3 text-gray-400">{{ $loop->iteration }}</td>
                    <td class="py-3">
                        @if($saran->tipe === 'like')
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-bold bg-green-100 text-green-700">
                                <i class="fas fa-thumbs-up"></i> Like
                            </span>
                        @else
                            <span class="inline-flex items-center gap-1.5 text-xs px-2.5 py-1 rounded-full font-bold bg-red-100 text-red-700">
                                <i class="fas fa-thumbs-down"></i> Dislike
                            </span>
                        @endif
                    </td>
                    <td class="py-3 text-gray-800 whitespace-pre-line leading-relaxed">{{ $saran->pesan ?? '-' }}</td>
                    <td class="py-3 text-gray-500">{{ $saran->created_at->translatedFormat('d M Y H:i') }}</td>
                    <td class="py-3">
                        <form method="POST" action="{{ route('admin.saran.destroy', $saran->id) }}" onsubmit="return confirm('Hapus respon ini?')">
                            @csrf @method('DELETE')
                            <button class="text-red-600 hover:text-red-800 text-xs px-2 py-1 border border-red-200 rounded">Hapus</button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-gray-400">
                        <i class="fas fa-inbox text-4xl mb-3 block text-gray-200"></i>
                        Belum ada saran/feedback masuk.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $sarans->links() }}</div>
</div>
@endsection
