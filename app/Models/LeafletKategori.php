<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeafletKategori extends Model
{
    protected $table    = 'rssite_leaflet_kategoris';
    protected $fillable = ['tipe', 'nama', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function items()
    {
        return $this->hasMany(LeafletItem::class, 'kategori_id')
            ->where('aktif', true)
            ->orderBy('urutan');
    }

    public function allItems()
    {
        return $this->hasMany(LeafletItem::class, 'kategori_id')->orderBy('urutan');
    }

    public function scopeAktif($q)
    {
        return $q->where('aktif', true);
    }
    public function scopeTipe($q, string $tipe)
    {
        return $q->where('tipe', $tipe);
    }
}
