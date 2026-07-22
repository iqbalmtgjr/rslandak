<nav id="navbar" class="bg-white sticky top-0 z-40 transition-shadow duration-300" x-data="{ open: false }">
    <div class="container mx-auto px-4">
        <div class="flex items-center justify-between py-3">
            <!-- Logo -->
            <a href="{{ route('home') }}" class="flex items-center gap-3">
                @if (!empty($settings['logo']))
                    <img src="{{ Storage::url($settings['logo']) }}" alt="Logo" class="h-12">
                @else
                    <div class="w-12 h-12 rounded-full bg-primary flex items-center justify-center">
                        <i class="fas fa-hospital text-white text-xl"></i>
                    </div>
                @endif
                <div>
                    <div class="font-playfair font-bold text-primary text-lg leading-tight">
                        {{ $settings['nama_rs'] ?? 'RSUD Landak' }}</div>
                    <div class="text-xs text-gray-500">{{ $settings['tagline'] ?? 'Melayani dengan Sepenuh Hati' }}
                    </div>
                </div>
            </a>

            <!-- Desktop Menu -->
            <div class="hidden md:flex items-center gap-5 text-sm font-medium">
                <a href="{{ route('home') }}" class="text-gray-700 hover:text-primary transition-colors">Beranda</a>

                {{-- Dropdown Profil RS --}}
                <div class="relative" x-data="{ dropOpen: false }">
                    <button @click="dropOpen = !dropOpen" @click.away="dropOpen = false"
                        class="flex items-center gap-1 transition-colors {{ request()->routeIs('profil.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">
                        Profil RS
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="dropOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="dropOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                        <a href="{{ route('profil.visi-misi') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('profil.visi-misi') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-eye w-5 text-primary"></i> Visi & Misi
                        </a>
                        <a href="{{ route('profil.profil-rs') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('profil.profil-rs') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-hospital w-5 text-primary"></i> Profil RS
                        </a>
                        <a href="{{ route('profil.direktur') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('profil.direktur') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-user-tie w-5 text-primary"></i> Direktur RS
                        </a>
                    </div>
                </div>

                {{-- Dropdown Layanan --}}
                <div class="relative" x-data="{ layananOpen: false }">
                    <button @click="layananOpen = !layananOpen" @click.away="layananOpen = false"
                        class="flex items-center gap-1 transition-colors {{ request()->routeIs('layanan.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">
                        Layanan
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="layananOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="layananOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                        <a href="{{ route('layanan.poliklinik.index') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors text-sm {{ request()->routeIs('layanan.poliklinik.*') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-clinic-medical w-5 text-primary"></i> Poliklinik
                        </a>
                        <a href="{{ route('layanan.rawat-inap') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors text-sm {{ request()->routeIs('layanan.rawat-inap') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-bed w-5 text-primary"></i> Rawat Inap
                        </a>
                        <a href="{{ route('layanan.pelayanan-24-jam') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors text-sm {{ request()->routeIs('layanan.pelayanan-24-jam') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-ambulance w-5 text-primary"></i> Pelayanan 24 Jam
                        </a>
                    </div>
                </div>
                {{-- Dropdown Informasi --}}
                <div class="relative" x-data="{ infoOpen: false }">
                    <button @click="infoOpen = !infoOpen" @click.away="infoOpen = false"
                        class="flex items-center gap-1 transition-colors {{ request()->routeIs('berita.*') || request()->routeIs('leaflet.*') || request()->routeIs('informasi.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">
                        Informasi
                        <i class="fas fa-chevron-down text-xs transition-transform duration-200"
                            :class="infoOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="infoOpen" x-transition:enter="transition ease-out duration-150"
                        x-transition:enter-start="opacity-0 -translate-y-2"
                        x-transition:enter-end="opacity-100 translate-y-0"
                        class="absolute top-full left-0 mt-2 w-52 bg-white rounded-xl shadow-xl border border-gray-100 py-2 z-50">
                        <a href="{{ route('berita.index') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('berita.*') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-newspaper w-5 text-primary"></i> Berita
                        </a>
                        <a href="{{ route('leaflet.index') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('leaflet.*') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-file-medical w-5 text-primary"></i> Leaflet & Poster
                        </a>
                        <a href="{{ route('informasi.alur-pelayanan') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('informasi.alur-pelayanan') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-sitemap w-5 text-primary"></i> Alur Pelayanan
                        </a>
                        <a href="{{ route('informasi.dokter') }}"
                            class="flex items-center gap-3 px-4 py-3 hover:bg-blue-50 hover:text-primary transition-colors {{ request()->routeIs('informasi.dokter') ? 'text-primary bg-blue-50 font-semibold' : 'text-gray-700' }}">
                            <i class="fas fa-user-md w-5 text-primary"></i> Dokter
                        </a>
                    </div>
                </div>
                <a href="{{ route('download.index') }}"
                    class="transition-colors {{ request()->routeIs('download.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">Download</a>
                <a href="{{ route('home') }}#kontak"
                    class="text-gray-700 hover:text-primary transition-colors">Kontak</a>
                <a href="{{ route('pendaftaran.form') }}"
                    class="bg-gold hover:bg-gold-light text-white px-5 py-2 rounded-full font-semibold transition-colors shadow">
                    <i class="fas fa-clipboard-list mr-1"></i> Daftar Online
                </a>
            </div>

            <!-- Hamburger -->
            <button @click="open = !open" class="md:hidden text-gray-700 focus:outline-none">
                <i class="fas fa-bars text-xl" x-show="!open"></i>
                <i class="fas fa-times text-xl" x-show="open"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div x-show="open" x-transition class="md:hidden py-4 border-t border-gray-100">
            <div class="flex flex-col gap-1 text-sm font-medium">
                <a href="{{ route('home') }}#beranda" @click="open=false"
                    class="text-gray-700 hover:text-primary py-2 px-2 rounded">Beranda</a>

                {{-- Dropdown mobile --}}
                <div x-data="{ subOpen: false }">
                    <button @click="subOpen = !subOpen"
                        class="w-full flex items-center justify-between text-gray-700 hover:text-primary py-2 px-2 rounded {{ request()->routeIs('profil.*') ? 'text-primary font-semibold' : '' }}">
                        <span>Profil RS</span>
                        <i class="fas fa-chevron-down text-xs" :class="subOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="subOpen" class="pl-4 pb-1 space-y-1">
                        <a href="{{ route('profil.visi-misi') }}" @click="open=false"
                            class="block text-gray-600 hover:text-primary py-1.5 px-2 rounded {{ request()->routeIs('profil.visi-misi') ? 'text-primary font-semibold' : '' }}">
                            <i class="fas fa-eye mr-2 text-primary text-xs"></i>Visi & Misi
                        </a>
                        <a href="{{ route('profil.profil-rs') }}" @click="open=false"
                            class="block text-gray-600 hover:text-primary py-1.5 px-2 rounded {{ request()->routeIs('profil.profil-rs') ? 'text-primary font-semibold' : '' }}">
                            <i class="fas fa-hospital mr-2 text-primary text-xs"></i>Profil RS
                        </a>
                        <a href="{{ route('profil.direktur') }}" @click="open=false"
                            class="block text-gray-600 hover:text-primary py-1.5 px-2 rounded {{ request()->routeIs('profil.direktur') ? 'text-primary font-semibold' : '' }}">
                            <i class="fas fa-user-tie mr-2 text-primary text-xs"></i>Direktur RS
                        </a>
                    </div>
                </div>

                {{-- Dropdown mobile Layanan --}}
                <div x-data="{ layananOpen: false }">
                    <button @click="layananOpen = !layananOpen"
                        class="w-full flex items-center justify-between py-2 px-2 rounded {{ request()->routeIs('layanan.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">
                        <span>Layanan</span>
                        <i class="fas fa-chevron-down text-xs" :class="layananOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="layananOpen" class="pl-4 pb-1 space-y-1">
                        <a href="{{ route('layanan.poliklinik.index') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded text-sm {{ request()->routeIs('layanan.poliklinik.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-clinic-medical mr-2 text-primary text-xs"></i>Poliklinik
                        </a>
                        <a href="{{ route('layanan.rawat-inap') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded text-sm {{ request()->routeIs('layanan.rawat-inap') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-bed mr-2 text-primary text-xs"></i>Rawat Inap
                        </a>
                        <a href="{{ route('layanan.pelayanan-24-jam') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded text-sm {{ request()->routeIs('layanan.pelayanan-24-jam') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-ambulance mr-2 text-primary text-xs"></i>Pelayanan 24 Jam
                        </a>
                    </div>
                </div>
                {{-- Dropdown mobile Informasi --}}
                <div x-data="{ infoOpen: false }">
                    <button @click="infoOpen = !infoOpen"
                        class="w-full flex items-center justify-between py-2 px-2 rounded {{ request()->routeIs('berita.*') || request()->routeIs('leaflet.*') || request()->routeIs('informasi.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">
                        <span>Informasi</span>
                        <i class="fas fa-chevron-down text-xs" :class="infoOpen ? 'rotate-180' : ''"></i>
                    </button>
                    <div x-show="infoOpen" class="pl-4 pb-1 space-y-1">
                        <a href="{{ route('berita.index') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded {{ request()->routeIs('berita.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-newspaper mr-2 text-primary text-xs"></i>Berita
                        </a>
                        <a href="{{ route('leaflet.index') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded {{ request()->routeIs('leaflet.*') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-file-medical mr-2 text-primary text-xs"></i>Leaflet & Poster
                        </a>
                        <a href="{{ route('informasi.alur-pelayanan') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded {{ request()->routeIs('informasi.alur-pelayanan') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-sitemap mr-2 text-primary text-xs"></i>Alur Pelayanan
                        </a>
                        <a href="{{ route('informasi.dokter') }}" @click="open=false"
                            class="block py-1.5 px-2 rounded {{ request()->routeIs('informasi.dokter') ? 'text-primary font-semibold' : 'text-gray-600 hover:text-primary' }}">
                            <i class="fas fa-user-md mr-2 text-primary text-xs"></i>Dokter
                        </a>
                    </div>
                </div>
                <a href="{{ route('download.index') }}" @click="open=false"
                    class="py-2 px-2 rounded {{ request()->routeIs('download.*') ? 'text-primary font-semibold' : 'text-gray-700 hover:text-primary' }}">Download</a>
                <a href="{{ route('home') }}#kontak" @click="open=false"
                    class="text-gray-700 hover:text-primary py-2 px-2 rounded">Kontak</a>
                <a href="{{ route('pendaftaran.form') }}"
                    class="bg-gold text-white px-4 py-2 rounded-lg font-semibold text-center mt-2">
                    <i class="fas fa-clipboard-list mr-1"></i> Daftar Online
                </a>
            </div>
        </div>
    </div>
</nav>
