@extends('layouts.app')
@section('title', 'Alur Pelayanan — RSUD Landak')
@section('content')

@include('partials.page-header', ['judul' => 'Alur Pelayanan', 'parent' => 'Informasi'])

<section class="py-12 bg-white min-h-screen">
  <div class="container mx-auto px-4 max-w-5xl">

    @if($alurs->isEmpty())
      <div class="text-center py-24 text-gray-400">
        <i class="fas fa-sitemap text-6xl mb-4 block"></i>
        <p class="text-lg">Informasi alur pelayanan sedang diperbarui.</p>
      </div>
    @else

      <div class="space-y-14">
        @foreach($alurs as $alur)
          <div class="reveal">

            {{-- Sub-judul dengan border kiri hijau --}}
            <div class="flex items-center gap-3 mb-5">
              <div class="w-1 h-7 bg-green-700 rounded-full flex-shrink-0"></div>
              <h2 class="font-playfair text-xl font-semibold text-gray-800">
                {{ $alur->judul }}
              </h2>
            </div>

            {{-- Gambar alur — klik untuk lightbox --}}
            @if($alur->gambar_url)
              <div class="alur-img-wrap rounded-2xl overflow-hidden shadow-md border border-gray-100 cursor-zoom-in"
                   @click="$dispatch('open-lightbox', { src: '{{ $alur->gambar_url }}', caption: '{{ addslashes($alur->judul) }}' })"
                   x-data>
                <img src="{{ $alur->gambar_url }}"
                     alt="{{ $alur->judul }}"
                     class="w-full h-auto object-contain hover:scale-[1.01] transition-transform duration-300">
              </div>

              <p class="mt-2 text-xs text-gray-400 flex items-center gap-1">
                <i class="fas fa-search-plus"></i>
                Klik gambar untuk memperbesar
              </p>
            @endif

            @if($alur->keterangan)
              <p class="mt-3 text-sm text-gray-500 italic">{{ $alur->keterangan }}</p>
            @endif

          </div>
        @endforeach
      </div>

    @endif

  </div>
</section>

{{-- Lightbox Overlay --}}
<div x-data="{
       open: false,
       src: '',
       caption: '',
       init() {
         window.addEventListener('open-lightbox', (e) => {
           this.src     = e.detail.src;
           this.caption = e.detail.caption;
           this.open    = true;
           document.body.classList.add('overflow-hidden');
         });
       },
       close() {
         this.open = false;
         document.body.classList.remove('overflow-hidden');
       }
     }"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="close()"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display:none; background:rgba(0,0,0,0.85);">

  <div class="absolute inset-0" @click="close()"></div>

  <div class="relative z-10 max-w-5xl w-full mx-auto">
    <button @click="close()"
            class="absolute -top-10 right-0 text-white hover:text-gray-300 transition-colors text-sm flex items-center gap-2">
      <i class="fas fa-times text-lg"></i>
      <span>Tutup (ESC)</span>
    </button>

    <p class="text-white text-sm font-medium mb-3 text-center opacity-80" x-text="caption"></p>

    <div class="rounded-2xl overflow-hidden shadow-2xl">
      <img :src="src" :alt="caption"
           class="w-full h-auto max-h-[80vh] object-contain bg-white">
    </div>

    <div class="text-center mt-3">
      <a :href="src" download
         class="inline-flex items-center gap-2 text-white text-xs opacity-60 hover:opacity-100 transition-opacity">
        <i class="fas fa-download"></i> Simpan Gambar
      </a>
    </div>
  </div>
</div>

@push('scripts')
<style>
.cursor-zoom-in { cursor: zoom-in; }
.alur-img-wrap { position: relative; }
.alur-img-wrap::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, #2563EB, #60A5FA);
  border-radius: 0 0 16px 16px;
  opacity: 0;
  transition: opacity 0.3s;
}
.alur-img-wrap:hover::after { opacity: 1; }
</style>
@endpush

@endsection
