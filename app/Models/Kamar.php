<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Kamar extends Model
{
    protected $table = 'rssite_kamars';

    protected $fillable = [
        'nama',
        'deskripsi',
        'fasilitas',
        'gambar',
        'badge',
        'tarif',
        'foto_1',
        'foto_2',
        'foto_3',
        'foto_4',
        'foto_5',
        'urutan',
        'aktif',
    ];

    protected $casts = ['fasilitas' => 'array', 'aktif' => 'boolean'];

    public function getFotosAttribute(): array
    {
        $fotos = [];
        foreach (['foto_1', 'foto_2', 'foto_3', 'foto_4', 'foto_5'] as $col) {
            if ($this->$col) $fotos[] = asset('storage/' . $this->$col);
        }
        return $fotos;
    }

    public function getFotoUtamaAttribute(): ?string
    {
        $fotos = $this->fotos;
        return $fotos[0] ?? null;
    }

    public function getFotoThumbnailsAttribute(): array
    {
        return array_slice($this->fotos, 1);
    }

    public function getTarifReadableAttribute(): ?string
    {
        if (!$this->tarif) return null;
        return 'Rp. ' . number_format($this->tarif, 0, ',', '.') . ',00';
    }
}
