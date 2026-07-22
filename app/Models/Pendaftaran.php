<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pendaftaran extends Model
{
    protected $table    = 'rssite_pendaftarans';
    protected $fillable = [
        'kode',
        'nama_lengkap',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'jenis_kelamin',
        'nomor_telepon',
        'alamat',
        'status_pasien',
        'jenis_layanan',
        'nama_asuransi',
        'poli_tujuan',
        'catatan',
        'foto_ktp',
        'foto_bpjs',
        'status',
        'catatan_admin',
        'sudah_konfirmasi_wa',
        'dikonfirmasi_at',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'sudah_konfirmasi_wa' => 'boolean',
        'dikonfirmasi_at'     => 'datetime',
    ];

    public static function generateKode(): string
    {
        $prefix = 'RS-' . now()->format('Ymd') . '-';
        $last   = static::where('kode', 'like', $prefix . '%')->orderByDesc('kode')->first();
        $seq    = $last ? (intval(substr($last->kode, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    public function getFotoKtpUrlAttribute(): ?string
    {
        return $this->foto_ktp ? asset('storage/' . $this->foto_ktp) : null;
    }

    public function getFotoBpjsUrlAttribute(): ?string
    {
        return $this->foto_bpjs ? asset('storage/' . $this->foto_bpjs) : null;
    }

    public function getTanggalLahirReadableAttribute(): string
    {
        if (!$this->tanggal_lahir) return '-';
        return Carbon::parse($this->tanggal_lahir)->translatedFormat('d F Y');
    }

    public function getPesanWaAttribute(): string
    {
        $nomor = SiteSetting::get('wa_pendaftaran', '6283830331205');
        $lines = [
            '*KONFIRMASI PENDAFTARAN ONLINE*',
            '*RSUD Landak*',
            '─────────────────────',
            "Nomor Pendaftaran : *{$this->kode}*",
            "Nama              : {$this->nama_lengkap}",
            "NIK               : " . ($this->nik ?: '-'),
            "TTL               : " . ($this->tempat_lahir ? $this->tempat_lahir . ', ' : '') . $this->tanggal_lahir_readable,
            "Jenis Kelamin     : {$this->jenis_kelamin}",
            "No. Telepon       : {$this->nomor_telepon}",
            "Status Pasien     : {$this->status_pasien}",
            "Jenis Layanan     : {$this->jenis_layanan}",
            "Poli Tujuan       : {$this->poli_tujuan}",
            '─────────────────────',
            'Mohon konfirmasi pendaftaran ini. Terima kasih.',
        ];
        $pesan = implode("\n", $lines);
        $nomor = preg_replace('/^0/', '62', preg_replace('/\D/', '', $nomor));
        return "https://wa.me/{$nomor}?text=" . rawurlencode($pesan);
    }

    public function getStatusBadgeAttribute(): array
    {
        return match ($this->status) {
            'Menunggu'     => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
            'Dikonfirmasi' => ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
            'Selesai'      => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
            'Dibatalkan'   => ['bg' => 'bg-red-100',    'text' => 'text-red-500'],
            default        => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600'],
        };
    }
}
