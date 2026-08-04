<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Fasilitas extends Model
{
    protected $table = 'rssite_fasilitas';

<<<<<<< HEAD
    protected $fillable = ['nama', 'deskripsi', 'gambar', 'kategori', 'untuk_difabel', 'urutan', 'aktif'];
=======
    protected $fillable = ['nama', 'deskripsi', 'gambar', 'untuk_difabel', 'urutan', 'aktif'];
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7

    protected $casts = ['aktif' => 'boolean', 'untuk_difabel' => 'boolean'];

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }
}
