<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelayanan24Jam extends Model
{
    protected $table    = 'rssite_pelayanan24jams';
    protected $fillable = ['nama', 'deskripsi', 'foto', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function scopeAktif($q) { return $q->where('aktif', true); }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
