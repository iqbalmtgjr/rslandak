<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Poliklinik extends Model
{
    protected $table    = 'rssite_polikliniks';
    protected $fillable = ['nama', 'slug', 'ikon', 'tipe_ikon', 'deskripsi', 'prosedur', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->nama);
        });
    }

    public function dokters()
    {
        return $this->belongsToMany(Dokter::class, 'rssite_dokter_poliklinik', 'poliklinik_id', 'dokter_id')
                    ->withPivot('urutan')->orderBy('rssite_dokter_poliklinik.urutan');
    }

    public function scopeAktif($q) { return $q->where('aktif', true); }

    public function getJumlahDokterAttribute(): int
    {
        return $this->dokters()->where('rssite_dokters.aktif', true)->count();
    }

    public function getIkonUrlAttribute(): ?string
    {
        if ($this->tipe_ikon === 'img' && $this->ikon) {
            return asset('storage/' . $this->ikon);
        }
        return null;
    }
}
