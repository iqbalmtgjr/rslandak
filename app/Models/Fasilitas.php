<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'rssite_fasilitas';

    protected $fillable = ['nama', 'deskripsi', 'gambar', 'untuk_difabel', 'kategori', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean', 'untuk_difabel' => 'boolean'];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }
}
