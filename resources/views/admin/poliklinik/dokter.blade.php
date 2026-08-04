@extends('layouts.admin')
@section('title', 'Kelola Dokter — ' . $poli->nama)
@section('breadcrumb')
<<<<<<< HEAD
    / <a href="{{ route('admin.poliklinik.index') }}" class="hover:text-green-700">Klinik</a>
=======
    / <a href="{{ route('admin.poliklinik.index') }}" class="hover:text-green-700">Poliklinik</a>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
    / <span class="text-gray-700">Dokter: {{ $poli->nama }}</span>
@endsection

@section('content')
<div class="flex items-center justify-between mb-6">
    <div>
        <h1 class="text-2xl font-bold text-gray-800">Kelola Dokter</h1>
        <p class="text-sm text-gray-500 mt-0.5">
<<<<<<< HEAD
            Klinik: <span class="font-semibold text-green-700">{{ $poli->nama }}</span>
=======
            Poliklinik: <span class="font-semibold text-green-700">{{ $poli->nama }}</span>
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        </p>
    </div>
    <a href="{{ route('admin.poliklinik.index') }}"
       class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-4 py-2 rounded-lg text-sm font-semibold">
        <i class="fas fa-arrow-left mr-1"></i> Kembali
    </a>
</div>

<div x-data="{
    search: '',
    selected: {{ json_encode($assignedIds) }},
    get filtered() {
        const s = this.search.toLowerCase();
        return s ? Array.from(document.querySelectorAll('.dokter-item')).filter(el =>
            el.dataset.nama.toLowerCase().includes(s) || el.dataset.spesialis.toLowerCase().includes(s)
        ) : Array.from(document.querySelectorAll('.dokter-item'));
    },
    toggle(id) {
        const idx = this.selected.indexOf(id);
        if (idx > -1) this.selected.splice(idx, 1);
        else this.selected.push(id);
    },
    isSelected(id) { return this.selected.includes(id); }
}" class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Panel Kiri: Checklist Dokter --}}
    <div class="lg:col-span-2">
        <div class="bg-white rounded-xl shadow p-5">
            <div class="flex items-center justify-between mb-4">
                <h2 class="text-base font-bold text-gray-800">Pilih Dokter</h2>
                <span class="text-xs text-gray-400">{{ $semuaDokter->count() }} dokter aktif</span>
            </div>

            {{-- Search --}}
            <div class="relative mb-4">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-gray-400 text-sm"></i>
                <input type="text" x-model="search" placeholder="Cari nama atau spesialisasi..."
                       class="w-full pl-9 pr-4 py-2 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-green-500">
            </div>

            <form method="POST" action="{{ route('admin.poliklinik.dokter.sync', $poli->id) }}" id="sync-form">
                @csrf

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 max-h-96 overflow-y-auto pr-1">
                    @foreach($semuaDokter as $dokter)
                    <label class="dokter-item flex items-center gap-3 p-3 rounded-lg border cursor-pointer transition-all hover:border-green-400"
                           data-nama="{{ strtolower($dokter->nama) }}"
                           data-spesialis="{{ strtolower($dokter->spesialisasi) }}"
                           :class="isSelected({{ $dokter->id }}) ? 'border-green-500 bg-green-50' : 'border-gray-200'"
                           x-show="search === '' || '{{ strtolower($dokter->nama) }}'.includes(search.toLowerCase()) || '{{ strtolower($dokter->spesialisasi) }}'.includes(search.toLowerCase())">
                        <input type="checkbox" name="dokter_ids[]" value="{{ $dokter->id }}"
                               @change="toggle({{ $dokter->id }})"
                               :checked="isSelected({{ $dokter->id }})"
                               class="w-4 h-4 text-green-600 rounded">

                        {{-- Avatar --}}
                        @if($dokter->foto)
                            <img src="{{ Storage::url($dokter->foto) }}"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="w-10 h-10 rounded-full flex items-center justify-center flex-shrink-0 text-white font-bold text-sm"
                                 style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                                {{ strtoupper(substr($dokter->nama, 0, 1)) }}
                            </div>
                        @endif

                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-semibold text-gray-800 truncate">{{ $dokter->nama }}</div>
                            <div class="text-xs text-green-600 truncate">{{ $dokter->spesialisasi }}</div>
                        </div>
                    </label>
                    @endforeach
                </div>

                {{-- Simpan --}}
                <div class="flex gap-3 mt-5 pt-4 border-t border-gray-100">
                    <button type="submit"
                            class="bg-green-700 hover:bg-green-800 text-white px-6 py-2 rounded-lg text-sm font-semibold">
                        <i class="fas fa-save mr-1"></i> Simpan Perubahan
                    </button>
                    <a href="{{ route('admin.poliklinik.index') }}"
                       class="border border-gray-300 text-gray-600 hover:bg-gray-50 px-6 py-2 rounded-lg text-sm font-semibold">
                        Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    {{-- Panel Kanan: Summary --}}
    <div class="lg:col-span-1">
        <div class="bg-white rounded-xl shadow p-5 sticky top-24">
            <h2 class="text-base font-bold text-gray-800 mb-4">
                Dokter Terpilih
                <span class="ml-1 text-xs px-2 py-0.5 rounded-full bg-green-100 text-green-700 font-semibold"
                      x-text="selected.length"></span>
            </h2>

            <div class="space-y-2 max-h-80 overflow-y-auto">
                @foreach($semuaDokter as $dokter)
                <div class="flex items-center gap-2 p-2 rounded-lg bg-green-50 border border-green-200"
                     x-show="isSelected({{ $dokter->id }})">
                    @if($dokter->foto)
                        <img src="{{ Storage::url($dokter->foto) }}"
                             class="w-8 h-8 rounded-full object-cover flex-shrink-0">
                    @else
                        <div class="w-8 h-8 rounded-full flex items-center justify-center flex-shrink-0 text-white text-xs font-bold"
                             style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                            {{ strtoupper(substr($dokter->nama, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-1 min-w-0">
                        <div class="text-xs font-semibold text-gray-800 truncate">{{ $dokter->nama }}</div>
                        <div class="text-xs text-gray-500 truncate">{{ $dokter->spesialisasi }}</div>
                    </div>
                    <button type="button" @click="toggle({{ $dokter->id }})"
                            class="text-red-400 hover:text-red-600 flex-shrink-0">
                        <i class="fas fa-times text-xs"></i>
                    </button>
                </div>
                @endforeach

                <div x-show="selected.length === 0" class="py-8 text-center text-gray-400 text-sm">
                    <i class="fas fa-user-md text-3xl mb-2 block text-gray-200"></i>
                    Belum ada dokter dipilih
                </div>
            </div>

            <div class="mt-4 pt-4 border-t border-gray-100">
                <div class="text-xs text-gray-500">
                    <i class="fas fa-info-circle text-blue-400 mr-1"></i>
                    Centang dokter di kiri untuk assign ke poliklinik ini.
                </div>
            </div>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js" defer></script>
@endsection
