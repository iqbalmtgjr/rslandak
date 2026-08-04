<article class="bg-white rounded-2xl shadow-sm hover:shadow-xl transition-all duration-300 hover:-translate-y-1 overflow-hidden group reveal">

  {{-- Thumbnail --}}
  <a href="{{ route('berita.show', $item->slug) }}" class="block overflow-hidden h-52 relative">
    @if($item->gambar_url)
      <img src="{{ $item->gambar_url }}"
           alt="{{ $item->judul }}"
           class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
    @else
      <div class="w-full h-full flex items-center justify-center
        @if($item->kategori === 'Berita') bg-gradient-to-br from-green-700 to-green-500
        @elseif($item->kategori === 'Pengumuman') bg-gradient-to-br from-yellow-600 to-yellow-400
        @else bg-gradient-to-br from-blue-700 to-blue-500
        @endif">
        <i class="fa fa-newspaper text-5xl text-white opacity-40"></i>
      </div>
    @endif

    <span class="absolute bottom-3 left-3 text-xs font-semibold px-3 py-1 rounded-full
      @if($item->kategori === 'Berita') bg-green-700 text-white
      @elseif($item->kategori === 'Pengumuman') bg-yellow-500 text-white
      @else bg-blue-600 text-white
      @endif">
      {{ $item->kategori }}
    </span>
  </a>

  {{-- Body --}}
  <div class="p-5">
    <p class="text-xs text-gray-400 mb-2">
      <i class="fa fa-calendar-alt mr-1"></i>
      Diposting : {{ \Carbon\Carbon::parse($item->created_at)->format('d-m-Y') }}
    </p>

    <h3 class="font-playfair font-semibold text-gray-800 text-base leading-snug mb-3 group-hover:text-green-700 transition-colors line-clamp-3">
      <a href="{{ route('berita.show', $item->slug) }}">{{ $item->judul }}</a>
    </h3>

    <p class="text-gray-500 text-sm leading-relaxed line-clamp-3 mb-4">
      {{ $item->ringkasan }}
    </p>

    <div class="flex items-center justify-between pt-3 border-t border-gray-100">
      <span class="text-xs text-gray-400">
        <i class="fa fa-user-circle mr-1"></i>{{ $item->penulis }}
      </span>
      <a href="{{ route('berita.show', $item->slug) }}"
         class="text-xs font-semibold text-green-700 hover:text-green-900 transition-colors">
        Baca selengkapnya →
      </a>
    </div>
  </div>

</article>
