<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - RSUD Landak</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563EB',
                        dark:    '#1E3A8A',
                        light:   '#60A5FA',
                        gold:    '#D97706',
                    }
                }
            }
        }
    </script>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Source+Sans+3:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .sidebar-link { display: flex; align-items: center; gap: 10px; padding: 10px 16px; border-radius: 8px; transition: all 0.2s; color: #d1fae5; font-size: 14px; }
        .sidebar-link:hover { background: rgba(201,168,76,0.2); color: #fff; }
        .sidebar-link.active { background: #D97706; color: #fff; font-weight: 600; }
        .sidebar-label { font-size: 10px; text-transform: uppercase; letter-spacing: 1px; color: #6ee7b7; padding: 8px 16px 4px; }
    </style>
</head>
<body class="bg-gray-100 min-h-screen flex">

    <!-- Sidebar -->
    <aside id="sidebar" class="fixed inset-y-0 left-0 z-30 w-64 flex flex-col transition-transform duration-300 lg:translate-x-0 -translate-x-full" style="background: #1E3A8A">
        <!-- Logo -->
        <div class="p-4 border-b border-green-800">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-primary flex items-center justify-center flex-shrink-0" style="background: #2563EB">
                    <i class="fas fa-hospital text-white"></i>
                </div>
                <div>
                    <div style="font-family: 'Plus Jakarta Sans', sans-serif; color: white; font-weight: 700; font-size: 14px; line-height: 1.2">RSUD Landak</div>
                    <div style="color: #6ee7b7; font-size: 10px;">Admin Panel</div>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="flex-1 p-3 overflow-y-auto">
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="fas fa-tachometer-alt w-4"></i> Dashboard
            </a>

            <div class="sidebar-label mt-3">Konten</div>
            <a href="{{ route('admin.profil.index') }}" class="sidebar-link {{ request()->routeIs('admin.profil.*') ? 'active' : '' }}">
                <i class="fas fa-id-card w-4"></i> Profil RS
            </a>
            <a href="{{ route('admin.hero.index') }}" class="sidebar-link {{ request()->routeIs('admin.hero.*') ? 'active' : '' }}">
                <i class="fas fa-images w-4"></i> Hero / Slider
            </a>
            <a href="{{ route('admin.layanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.layanan.*') ? 'active' : '' }}">
                <i class="fas fa-hand-holding-medical w-4"></i> Layanan
            </a>
            <a href="{{ route('admin.dokter.index') }}" class="sidebar-link {{ request()->routeIs('admin.dokter.*') ? 'active' : '' }}">
                <i class="fas fa-user-md w-4"></i> Dokter
            </a>
            <a href="{{ route('admin.berita.index') }}" class="sidebar-link {{ request()->routeIs('admin.berita.*') ? 'active' : '' }}">
                <i class="fas fa-newspaper w-4"></i> Berita
            </a>
            <a href="{{ route('admin.kamar.index') }}" class="sidebar-link {{ request()->routeIs('admin.kamar.*') ? 'active' : '' }}">
                <i class="fas fa-bed w-4"></i> Kamar
            </a>
            <a href="{{ route('admin.poliklinik.index') }}" class="sidebar-link {{ request()->routeIs('admin.poliklinik.*') ? 'active' : '' }}">
                <i class="fas fa-clinic-medical w-4"></i> Poliklinik
            </a>
            <a href="{{ route('admin.pelayanan24jam.index') }}" class="sidebar-link {{ request()->routeIs('admin.pelayanan24jam.*') ? 'active' : '' }}">
                <i class="fas fa-clock w-4"></i> Pelayanan 24 Jam
            </a>
            <a href="{{ route('admin.alur-pelayanan.index') }}" class="sidebar-link {{ request()->routeIs('admin.alur-pelayanan.*') ? 'active' : '' }}">
                <i class="fas fa-sitemap w-4"></i> Alur Pelayanan
            </a>
            <a href="{{ route('admin.download.index') }}" class="sidebar-link {{ request()->routeIs('admin.download.*') ? 'active' : '' }}">
                <i class="fas fa-download w-4"></i> Download
            </a>
            <a href="{{ route('admin.leaflet.index') }}" class="sidebar-link {{ request()->routeIs('admin.leaflet.*') ? 'active' : '' }}">
                <i class="fas fa-file-medical w-4"></i> Leaflet & Poster
            </a>

            <a href="{{ route('admin.pendaftaran.index') }}" class="sidebar-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
                <i class="fas fa-clipboard-list w-4"></i>
                <span>Pendaftaran Online</span>
                @php $menunggu = \App\Models\Pendaftaran::where('status','Menunggu')->count(); @endphp
                @if($menunggu > 0)
                  <span class="ml-auto bg-red-500 text-white text-xs rounded-full w-5 h-5 flex items-center justify-center flex-shrink-0">
                    {{ $menunggu > 9 ? '9+' : $menunggu }}
                  </span>
                @endif
            </a>

            <div class="sidebar-label mt-3">Pengaturan</div>
            <a href="{{ route('admin.setting.index') }}" class="sidebar-link {{ request()->routeIs('admin.setting.*') ? 'active' : '' }}">
                <i class="fas fa-cog w-4"></i> Pengaturan Situs
            </a>

            <div class="mt-4 border-t border-green-800 pt-3">
                <a href="{{ route('home') }}" target="_blank" class="sidebar-link">
                    <i class="fas fa-external-link-alt w-4"></i> Lihat Website
                </a>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="sidebar-link w-full text-left text-red-300 hover:text-red-200">
                        <i class="fas fa-sign-out-alt w-4"></i> Logout
                    </button>
                </form>
            </div>
        </nav>
    </aside>

    <!-- Main -->
    <div class="flex-1 flex flex-col min-h-screen lg:ml-64">
        <!-- Topbar -->
        <header class="bg-white shadow-sm sticky top-0 z-20 px-4 py-3 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <button id="sidebar-toggle" class="lg:hidden text-gray-600">
                    <i class="fas fa-bars text-xl"></i>
                </button>
                <div class="text-sm text-gray-500">
                    <a href="{{ route('admin.dashboard') }}" class="hover:text-green-700">Dashboard</a>
                    @yield('breadcrumb')
                </div>
            </div>
            <div class="flex items-center gap-3 text-sm">
                <span class="text-gray-600"><i class="fas fa-user-circle mr-1"></i>{{ Auth::user()->name }}</span>
            </div>
        </header>

        <!-- Content -->
        <main class="flex-1 p-6">
            @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
            </div>
            @endif
            @if(session('error'))
            <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-4 flex items-center gap-2">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
            </div>
            @endif

            @yield('content')
        </main>
    </div>

    <!-- Overlay mobile -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black/40 z-20 hidden lg:hidden"></div>

    <script>
        const toggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const overlay = document.getElementById('sidebar-overlay');
        toggle.addEventListener('click', () => {
            sidebar.classList.toggle('-translate-x-full');
            overlay.classList.toggle('hidden');
        });
        overlay.addEventListener('click', () => {
            sidebar.classList.add('-translate-x-full');
            overlay.classList.add('hidden');
        });
    </script>
    @yield('scripts')
</body>
</html>
