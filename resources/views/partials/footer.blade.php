<footer class="bg-dark text-white">
    <div class="container mx-auto px-4 py-12">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            <!-- Kolom 1 -->
            <div>
                <div class="flex items-center gap-2 mb-4">
                    <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center">
                        <i class="fas fa-hospital text-white"></i>
                    </div>
                    <div class="font-playfair font-bold text-lg">{{ $settings['nama_rs'] ?? 'RSUD Landak' }}</div>
                </div>
                <p class="text-gray-300 text-sm mb-4">{{ $settings['tagline'] ?? 'Melayani dengan Sepenuh Hati' }}</p>
                <p class="text-gray-400 text-sm mb-4"><i class="fas fa-map-marker-alt text-gold mr-2"></i>{{ $settings['alamat'] ?? '' }}</p>
                <div class="flex gap-3">
                    @if(!empty($settings['facebook_url']))
                    <a href="{{ $settings['facebook_url'] }}" target="_blank" class="w-8 h-8 bg-primary rounded-full flex items-center justify-center hover:bg-light transition-colors"><i class="fab fa-facebook-f text-xs"></i></a>
                    @endif
                    @if(!empty($settings['instagram_url']))
                    <a href="{{ $settings['instagram_url'] }}" target="_blank" class="w-8 h-8 bg-primary rounded-full flex items-center justify-center hover:bg-light transition-colors"><i class="fab fa-instagram text-xs"></i></a>
                    @endif
                    @if(!empty($settings['youtube_url']))
                    <a href="{{ $settings['youtube_url'] }}" target="_blank" class="w-8 h-8 bg-primary rounded-full flex items-center justify-center hover:bg-light transition-colors"><i class="fab fa-youtube text-xs"></i></a>
                    @endif
                </div>
            </div>

            <!-- Kolom 2 -->
            <div>
                <h4 class="font-semibold text-gold mb-4">Layanan Kami</h4>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a href="#layanan" class="hover:text-white transition-colors">IGD 24 Jam</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Rawat Jalan</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Rawat Inap</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Laboratorium</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Radiologi</a></li>
                    <li><a href="#layanan" class="hover:text-white transition-colors">Farmasi</a></li>
                </ul>
            </div>

            <!-- Kolom 3 -->
            <div>
                <h4 class="font-semibold text-gold mb-4">Informasi</h4>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li><a href="#berita" class="hover:text-white transition-colors">Berita & Pengumuman</a></li>
                    <li><a href="#dokter" class="hover:text-white transition-colors">Jadwal Dokter</a></li>
                    <li><a href="#fasilitas" class="hover:text-white transition-colors">Fasilitas</a></li>
                    <li><a href="#kontak" class="hover:text-white transition-colors">Hubungi Kami</a></li>
                </ul>
            </div>

            <!-- Kolom 4 -->
            <div>
                <h4 class="font-semibold text-gold mb-4">Jam Operasional</h4>
                <ul class="space-y-2 text-sm text-gray-300">
                    <li class="flex items-start gap-2">
                        <i class="fas fa-ambulance text-gold mt-0.5"></i>
                        <div><div class="font-medium text-white">IGD</div><div>{{ $settings['jam_igd'] ?? '24 Jam' }}</div></div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-stethoscope text-gold mt-0.5"></i>
                        <div><div class="font-medium text-white">Rawat Jalan</div><div>{{ $settings['jam_rajal'] ?? '' }}</div></div>
                    </li>
                    <li class="flex items-start gap-2">
                        <i class="fas fa-pills text-gold mt-0.5"></i>
                        <div><div class="font-medium text-white">Apotek</div><div>{{ $settings['jam_apotek'] ?? '' }}</div></div>
                    </li>
                </ul>
            </div>
        </div>
    </div>

    <div class="border-t border-gray-700 py-4">
        <div class="container mx-auto px-4 text-center text-sm text-gray-400">
            &copy; {{ date('Y') }} {{ $settings['nama_rs'] ?? 'RSUD Landak' }}. Hak cipta dilindungi.
        </div>
    </div>
</footer>
