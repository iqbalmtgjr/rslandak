<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Hero extends Model
{
    protected $table = 'rssite_heroes';

    protected $fillable = ['judul', 'sub_judul', 'gambar', 'tombol_teks', 'tombol_url', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];
}
