@extends('layouts.app')

@section('title', ($settings['nama_rs'] ?? 'RSUD Landak') . ' - ' . ($settings['tagline'] ?? ''))

@section('content')

{{-- HERO SECTION --}}
<section id="beranda" class="relative" x-data="{ active: 0, total: {{ $heroes->count() ?: 1 }} }" x-init="setInterval(() => active = (active + 1) % total, 5000)">
    @if($heroes->count() > 0)
        @foreach($heroes as $i => $hero)
        <div x-show="active === {{ $i }}" x-transition:enter="transition-opacity duration-700" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="relative h-screen min-h-[500px] max-h-[700px]">
            @if($hero->gambar)
                <img src="{{ Storage::url($hero->gambar) }}" alt="{{ $hero->judul }}" class="absolute inset-0 w-full h-full object-cover">
            @else
                <div class="absolute inset-0" style="background: linear-gradient(135deg, #1E3A8A, #2563EB, #60A5FA)"></div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-r from-dark/95 via-dark/65 to-primary/25"></div>
            <div class="relative z-10 h-full flex items-center px-4">
                <div class="container mx-auto">
                    <div class="max-w-3xl">
                        <span class="inline-flex items-center gap-2 bg-white/10 backdrop-blur border border-white/25 text-white/90 text-xs md:text-sm px-4 py-1.5 rounded-full mb-6 tracking-widest uppercase">
                            <i class="fas fa-hospital text-gold-light"></i> Rumah Sakit Umum Daerah Kabupaten Landak
                        </span>
                        <h1 class="font-playfair text-4xl md:text-5xl lg:text-6xl font-extrabold text-white mb-5 leading-tight">{{ $hero->judul }}</h1>
                        @if($hero->sub_judul)
                        <p class="text-white/85 text-lg md:text-xl mb-8 max-w-2xl">{{ $hero->sub_judul }}</p>
                        @endif
                        <div class="flex flex-wrap gap-3">
                            @if($hero->tombol_teks)
                            <a href="{{ $hero->tombol_url ?? '#' }}" class="inline-flex items-center gap-2 bg-gold hover:bg-gold-light text-white font-semibold px-8 py-3.5 rounded-full text-lg transition-all shadow-lg hover:shadow-xl hover:-translate-y-0.5">
                                {{ $hero->tombol_teks }} <i class="fas fa-arrow-right text-sm"></i>
                            </a>
                            @endif
                            <a href="#layanan" class="inline-flex items-center gap-2 border-2 border-white/60 text-white hover:bg-white hover:text-dark font-semibold px-8 py-3.5 rounded-full text-lg transition-all">
                                <i class="fas fa-hand-holding-medical text-sm"></i> Layanan Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach

        {{-- Dots --}}
        <div class="absolute bottom-24 left-1/2 -translate-x-1/2 flex gap-2 z-20">
            @foreach($heroes as $i => $hero)
            <button @click="active = {{ $i }}" :class="active === {{ $i }} ? 'bg-gold w-8' : 'bg-white/50 w-3'" class="h-3 rounded-full transition-all duration-300"></button>
            @endforeach
        </div>
    @else
        <div class="h-[600px] flex items-center justify-center" style="background: linear-gradient(135deg, #1E3A8A, #2563EB, #60A5FA)">
            <div class="text-center text-white px-4">
                <h1 class="font-playfair text-5xl font-bold mb-4">{{ $settings['nama_rs'] ?? 'RSUD Landak' }}</h1>
                <p class="text-xl">{{ $settings['tagline'] ?? 'Melayani dengan Sepenuh Hati' }}</p>
            </div>
        </div>
    @endif
</section>

{{-- STATS BAR — kartu mengambang menindih hero --}}
<section class="relative z-20 -mt-16 px-4">
    <div class="container mx-auto">
        <div class="bg-white rounded-3xl shadow-2xl border border-gray-100 grid grid-cols-2 md:grid-cols-4 gap-2 p-4 md:p-6 text-center">
            @foreach([
                ['ikon' => 'fa-calendar-check', 'target' => $settings['stats_tahun_berdiri'] ?? 1970, 'suffix' => '', 'label' => 'Tahun Berdiri'],
                ['ikon' => 'fa-user-nurse', 'target' => $settings['stats_tenaga_medis'] ?? 150, 'suffix' => '+', 'label' => 'Tenaga Medis'],
                ['ikon' => 'fa-bed', 'target' => $settings['stats_kapasitas_tt'] ?? 120, 'suffix' => ' TT', 'label' => 'Kapasitas Tempat Tidur'],
                ['ikon' => 'fa-users', 'target' => $settings['stats_pasien_pertahun'] ?? 25000, 'suffix' => '+', 'label' => 'Pasien Per Tahun'],
            ] as $stat)
            <div class="reveal flex flex-col items-center py-4 px-2 rounded-2xl hover:bg-blue-50/60 transition-colors">
                <div class="w-12 h-12 rounded-full bg-primary/10 text-primary flex items-center justify-center mb-3">
                    <i class="fas {{ $stat['ikon'] }} text-xl"></i>
                </div>
                <div class="text-3xl md:text-4xl font-bold text-dark">
                    <span class="counter" data-target="{{ $stat['target'] }}" data-suffix="{{ $stat['suffix'] }}">0</span>
                </div>
                <div class="text-sm mt-1 text-gray-500">{{ $stat['label'] }}</div>
            </div>
            @endforeach
        </div>
    </div>
</section>


{{-- SAMBUTAN DIREKTUR --}}
<section class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
            <div class="reveal">
                <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Kata Pengantar</span>
                <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Sambutan Direktur</h2>
                <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mb-6"></div>
                <p class="text-gray-600 leading-relaxed mb-6 italic text-lg">"{{ $settings['sambutan_direktur_teks'] ?? '' }}"</p>
                <div>
                    <div class="font-bold text-primary text-lg">{{ $settings['sambutan_direktur_nama'] ?? '' }}</div>
                    <div class="text-gray-500 text-sm">{{ $settings['sambutan_direktur_jabatan'] ?? '' }}</div>
                </div>
            </div>
            <div class="flex justify-center reveal">
                @if(!empty($settings['sambutan_direktur_foto']))
                    <img src="{{ Storage::url($settings['sambutan_direktur_foto']) }}" alt="Direktur" class="w-64 h-80 object-cover rounded-2xl shadow-xl">
                @else
                    <div class="w-64 h-80 rounded-2xl shadow-xl flex items-center justify-center" style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                        <i class="fas fa-user-md text-white text-6xl"></i>
                    </div>
                @endif
            </div>
        </div>
    </div>
</section>

{{-- PROFIL RS --}}
<section id="profil" class="py-16 bg-white">
    <div class="container mx-auto px-4 max-w-6xl">
        <div class="text-center mb-12 reveal">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Profil</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Tentang Kami</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-xl mx-auto">Mengenal lebih jauh RSUD Landak — visi misi, sejarah, dan pimpinan kami.</p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-center mb-12">
            {{-- Kiri: Ringkasan Visi --}}
            <div class="reveal">
                <div class="border-l-4 border-gold pl-5 mb-6">
                    <p class="text-xs font-semibold uppercase tracking-widest text-gold mb-1">Visi</p>
                    <p class="text-gray-700 italic text-lg leading-relaxed">
                        "{{ $settings['profil_visi'] ?? 'Menjadi Rumah Sakit pilihan utama masyarakat Landak yang profesional dan terpercaya.' }}"
                    </p>
                </div>

                @php
                    $misi = json_decode($settings['profil_misi'] ?? '[]', true) ?: [];
                @endphp
                @if(count($misi) > 0)
                <div>
                    <p class="text-xs font-semibold uppercase tracking-widest text-gold mb-3">Misi Utama</p>
                    <ul class="space-y-2">
                        @foreach(array_slice($misi, 0, 3) as $i => $item)
                        <li class="flex gap-3 items-start">
                            <span class="flex-shrink-0 w-6 h-6 rounded-full bg-primary text-white text-xs flex items-center justify-center font-bold mt-0.5">{{ $i + 1 }}</span>
                            <span class="text-gray-600 text-sm leading-relaxed">{{ $item }}</span>
                        </li>
                        @endforeach
                        @if(count($misi) > 3)
                        <li class="text-sm text-primary pl-9">
                            <a href="{{ route('profil.visi-misi') }}" class="hover:underline">+ {{ count($misi) - 3 }} misi lainnya &rarr;</a>
                        </li>
                        @endif
                    </ul>
                </div>
                @endif

                @if(!empty($settings['profil_motto']))
                <div class="mt-6 p-4 rounded-xl border-l-4 border-gold" style="background:#fffbeb">
                    <i class="fas fa-quote-left text-gold/30 text-3xl float-left mr-2 -mt-1"></i>
                    <p class="font-playfair italic text-dark font-semibold">"{{ $settings['profil_motto'] }}"</p>
                </div>
                @endif
            </div>

            {{-- Kanan: 3 Card link --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-1 gap-4 reveal">
                <a href="{{ route('profil.visi-misi') }}"
                   class="flex items-center gap-4 p-5 bg-white border border-gray-100 rounded-xl shadow hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg,#2563EB,#60A5FA)">
                        <i class="fas fa-eye text-white text-lg"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark group-hover:text-primary transition-colors">Visi & Misi</div>
                        <div class="text-sm text-gray-500">Tujuan dan arah pelayanan RS</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                </a>

                <a href="{{ route('profil.profil-rs') }}"
                   class="flex items-center gap-4 p-5 bg-white border border-gray-100 rounded-xl shadow hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg,#D97706,#F59E0B)">
                        <i class="fas fa-hospital text-white text-lg"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark group-hover:text-primary transition-colors">Profil Rumah Sakit</div>
                        <div class="text-sm text-gray-500">Sejarah, nilai, dan akreditasi</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                </a>

                <a href="{{ route('profil.direktur') }}"
                   class="flex items-center gap-4 p-5 bg-white border border-gray-100 rounded-xl shadow hover:shadow-lg hover:-translate-y-0.5 transition-all group">
                    <div class="w-12 h-12 rounded-xl flex items-center justify-center flex-shrink-0 group-hover:scale-110 transition-transform" style="background: linear-gradient(135deg,#1E3A8A,#2563EB)">
                        <i class="fas fa-user-tie text-white text-lg"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark group-hover:text-primary transition-colors">Direktur RS</div>
                        <div class="text-sm text-gray-500">{{ Str::limit($settings['direktur_nama'] ?? 'Pimpinan rumah sakit', 35) }}</div>
                    </div>
                    <i class="fas fa-chevron-right text-gray-300 group-hover:text-primary ml-auto transition-colors"></i>
                </a>
            </div>
        </div>
    </div>
</section>


{{-- LAYANAN --}}
<section id="layanan" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 reveal">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Pelayanan</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Layanan Kami</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-xl mx-auto">Kami menyediakan berbagai layanan kesehatan komprehensif untuk memenuhi kebutuhan medis Anda dan keluarga.</p>
        </div>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
            @foreach($layanans as $layanan)
            <div class="group bg-white rounded-2xl p-6 text-center shadow hover:shadow-xl hover:-translate-y-1 transition-all duration-300 reveal border border-gray-100">
                @if($layanan->gambar)
                <img src="{{ Storage::url($layanan->gambar) }}" alt="{{ $layanan->nama }}" class="w-full h-32 object-cover rounded-lg mb-4">
                @endif
                <div class="w-16 h-16 mx-auto rounded-2xl bg-primary/10 text-primary flex items-center justify-center text-2xl mb-4 group-hover:bg-primary group-hover:text-white group-hover:scale-110 transition-all duration-300"><i class="fas {{ $layanan->ikon }}"></i></div>
                <h3 class="font-semibold text-gray-800 mb-2">{{ $layanan->nama }}</h3>
                <p class="text-sm text-gray-500 leading-relaxed">{{ Str::limit($layanan->deskripsi, 80) }}</p>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- JADWAL DOKTER --}}
<section id="dokter" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 reveal">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Tim Medis</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Dokter Kami</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-xl mx-auto">Tim dokter spesialis berpengalaman siap memberikan pelayanan terbaik untuk kesehatan Anda.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($dokters as $dokter)
            <div class="bg-white border border-gray-100 rounded-xl p-6 shadow hover:shadow-lg transition-shadow reveal">
                <div class="flex flex-col items-center text-center mb-4">
                    @if($dokter->foto)
                        <img src="{{ Storage::url($dokter->foto) }}" alt="{{ $dokter->nama }}"
                             class="w-24 h-24 rounded-full object-cover border-4 border-primary/20 mb-3">
                    @else
                        <div class="w-24 h-24 rounded-full flex items-center justify-center text-white text-2xl font-bold mb-3"
                             style="background: linear-gradient(135deg, #2563EB, #60A5FA)">
                            {{ strtoupper(substr(trim($dokter->nama), 0, 1)) }}
                        </div>
                    @endif
                    <h3 class="font-semibold text-gray-800">{{ $dokter->nama }}</h3>
                    <span class="text-xs bg-gold/10 text-gold border border-gold/30 px-3 py-1 rounded-full mt-1">
                        {{ $dokter->spesialisasi }}
                    </span>
                </div>
                @if($dokter->jadwal)
                <div class="space-y-1">
                    @foreach($dokter->jadwal as $jadwal)
                    <div class="flex justify-between text-sm bg-gray-50 rounded px-3 py-1.5">
                        <span class="text-gray-600 font-medium">{{ $jadwal['hari'] }}</span>
                        <span class="text-primary">{{ $jadwal['jam'] }}</span>
                    </div>
                    @endforeach
                </div>
                @endif
            </div>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('informasi.dokter') }}" class="inline-block border-2 border-primary text-primary hover:bg-primary hover:text-white px-7 py-2.5 rounded-full font-semibold transition-colors">
                Lihat Semua Dokter
            </a>
        </div>
    </div>
</section>

{{-- KAMAR RAWAT INAP --}}
<section id="fasilitas" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 reveal">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Fasilitas</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Kamar Rawat Inap</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-xl mx-auto">Pilihan kamar rawat inap yang nyaman dengan berbagai fasilitas untuk mendukung proses pemulihan Anda.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
            @foreach($kamars as $kamar)
            <div class="bg-white rounded-xl overflow-hidden shadow hover:shadow-xl transition-shadow reveal">
                @if($kamar->gambar)
                    <img src="{{ Storage::url($kamar->gambar) }}" alt="{{ $kamar->nama }}" class="w-full h-40 object-cover">
                @else
                    <div class="h-40" style="background: linear-gradient(135deg, #2563EB, #60A5FA)"></div>
                @endif
                <div class="p-5">
                    @if($kamar->badge)
                    <span class="text-xs bg-gold text-white px-2 py-0.5 rounded font-semibold">{{ $kamar->badge }}</span>
                    @endif
                    <h3 class="font-playfair font-bold text-xl text-dark mt-2 mb-2">{{ $kamar->nama }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ Str::limit($kamar->deskripsi, 80) }}</p>
                    @if($kamar->fasilitas)
                    <ul class="space-y-1">
                        @foreach(array_slice($kamar->fasilitas, 0, 4) as $fas)
                        <li class="text-sm text-gray-600 flex items-center gap-2">
                            <i class="fas fa-check text-primary text-xs"></i> {{ $fas }}
                        </li>
                        @endforeach
                        @if(count($kamar->fasilitas) > 4)
                        <li class="text-xs text-gray-400">+ {{ count($kamar->fasilitas) - 4 }} fasilitas lainnya</li>
                        @endif
                    </ul>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>
</section>

{{-- BERITA TERBARU --}}
<section id="berita" class="py-16 bg-gray-50">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 reveal">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Informasi</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Berita Terbaru</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto mb-4"></div>
            <p class="text-gray-600 max-w-xl mx-auto">Informasi terkini seputar kegiatan, pengumuman, dan berita dari RSUD Landak.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($beritas as $berita)
            <a href="{{ route('berita.show', $berita->slug) }}"
               class="bg-white rounded-xl overflow-hidden shadow hover:shadow-xl transition-all hover:-translate-y-1 reveal block group">
                @if($berita->gambar)
                    <img src="{{ Storage::url($berita->gambar) }}" alt="{{ $berita->judul }}"
                         class="w-full h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                @else
                    <div class="h-48" style="background: linear-gradient(135deg, #1E3A8A, #2563EB)"></div>
                @endif
                <div class="p-5">
                    <span class="text-xs px-2 py-0.5 rounded font-semibold
                        @if($berita->kategori === 'Berita') bg-blue-100 text-blue-700
                        @elseif($berita->kategori === 'Pengumuman') bg-yellow-100 text-yellow-700
                        @else bg-green-100 text-green-700 @endif">
                        {{ $berita->kategori }}
                    </span>
                    <h3 class="font-semibold text-gray-800 mt-2 mb-2 line-clamp-2 group-hover:text-primary transition-colors">{{ $berita->judul }}</h3>
                    <p class="text-sm text-gray-500 mb-3">{{ Str::limit(strip_tags($berita->konten), 100) }}</p>
                    <div class="flex justify-between items-center text-xs text-gray-400">
                        <span><i class="fas fa-calendar mr-1"></i>{{ $berita->created_at->format('d M Y') }}</span>
                        <span class="text-primary font-medium group-hover:text-dark">Baca &rarr;</span>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
        <div class="text-center mt-8">
            <a href="{{ route('berita.index') }}" class="inline-block border-2 border-primary text-primary hover:bg-primary hover:text-white px-7 py-2.5 rounded-full font-semibold transition-colors">Lihat Semua Berita</a>
        </div>
    </div>
</section>

{{-- CTA --}}
<section class="relative overflow-hidden py-16 bg-gradient-to-r from-dark to-primary text-white text-center">
    <div class="absolute -top-20 -left-20 w-72 h-72 bg-white/5 rounded-full"></div>
    <div class="absolute -bottom-24 -right-16 w-96 h-96 bg-white/5 rounded-full"></div>
    <div class="absolute top-8 right-1/4 w-20 h-20 bg-gold/20 rounded-full blur-xl"></div>
    <div class="relative container mx-auto px-4 reveal">
        <h2 class="font-playfair text-3xl md:text-4xl font-bold mb-4">Butuh Bantuan Medis Segera?</h2>
        <p class="text-white/80 mb-8 max-w-xl mx-auto">Tim medis kami siap melayani Anda 24 jam. Hubungi kami atau kunjungi IGD kami sekarang.</p>
        <div class="flex flex-wrap justify-center gap-4">
            <a href="tel:{{ $settings['telepon'] ?? '' }}" class="bg-gold hover:bg-gold-light text-white px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                <i class="fas fa-phone mr-2"></i> {{ $settings['telepon'] ?? 'Hubungi Kami' }}
            </a>
            <a href="#kontak" class="border-2 border-white text-white hover:bg-white hover:text-primary px-8 py-3 rounded-lg font-semibold text-lg transition-colors">
                Lokasi Kami
            </a>
        </div>
    </div>
</section>

{{-- KONTAK & PETA --}}
<section id="kontak" class="py-16 bg-white">
    <div class="container mx-auto px-4">
        <div class="text-center mb-12 reveal">
            <span class="inline-block bg-primary/10 text-primary text-xs font-bold tracking-widest uppercase px-4 py-1.5 rounded-full mb-3">Kontak</span>
            <h2 class="font-playfair text-3xl md:text-4xl font-bold text-dark mb-3">Hubungi Kami</h2>
            <div class="w-20 h-1.5 rounded-full bg-gradient-to-r from-primary to-light mx-auto"></div>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <div class="space-y-4 reveal">
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-map-marker-alt text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark">Alamat</div>
                        <div class="text-gray-600 text-sm">{{ $settings['alamat'] ?? '' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-phone text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark">Telepon</div>
                        <div class="text-gray-600 text-sm">{{ $settings['telepon'] ?? '' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-envelope text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark">Email</div>
                        <div class="text-gray-600 text-sm">{{ $settings['email'] ?? '' }}</div>
                    </div>
                </div>
                <div class="flex items-start gap-4 p-4 bg-gray-50 rounded-xl">
                    <div class="w-10 h-10 bg-primary rounded-full flex items-center justify-center flex-shrink-0">
                        <i class="fas fa-clock text-white"></i>
                    </div>
                    <div>
                        <div class="font-semibold text-dark">Jam Layanan</div>
                        <div class="text-gray-600 text-sm">
                            IGD: {{ $settings['jam_igd'] ?? '24 Jam' }}<br>
                            Rajal: {{ $settings['jam_rajal'] ?? '' }}<br>
                            Apotek: {{ $settings['jam_apotek'] ?? '' }}
                        </div>
                    </div>
                </div>
            </div>
            <div class="reveal">
                <iframe
                    src="https://maps.google.com/maps?q=RSUD+Landak+Ngabang+Kalimantan+Barat&output=embed"
                    width="100%" height="400" style="border:0; border-radius:12px;" allowfullscreen loading="lazy">
                </iframe>
            </div>
        </div>
    </div>
</section>

@endsection
