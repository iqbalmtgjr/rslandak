<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pkrs extends Model
{
    protected $table = 'rssite_pkrs';

    protected $fillable = ['judul', 'slug', 'gambar', 'konten', 'penulis', 'views', 'aktif', 'urutan'];

    protected $casts = [
        'aktif' => 'boolean',
    ];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }
}
