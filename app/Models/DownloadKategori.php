<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DownloadKategori extends Model
{
    protected $table    = 'rssite_download_kategoris';
    protected $fillable = ['nama', 'slug', 'ikon', 'warna', 'deskripsi', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->nama);
        });
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'kategori_id')
                    ->where('aktif', true)->latest();
    }

    public function allDownloads()
    {
        return $this->hasMany(Download::class, 'kategori_id')->latest();
    }

    public function scopeAktif($q) { return $q->where('aktif', true); }

    public function getJumlahFileAttribute(): int
    {
        return $this->downloads()->count();
    }
}
