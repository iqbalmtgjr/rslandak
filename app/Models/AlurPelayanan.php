<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlurPelayanan extends Model
{
    protected $table    = 'rssite_alur_pelayanan';
    protected $fillable = ['judul', 'gambar', 'keterangan', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }

    public function getGambarUrlAttribute(): ?string
    {
        return $this->gambar ? asset('storage/' . $this->gambar) : null;
    }
}
