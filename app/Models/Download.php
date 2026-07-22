<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    protected $table    = 'rssite_downloads';
    protected $fillable = [
        'kategori_id',
        'judul',
        'deskripsi',
        'nama_file',
        'path_file',
        'tipe_file',
        'mime_type',
        'ukuran_file',
        'jumlah_download',
        'aktif',
    ];
    protected $casts = ['aktif' => 'boolean'];

    public function kategori()
    {
        return $this->belongsTo(DownloadKategori::class, 'kategori_id');
    }

    public function getUrlDownloadAttribute(): string
    {
        return route('download.unduh', $this->id);
    }

    public function getUkuranReadableAttribute(): string
    {
        $b = $this->ukuran_file;
        if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
        if ($b >= 1024)    return round($b / 1024) . ' KB';
        return $b . ' B';
    }

    public function getIkonFileAttribute(): string
    {
        return match ($this->tipe_file) {
            'pdf'                        => 'fa-file-pdf',
            'doc', 'docx'               => 'fa-file-word',
            'xls', 'xlsx'               => 'fa-file-excel',
            'ppt', 'pptx'               => 'fa-file-powerpoint',
            'jpg', 'jpeg', 'png',
            'gif', 'webp'               => 'fa-file-image',
            'zip', 'rar', '7z'          => 'fa-file-archive',
            default                      => 'fa-file-alt',
        };
    }

    public function getWarnaIkonAttribute(): string
    {
        return match ($this->tipe_file) {
            'pdf'                        => 'text-red-500',
            'doc', 'docx'               => 'text-blue-600',
            'xls', 'xlsx'               => 'text-green-600',
            'ppt', 'pptx'               => 'text-orange-500',
            'jpg', 'jpeg', 'png',
            'gif', 'webp'               => 'text-purple-500',
            'zip', 'rar', '7z'          => 'text-yellow-600',
            default                      => 'text-gray-500',
        };
    }

    public function getBgIkonAttribute(): string
    {
        return match ($this->tipe_file) {
            'pdf'                        => 'bg-red-50',
            'doc', 'docx'               => 'bg-blue-50',
            'xls', 'xlsx'               => 'bg-green-50',
            'ppt', 'pptx'               => 'bg-orange-50',
            'jpg', 'jpeg', 'png',
            'gif', 'webp'               => 'bg-purple-50',
            'zip', 'rar', '7z'          => 'bg-yellow-50',
            default                      => 'bg-gray-50',
        };
    }
}
