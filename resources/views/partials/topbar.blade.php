<div class="bg-dark text-white py-2 text-sm">
    <div class="container mx-auto px-4 flex flex-wrap justify-between items-center gap-2">
        <div class="flex flex-wrap items-center gap-4">
            <span class="flex items-center gap-1">
                <i class="fas fa-phone text-gold-light text-xs"></i>
                {{ $settings['telepon'] ?? '(0563) 2022170' }}
            </span>
            <span class="flex items-center gap-1">
                <i class="fas fa-envelope text-gold-light text-xs"></i>
                {{ $settings['email'] ?? 'rsudlandak@gmail.com' }}
            </span>
            <span class="flex items-center gap-1">
                <i class="fas fa-clock text-gold-light text-xs"></i>
                IGD {{ $settings['jam_igd'] ?? '24 Jam' }}
            </span>
        </div>
        <div class="flex items-center gap-3">
            @if(!empty($settings['facebook_url']))
            <a href="{{ $settings['facebook_url'] }}" target="_blank" class="hover:text-gold-light transition-colors"><i class="fab fa-facebook-f"></i></a>
            @endif
            @if(!empty($settings['instagram_url']))
            <a href="{{ $settings['instagram_url'] }}" target="_blank" class="hover:text-gold-light transition-colors"><i class="fab fa-instagram"></i></a>
            @endif
            @if(!empty($settings['youtube_url']))
            <a href="{{ $settings['youtube_url'] }}" target="_blank" class="hover:text-gold-light transition-colors"><i class="fab fa-youtube"></i></a>
            @endif
        </div>
    </div>
</div>
