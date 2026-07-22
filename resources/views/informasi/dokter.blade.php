@extends('layouts.app')
@section('title', 'Dokter — RSUD Landak')
@section('content')

@include('partials.page-header', ['judul' => 'Dokter', 'parent' => 'Informasi'])

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl">

    @if($dokters->isEmpty())
      <div class="text-center py-24 text-gray-400">
        <i class="fas fa-user-md text-6xl mb-4 block"></i>
        <p class="text-lg">Data dokter sedang diperbarui.</p>
      </div>
    @else

      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($dokters as $dokter)
        <div class="bg-white border border-gray-100 rounded-xl p-6 shadow hover:shadow-lg transition-shadow reveal">

          {{-- Foto / Inisial --}}
          <div class="flex flex-col items-center text-center mb-4">
            @if($dokter->foto)
              <img src="{{ Storage::url($dokter->foto) }}" alt="{{ $dokter->nama }}"
                   class="w-28 h-28 rounded-full object-cover border-4 border-primary/20 mb-3">
            @else
              <div class="w-28 h-28 rounded-full flex items-center justify-center text-white text-3xl font-bold mb-3"
                   style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                {{ strtoupper(substr(trim($dokter->nama), 0, 1)) }}
              </div>
            @endif
            <h3 class="font-semibold text-gray-800 text-lg">{{ $dokter->nama }}</h3>
            <span class="text-xs bg-gold/10 text-gold border border-gold/30 px-3 py-1 rounded-full mt-1">
              {{ $dokter->spesialisasi }}
            </span>
          </div>

          {{-- Bio --}}
          @if($dokter->bio)
          <p class="text-sm text-gray-500 text-center mb-4 leading-relaxed">{{ Str::limit($dokter->bio, 120) }}</p>
          @endif

          {{-- Jadwal --}}
          @if($dokter->jadwal && count($dokter->jadwal) > 0)
          <div class="border-t border-gray-100 pt-4">
            <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-2 flex items-center gap-1">
              <i class="fas fa-calendar-alt text-primary"></i> Jadwal Praktik
            </p>
            <div class="space-y-1">
              @foreach($dokter->jadwal as $jadwal)
              <div class="flex justify-between text-sm bg-gray-50 rounded-lg px-3 py-1.5">
                <span class="text-gray-600 font-medium">{{ $jadwal['hari'] }}</span>
                <span class="text-primary font-semibold">{{ $jadwal['jam'] }}</span>
              </div>
              @endforeach
            </div>
          </div>
          @endif

        </div>
        @endforeach
      </div>

    @endif

  </div>
</section>

@endsection
