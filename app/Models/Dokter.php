<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dokter extends Model
{
    protected $table = 'rssite_dokters';

    protected $fillable = ['nama', 'spesialisasi', 'foto', 'jadwal', 'bio', 'aktif', 'urutan'];

    protected $casts = ['jadwal' => 'array', 'aktif' => 'boolean'];

    public function polikliniks()
    {
        return $this->belongsToMany(Poliklinik::class, 'rssite_dokter_poliklinik', 'dokter_id', 'poliklinik_id')
                    ->withPivot('urutan');
    }
}
