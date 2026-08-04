@extends('layouts.app')
@section('title', 'Rawat Inap — RSUD Landak')
@section('content')

@include('partials.page-header', ['judul' => 'Rawat Inap', 'parent' => 'Layanan'])

<section class="py-12 bg-white min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl"
       x-data="rawatInap({{ $kamarsJson }})">

    {{-- Dropdown Pilih Tipe Kamar --}}
    <div class="mb-10">
      <label class="block text-sm font-medium text-gray-700 mb-2">Pilih Tipe Kamar</label>
      <div class="relative w-72">
        <select x-model="selectedId" @change="onSelect()"
                class="w-full appearance-none border border-gray-300 rounded-lg px-4 py-3 pr-10 text-sm text-gray-700 bg-white focus:outline-none focus:border-green-500 cursor-pointer">
          <option value="">-- Pilih Tipe Kamar --</option>
          @foreach($kamars as $k)
            <option value="{{ $k->id }}">{{ $k->nama }}</option>
          @endforeach
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3 bg-green-700 rounded-r-lg">
          <i class="fas fa-chevron-down text-white text-xs"></i>
        </div>
      </div>
    </div>

    {{-- State awal --}}
    <div x-show="selected === null" class="py-20 text-center text-gray-400">
      <i class="fas fa-hand-point-up text-5xl mb-4 block"></i>
      <p>Pilih tipe kamar di atas untuk melihat detail fasilitas</p>
    </div>

    {{-- Konten kamar --}}
    <div x-show="selected !== null"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0">

      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- Gallery Kiri --}}
        <div class="lg:col-span-7">
          <div class="relative rounded-2xl overflow-hidden shadow-md mb-3" style="min-height:320px; background:#DCFCE7;">
            <template x-if="selected && selected.fotos && selected.fotos.length > 0">
              <img :src="activeFoto" :alt="selected.nama" class="w-full h-80 object-cover transition-opacity duration-300">
            </template>
            <template x-if="!selected || !selected.fotos || selected.fotos.length === 0">
              <div class="w-full h-80 flex items-center justify-center bg-green-50">
                <div class="text-center text-green-700 opacity-40">
                  <i class="fas fa-bed text-7xl block mb-3"></i>
                  <p class="text-sm font-medium">Foto belum tersedia</p>
                </div>
              </div>
            </template>
            <template x-if="selected && selected.badge">
              <div class="absolute top-4 left-4 bg-green-700 text-white text-xs font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider shadow">
                <span x-text="selected.badge"></span>
              </div>
            </template>
          </div>

          {{-- Thumbnails --}}
          <template x-if="selected && selected.fotos && selected.fotos.length > 1">
            <div class="grid grid-cols-4 gap-2">
              <template x-for="(foto, idx) in selected.fotos" :key="idx">
                <button type="button" @click="activeFoto = foto"
                        class="rounded-xl overflow-hidden border-2 transition-all duration-200 aspect-square"
                        :class="activeFoto === foto ? 'border-green-600 shadow-md ring-2 ring-green-300' : 'border-transparent hover:border-green-400'">
                  <img :src="foto" class="w-full h-full object-cover">
                </button>
              </template>
            </div>
          </template>
        </div>

        {{-- Info Kanan --}}
        <div class="lg:col-span-5">
          <div class="border-l-4 border-yellow-500 pl-5 mb-6">
            <h2 class="font-playfair text-3xl font-bold text-gray-800" x-text="selected ? 'Fasilitas ' + selected.nama : ''"></h2>
          </div>

          <template x-if="selected && selected.deskripsi">
            <p class="text-gray-600 text-sm mb-5 leading-relaxed" x-text="selected.deskripsi"></p>
          </template>

          <template x-if="selected && selected.fasilitas && selected.fasilitas.length > 0">
            <ol class="space-y-2 mb-6">
              <template x-for="(item, idx) in selected.fasilitas" :key="idx">
                <li class="flex items-start gap-2 text-gray-700 text-sm border-b border-dashed border-gray-100 pb-2 last:border-b-0">
                  <span class="font-medium text-gray-500 flex-shrink-0" x-text="(idx + 1) + '.'"></span>
                  <span x-text="item"></span>
                </li>
              </template>
            </ol>
          </template>

          <template x-if="selected && selected.tarif_text">
            <div class="pt-4 border-t border-gray-200">
              <p class="text-gray-800 text-base font-medium">
                Tarif :
                <span class="text-green-700 font-bold text-lg" x-text="selected.tarif_text"></span>
                <span class="text-gray-500 font-normal text-sm"> / Hari</span>
              </p>
              <p class="text-xs text-gray-400 mt-1">*Tarif belum termasuk biaya tindakan medis dan obat-obatan</p>
            </div>
          </template>

          <template x-if="selected && !selected.tarif_text">
            <div class="pt-4 border-t border-gray-200">
              <p class="text-sm text-gray-400 italic">Untuk informasi tarif, silakan hubungi bagian administrasi.</p>
            </div>
          </template>

          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('home') }}#kontak"
               class="flex-1 flex items-center justify-center gap-2 py-3 px-5 bg-green-700 text-white rounded-xl text-sm font-medium hover:bg-green-800 transition-colors">
              <i class="fas fa-phone"></i> Hubungi Kami
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

@push('scripts')
<script>
function rawatInap(kamarsData) {
  return {
    kamars: kamarsData, selectedId: '', selected: null, activeFoto: null,
    onSelect() {
      if (!this.selectedId) { this.selected = null; this.activeFoto = null; return; }
      this.selected = this.kamars.find(k => k.id == this.selectedId) || null;
      this.activeFoto = (this.selected && this.selected.fotos && this.selected.fotos.length > 0)
        ? this.selected.fotos[0] : null;
    },
    init() { this.selected = null; }
  }
}
</script>
@endpush

@endsection
