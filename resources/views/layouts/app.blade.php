<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', $settings['nama_rs'] ?? 'RSUD Landak')</title>
    <meta name="description" content="@yield('meta_description', $settings['meta_description'] ?? '')">
    <link rel="icon" href="{{ $settings['favicon'] ? Storage::url($settings['favicon']) : '/favicon.ico' }}">

    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@500;600;700;800&family=Source+Sans+3:wght@300;400;600&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        primary: '#2563EB',
                        dark: '#1E3A8A',
                        light: '#60A5FA',
                        gold: '#D97706',
                        'gold-light': '#F59E0B',
                    },
                    fontFamily: {
                        playfair: ['"Plus Jakarta Sans"', 'sans-serif'],
                        sans: ['"Source Sans 3"', 'sans-serif'],
                    }
                }
            }
        }
    </script>

    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">

    <style>
        body { font-family: 'Source Sans 3', sans-serif; }
        .font-playfair { font-family: 'Plus Jakarta Sans', sans-serif; }
        .reveal { opacity: 0; transform: translateY(30px); transition: opacity 0.6s ease, transform 0.6s ease; }
        .reveal.visible { opacity: 1; transform: translateY(0); }

        /* Foto Direktur */
        .direktur-frame {
            position: relative;
            border-radius: 12px;
            overflow: hidden;
            aspect-ratio: 3/4;
        }
        .direktur-frame::before {
            content: '';
            position: absolute;
            inset: 0;
            background: linear-gradient(to bottom, transparent 60%, rgba(30,58,138,0.8));
            z-index: 1;
        }

        /* Dekoratif kotak pojok foto */
        .photo-deco {
            position: relative;
        }
        .photo-deco::before {
            content: '';
            position: absolute;
            width: 60px;
            height: 60px;
            background: #D97706;
            top: -15px;
            left: -15px;
            z-index: -1;
            border-radius: 4px;
        }
        .photo-deco::after {
            content: '';
            position: absolute;
            width: 40px;
            height: 40px;
            background: #2563EB;
            bottom: -10px;
            right: -10px;
            z-index: -1;
            border-radius: 4px;
        }

        /* line-clamp fallback */
        .line-clamp-2 { display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden; }
        .line-clamp-3 { display: -webkit-box; -webkit-line-clamp: 3; -webkit-box-orient: vertical; overflow: hidden; }

        /* Article content styling */
        .article-content p    { margin-bottom: 1rem; line-height: 1.8; }
        .article-content h2   { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.4rem; font-weight: 700; color: #1E3A8A; margin: 1.5rem 0 0.75rem; }
        .article-content h3   { font-family: 'Plus Jakarta Sans', sans-serif; font-size: 1.2rem; font-weight: 600; color: #2563EB; margin: 1.25rem 0 0.5rem; }
        .article-content ul,
        .article-content ol   { padding-left: 1.5rem; margin-bottom: 1rem; }
        .article-content li   { margin-bottom: 0.4rem; line-height: 1.7; }
        .article-content img  { border-radius: 12px; max-width: 100%; height: auto; margin: 1rem 0; }
        .article-content a    { color: #2563EB; text-decoration: underline; }
        .article-content blockquote { border-left: 4px solid #D97706; padding: 0.75rem 1rem; margin: 1rem 0; background: #F8F9FA; font-style: italic; color: #555; border-radius: 0 8px 8px 0; }

        /* Pagination */
        .pagination-btn { display: inline-flex; align-items: center; justify-content: center; min-width: 36px; height: 36px; padding: 0 8px; border: 1px solid #D1D5DB; border-radius: 8px; font-size: 0.875rem; color: #4B5563; transition: all 0.2s; text-decoration: none; }
        .pagination-btn:hover  { background: #2563EB; color: white; border-color: #2563EB; }
        .pagination-btn.active { background: #2563EB; color: white; border-color: #2563EB; font-weight: 600; }

        @yield('styles')
    </style>
</head>
<body class="bg-white text-gray-800">

    @include('partials.topbar')
    @include('partials.navbar')

    @yield('content')

    @include('partials.footer')

    <!-- Back to top -->
    <button id="back-to-top" class="hidden fixed bottom-6 right-6 bg-primary text-white w-12 h-12 rounded-full shadow-lg z-50 hover:bg-dark transition-colors flex items-center justify-center">
        <i class="fas fa-chevron-up"></i>
    </button>

    <script>
        // Sticky navbar
        window.addEventListener('scroll', () => {
            document.getElementById('navbar').classList.toggle('shadow-lg', window.scrollY > 50);
        });

        // Back to top
        const btn = document.getElementById('back-to-top');
        window.addEventListener('scroll', () => btn.classList.toggle('hidden', window.scrollY < 300));
        btn.addEventListener('click', () => window.scrollTo({ top: 0, behavior: 'smooth' }));

        // Scroll reveal
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(e => {
                if (e.isIntersecting) {
                    e.target.classList.add('visible');
                    observer.unobserve(e.target);
                }
            });
        }, { threshold: 0.1 });
        document.querySelectorAll('.reveal').forEach(el => observer.observe(el));

        // Counter animasi
        const counterObserver = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    const el = entry.target;
                    const target = parseInt(el.dataset.target);
                    let current = 0;
                    const step = Math.ceil(target / 60);
                    const timer = setInterval(() => {
                        current = Math.min(current + step, target);
                        el.textContent = current.toLocaleString('id-ID') + (el.dataset.suffix || '');
                        if (current >= target) clearInterval(timer);
                    }, 30);
                    counterObserver.unobserve(el);
                }
            });
        });
        document.querySelectorAll('.counter').forEach(el => counterObserver.observe(el));
    </script>

    @yield('scripts')
    @stack('scripts')
</body>
</html>
