<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Layanan extends Model
{
    protected $table = 'rssite_layanans';

    protected $fillable = ['nama', 'deskripsi', 'ikon', 'gambar', 'urutan', 'aktif'];

    protected $casts = ['aktif' => 'boolean'];
}
