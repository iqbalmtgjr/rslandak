@extends('layouts.admin')

@section('title', 'Kelola Download')

@section('breadcrumb')
  <span class="mx-2">/</span> <span class="text-gray-800">Download</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
  <h1 class="text-2xl font-bold text-gray-800">Kelola Download</h1>
  <div class="flex gap-2">
    <a href="{{ route('admin.download.file.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 text-white text-sm font-semibold rounded-xl hover:bg-green-800 transition-colors">
      <i class="fas fa-cloud-upload-alt"></i> Upload File
    </a>
    <a href="{{ route('admin.download.kategori.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 border border-green-700 text-green-700 text-sm font-semibold rounded-xl hover:bg-green-50 transition-colors">
      <i class="fas fa-folder-plus"></i> Tambah Kategori
    </a>
  </div>
</div>

{{-- Stats Row --}}
<div class="grid grid-cols-3 gap-4 mb-6">
  <div class="bg-white rounded-xl p-4 shadow-sm flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-green-100 flex items-center justify-center">
      <i class="fas fa-file-alt text-green-600 text-xl"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-gray-800">{{ \App\Models\Download::count() }}</p>
      <p class="text-xs text-gray-500">Total File</p>
    </div>
  </div>
  <div class="bg-white rounded-xl p-4 shadow-sm flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-blue-100 flex items-center justify-center">
      <i class="fas fa-download text-blue-600 text-xl"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-gray-800">{{ number_format($totalDownload) }}</p>
      <p class="text-xs text-gray-500">Total Download</p>
    </div>
  </div>
  <div class="bg-white rounded-xl p-4 shadow-sm flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-yellow-100 flex items-center justify-center">
      <i class="fas fa-folder text-yellow-600 text-xl"></i>
    </div>
    <div>
      <p class="text-2xl font-bold text-gray-800">{{ \App\Models\DownloadKategori::aktif()->count() }}</p>
      <p class="text-xs text-gray-500">Kategori Aktif</p>
    </div>
  </div>
</div>

{{-- Tab Switcher --}}
<div class="flex border-b border-gray-200 mb-6">
  <a href="{{ route('admin.download.index', ['tab' => 'file'] + request()->except('tab')) }}"
     class="px-6 py-3 text-sm font-medium transition-colors
            {{ $tab !== 'kategori' ? 'border-b-2 border-green-700 text-green-700' : 'text-gray-500 hover:text-gray-700' }}">
    <i class="fas fa-file-alt mr-1"></i> File
  </a>
  <a href="{{ route('admin.download.index', ['tab' => 'kategori']) }}"
     class="px-6 py-3 text-sm font-medium transition-colors
            {{ $tab === 'kategori' ? 'border-b-2 border-green-700 text-green-700' : 'text-gray-500 hover:text-gray-700' }}">
    <i class="fas fa-folder mr-1"></i> Kategori
  </a>
</div>

@if($tab !== 'kategori')
  {{-- Tab File --}}

  {{-- Filter Bar --}}
  <div class="bg-white rounded-xl shadow-sm p-4 mb-4">
    <form method="GET" action="{{ route('admin.download.index') }}" class="flex flex-wrap gap-3 items-end">
      <input type="hidden" name="tab" value="file">
      <div class="flex-1 min-w-[180px]">
        <label class="block text-xs text-gray-500 mb-1">Kategori</label>
        <select name="kat" class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
          <option value="">Semua Kategori</option>
          @foreach($kategoris as $k)
            <option value="{{ $k->id }}" {{ request('kat') == $k->id ? 'selected' : '' }}>{{ $k->nama }}</option>
          @endforeach
        </select>
      </div>
      <div class="flex-1 min-w-[200px]">
        <label class="block text-xs text-gray-500 mb-1">Cari Judul</label>
        <input type="text" name="cari" value="{{ request('cari') }}" placeholder="Nama file..."
               class="w-full border border-gray-200 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
      </div>
      <button type="submit" class="px-4 py-2 bg-green-700 text-white text-sm rounded-lg hover:bg-green-800 transition-colors">
        <i class="fas fa-filter mr-1"></i> Filter
      </button>
      @if(request('cari') || request('kat'))
        <a href="{{ route('admin.download.index', ['tab' => 'file']) }}"
           class="px-4 py-2 border border-gray-300 text-gray-600 text-sm rounded-lg hover:bg-gray-50 transition-colors">
          Reset
        </a>
      @endif
    </form>
  </div>

  {{-- Tabel File --}}
  <div class="bg-white rounded-xl shadow-sm overflow-hidden">
    <table class="w-full text-sm">
      <thead class="bg-gray-50 border-b border-gray-200">
        <tr>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">#</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tipe</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Judul & File</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Kategori</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Ukuran</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Download</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Status</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Tgl</th>
          <th class="px-4 py-3 text-left text-xs font-semibold text-gray-500 uppercase">Aksi</th>
        </tr>
      </thead>
      <tbody class="divide-y divide-gray-100">
        @forelse($files as $i => $f)
        <tr class="hover:bg-gray-50 transition-colors">
          <td class="px-4 py-3 text-gray-500">{{ $files->firstItem() + $i }}</td>
          <td class="px-4 py-3">
            <div class="w-10 h-10 rounded-lg {{ $f->bg_ikon }} flex items-center justify-center">
              <i class="fas {{ $f->ikon_file }} {{ $f->warna_ikon }}"></i>
            </div>
          </td>
          <td class="px-4 py-3 max-w-[200px]">
            <p class="font-medium text-gray-800 truncate">{{ $f->judul }}</p>
            <p class="text-xs text-gray-400 truncate">{{ $f->nama_file }}</p>
          </td>
          <td class="px-4 py-3">
            @if($f->kategori)
              <span class="px-2 py-1 text-xs rounded-full"
                    style="background: {{ $f->kategori->warna }}20; color: {{ $f->kategori->warna }}">
                {{ $f->kategori->nama }}
              </span>
            @endif
          </td>
          <td class="px-4 py-3 text-gray-600">{{ $f->ukuran_readable }}</td>
          <td class="px-4 py-3">
            <span class="px-2 py-1 bg-blue-100 text-blue-700 text-xs rounded-full font-semibold">
              {{ $f->jumlah_download }}x
            </span>
          </td>
          <td class="px-4 py-3">
            <form method="POST" action="{{ route('admin.download.file.toggle', $f->id) }}">
              @csrf
              <button type="submit"
                      class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors
                             {{ $f->aktif ? 'bg-green-500' : 'bg-gray-300' }}"
                      title="{{ $f->aktif ? 'Non-aktifkan' : 'Aktifkan' }}">
                <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                             {{ $f->aktif ? 'translate-x-4' : 'translate-x-1' }}"></span>
              </button>
            </form>
          </td>
          <td class="px-4 py-3 text-xs text-gray-400">{{ $f->created_at->format('d/m/Y') }}</td>
          <td class="px-4 py-3">
            <div class="flex items-center gap-1">
              <a href="{{ route('admin.download.file.edit', $f->id) }}"
                 class="px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
                Edit
              </a>
              <form method="POST" action="{{ route('admin.download.file.destroy', $f->id) }}"
                    onsubmit="return confirm('Hapus file {{ addslashes($f->judul) }}? File fisik juga akan dihapus.')">
                @csrf @method('DELETE')
                <button type="submit"
                        class="px-2 py-1 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
                  Hapus
                </button>
              </form>
            </div>
          </td>
        </tr>
        @empty
        <tr>
          <td colspan="9" class="px-4 py-12 text-center text-gray-400">
            <i class="fas fa-file-alt text-4xl mb-2 block"></i>
            Belum ada file. <a href="{{ route('admin.download.file.create') }}" class="text-green-700 hover:underline">Upload sekarang</a>
          </td>
        </tr>
        @endforelse
      </tbody>
    </table>
    @if($files->hasPages())
    <div class="px-4 py-3 border-t border-gray-100 flex items-center justify-between">
      <p class="text-xs text-gray-500">Menampilkan {{ $files->firstItem() }}–{{ $files->lastItem() }} dari {{ $files->total() }} file</p>
      <div class="flex gap-1">
        @if(!$files->onFirstPage())
          <a href="{{ $files->previousPageUrl() }}" class="px-3 py-1 text-xs border border-gray-200 rounded-lg hover:bg-gray-50">
            <i class="fas fa-chevron-left"></i>
          </a>
        @endif
        @foreach($files->getUrlRange(max(1, $files->currentPage()-2), min($files->lastPage(), $files->currentPage()+2)) as $page => $url)
          <a href="{{ $url }}" class="px-3 py-1 text-xs border rounded-lg {{ $page == $files->currentPage() ? 'bg-green-700 text-white border-green-700' : 'border-gray-200 hover:bg-gray-50' }}">
            {{ $page }}
          </a>
        @endforeach
        @if($files->hasMorePages())
          <a href="{{ $files->nextPageUrl() }}" class="px-3 py-1 text-xs border border-gray-200 rounded-lg hover:bg-gray-50">
            <i class="fas fa-chevron-right"></i>
          </a>
        @endif
      </div>
    </div>
    @endif
  </div>

@else
  {{-- Tab Kategori --}}
  <div class="flex justify-end mb-4">
    <a href="{{ route('admin.download.kategori.create') }}"
       class="inline-flex items-center gap-2 px-4 py-2 bg-green-700 text-white text-sm font-semibold rounded-xl hover:bg-green-800 transition-colors">
      <i class="fas fa-plus"></i> Tambah Kategori
    </a>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @forelse($kategoris as $kat)
    <div class="bg-white rounded-xl shadow-sm overflow-hidden">
      <div class="p-4">
        <div class="flex items-start justify-between mb-3">
          <div class="flex items-center gap-3">
            <div class="w-12 h-12 rounded-xl flex items-center justify-center text-xl"
                 style="background: {{ $kat->warna }}20; color: {{ $kat->warna }}">
              <i class="fas {{ $kat->ikon }}"></i>
            </div>
            <div>
              <h3 class="font-bold text-gray-800">{{ $kat->nama }}</h3>
              <span class="text-xs {{ $kat->aktif ? 'bg-green-100 text-green-700' : 'bg-gray-100 text-gray-500' }} px-2 py-0.5 rounded-full">
                {{ $kat->all_downloads_count }} file
              </span>
            </div>
          </div>
          <form method="POST" action="{{ route('admin.download.kategori.toggle', $kat->id) }}">
            @csrf
            <button type="submit"
                    class="relative inline-flex h-5 w-9 items-center rounded-full transition-colors
                           {{ $kat->aktif ? 'bg-green-500' : 'bg-gray-300' }}">
              <span class="inline-block h-3.5 w-3.5 transform rounded-full bg-white shadow transition-transform
                           {{ $kat->aktif ? 'translate-x-4' : 'translate-x-1' }}"></span>
            </button>
          </form>
        </div>
        @if($kat->deskripsi)
          <p class="text-sm text-gray-500 mb-2">{{ $kat->deskripsi }}</p>
        @endif
        <p class="text-xs text-gray-400">Urutan: {{ $kat->urutan }}</p>
      </div>
      <div class="px-4 pb-4 flex gap-2">
        <a href="{{ route('admin.download.kategori.edit', $kat->id) }}"
           class="flex-1 text-center px-3 py-1.5 text-xs bg-blue-100 text-blue-700 rounded-lg hover:bg-blue-200 transition-colors">
          Edit
        </a>
        <a href="{{ route('admin.download.index', ['tab' => 'file', 'kat' => $kat->id]) }}"
           class="flex-1 text-center px-3 py-1.5 text-xs bg-gray-100 text-gray-600 rounded-lg hover:bg-gray-200 transition-colors">
          Lihat File
        </a>
        @if($kat->all_downloads_count > 0)
          <button type="button" disabled
                  title="Hapus semua file kategori ini dulu"
                  class="px-3 py-1.5 text-xs bg-gray-100 text-gray-400 rounded-lg cursor-not-allowed">
            Hapus
          </button>
        @else
          <form method="POST" action="{{ route('admin.download.kategori.destroy', $kat->id) }}"
                onsubmit="return confirm('Hapus kategori {{ addslashes($kat->nama) }}?')">
            @csrf @method('DELETE')
            <button type="submit"
                    class="px-3 py-1.5 text-xs bg-red-100 text-red-700 rounded-lg hover:bg-red-200 transition-colors">
              Hapus
            </button>
          </form>
        @endif
      </div>
    </div>
    @empty
    <div class="col-span-3 py-16 text-center text-gray-400 bg-white rounded-xl shadow-sm">
      <i class="fas fa-folder-open text-5xl mb-3 block"></i>
      <p>Belum ada kategori. <a href="{{ route('admin.download.kategori.create') }}" class="text-green-700 hover:underline">Tambah sekarang</a></p>
    </div>
    @endforelse
  </div>
@endif

@endsection
