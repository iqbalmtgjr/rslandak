@extends('layouts.admin')
@section('title', 'Kelola Alur Pelayanan')
@section('content')

<div class="flex items-center justify-between mb-6">
  <div>
    <h1 class="text-2xl font-bold text-gray-800">Alur Pelayanan</h1>
    <p class="text-sm text-gray-500 mt-1">Kelola daftar alur pelayanan yang ditampilkan ke pengunjung</p>
  </div>
  <a href="{{ route('admin.alur-pelayanan.create') }}"
     class="inline-flex items-center gap-2 bg-primary hover:bg-dark text-white px-4 py-2 rounded-xl text-sm font-semibold transition-colors">
    <i class="fas fa-plus"></i> Tambah Alur Pelayanan
  </a>
</div>

{{-- Info box --}}
<div class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex gap-3">
  <i class="fas fa-info-circle text-blue-500 mt-0.5 flex-shrink-0"></i>
  <p class="text-sm text-blue-700">
    Gambar alur biasanya berupa infografis/bagan. Rekomendasikan format <strong>landscape</strong>
    (lebar &gt; tinggi) dengan resolusi minimal 1200px. Format: JPG atau PNG, maks 5MB.
  </p>
</div>

<div class="bg-white rounded-2xl shadow-sm overflow-hidden">
  <table class="w-full text-sm">
    <thead class="bg-gray-50 border-b border-gray-100">
      <tr>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold w-10">#</th>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Preview</th>
        <th class="px-4 py-3 text-left text-gray-600 font-semibold">Judul</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-20">Urutan</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-24">Status</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-28">Tanggal</th>
        <th class="px-4 py-3 text-center text-gray-600 font-semibold w-32">Aksi</th>
      </tr>
    </thead>
    <tbody class="divide-y divide-gray-50">
      @forelse($alurs as $i => $alur)
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-4 py-3 text-gray-500">{{ $alurs->firstItem() + $i }}</td>

          <td class="px-4 py-3">
            @if($alur->gambar_url)
              <a href="{{ $alur->gambar_url }}" target="_blank" title="Lihat gambar penuh">
                <img src="{{ $alur->gambar_url }}"
                     alt="{{ $alur->judul }}"
                     class="w-32 h-20 object-cover rounded-lg shadow-sm hover:opacity-80 transition-opacity">
              </a>
            @else
              <div class="w-32 h-20 bg-gray-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-image text-gray-300 text-2xl"></i>
              </div>
            @endif
          </td>

          <td class="px-4 py-3">
            <p class="font-medium text-gray-800">{{ $alur->judul }}</p>
            @if($alur->keterangan)
              <p class="text-xs text-gray-400 line-clamp-1 mt-0.5">{{ $alur->keterangan }}</p>
            @endif
          </td>

          <td class="px-4 py-3 text-center text-gray-600">{{ $alur->urutan }}</td>

          <td class="px-4 py-3 text-center">
            <form method="POST" action="{{ route('admin.alur-pelayanan.toggle', $alur->id) }}">
              @csrf
              <button type="submit"
                      class="px-3 py-1 rounded-full text-xs font-semibold transition-colors
                             {{ $alur->aktif
                                ? 'bg-green-100 text-green-700 hover:bg-green-200'
                                : 'bg-gray-100 text-gray-500 hover:bg-gray-200' }}">
                {{ $alur->aktif ? 'Aktif' : 'Non-aktif' }}
              </button>
            </form>
          </td>

          <td class="px-4 py-3 text-center text-gray-500 text-xs">
            {{ $alur->created_at->format('d/m/Y') }}
          </td>

          <td class="px-4 py-3 text-center">
            <div class="flex items-center justify-center gap-2">
              <a href="{{ route('admin.alur-pelayanan.edit', $alur->id) }}"
                 class="text-xs bg-blue-50 text-blue-600 hover:bg-blue-100 px-3 py-1.5 rounded-lg font-medium transition-colors">
                <i class="fas fa-edit"></i> Edit
              </a>
              <form method="POST" action="{{ route('admin.alur-pelayanan.destroy', $alur->id) }}"
                    onsubmit="return confirm('Yakin hapus alur \'{{ addslashes($alur->judul) }}\'? Gambar akan ikut terhapus.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="text-xs bg-red-50 text-red-600 hover:bg-red-100 px-3 py-1.5 rounded-lg font-medium transition-colors">
                  <i class="fas fa-trash"></i> Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>
      @empty
        <tr>
          <td colspan="7" class="px-4 py-16 text-center text-gray-400">
            <i class="fas fa-sitemap text-4xl mb-3 block"></i>
            Belum ada alur pelayanan. <a href="{{ route('admin.alur-pelayanan.create') }}" class="text-primary hover:underline">Tambah sekarang</a>.
          </td>
        </tr>
      @endforelse
    </tbody>
  </table>
</div>

@if($alurs->hasPages())
  <div class="mt-4">{{ $alurs->links() }}</div>
@endif

@endsection
