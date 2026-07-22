<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LeafletItem extends Model
{
    protected $table    = 'rssite_leaflet_items';
    protected $fillable = ['kategori_id', 'nama', 'url_gdrive', 'deskripsi', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function kategori()
    {
        return $this->belongsTo(LeafletKategori::class, 'kategori_id');
    }

    public function getUrlPreviewAttribute(): string
    {
        $url = $this->url_gdrive;
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/preview';
        }
        return $url;
    }

    public function getUrlOpenAttribute(): string
    {
        $url = $this->url_gdrive;
        if (preg_match('/\/d\/([a-zA-Z0-9_-]+)/', $url, $m)) {
            return 'https://drive.google.com/file/d/' . $m[1] . '/view';
        }
        return $url;
    }
}
