<section class="py-10 text-center" style="background: linear-gradient(135deg, #1E3A8A 0%, #2563EB 60%, #60A5FA 100%);">
    <div class="container mx-auto px-4">
        <h1 class="font-playfair text-3xl font-bold text-white">{{ $judul }}</h1>
        <nav class="mt-2 text-sm text-green-200">
            <a href="{{ route('home') }}" class="hover:text-white transition-colors">Beranda</a>
            <span class="mx-2">/</span>
            @isset($parent)
                <span>{{ $parent }}</span>
                <span class="mx-2">/</span>
            @endisset
            <span class="text-white font-medium">{{ $judul }}</span>
        </nav>
    </div>
</section>
