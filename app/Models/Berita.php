<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Berita extends Model
{
    protected $table = 'rssite_beritas';

    protected $fillable = ['judul', 'slug', 'kategori', 'konten', 'gambar', 'penulis', 'views', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];

    protected static function booted(): void
    {
        static::creating(function ($berita) {
            if (empty($berita->slug)) {
                $berita->slug = Str::slug($berita->judul);
            }
        });
    }

    public function getRingkasanAttribute(): string
    {
        return mb_substr(strip_tags($this->konten), 0, 150) . '...';
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
