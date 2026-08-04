<?php

namespace App\Http\Controllers;

use App\Models\SiteSetting;

class ProfilController extends Controller
{
    private function settings(): array
    {
        return SiteSetting::whereIn('key', [
            'profil_visi',
            'profil_misi',
            'profil_motto',
            'profil_rs_foto',
            'profil_rs_sejarah',
            'profil_rs_legalitas',
            'profil_rs_nilai',
            'direktur_nama',
            'direktur_jabatan',
            'direktur_nrp',
            'direktur_foto',
            'direktur_sambutan',
            'direktur_pendidikan',
            'direktur_riwayat',
            'struktur_organisasi_gambar',
            'struktur_organisasi_keterangan',
            'maklumat_gambar',
            'maklumat_teks',
            'nama_rs',
            'alamat',
            'telepon',
            'email',
        ])->pluck('value', 'key')->toArray();
    }

    public function visiMisi()
    {
        $s = $this->settings();
        $settings = SiteSetting::pluck('value', 'key');

        return view('profil.visi-misi', [
            'visi' => $s['profil_visi'] ?? '',
            'misi' => json_decode($s['profil_misi'] ?? '[]', true) ?: [],
            'motto' => $s['profil_motto'] ?? '',
            'foto' => $s['profil_rs_foto'] ?? null,
            'nama_rs' => $s['nama_rs'] ?? 'RSUD Landak',
            'settings' => $settings,
        ]);
    }

    public function profilRs()
    {
        $s = $this->settings();
        $settings = SiteSetting::pluck('value', 'key');

        return view('profil.profil-rs', [
            'foto' => $s['profil_rs_foto'] ?? null,
            'sejarah' => $s['profil_rs_sejarah'] ?? '',
            'legalitas' => $s['profil_rs_legalitas'] ?? '',
            'nilai' => json_decode($s['profil_rs_nilai'] ?? '[]', true) ?: [],
            'nama_rs' => $s['nama_rs'] ?? 'RSUD Landak',
            'settings' => $settings,
        ]);
    }

    public function strukturOrganisasi()
    {
        $s = $this->settings();
        $settings = SiteSetting::pluck('value', 'key');

        return view('profil.struktur-organisasi', [
            'gambar' => $s['struktur_organisasi_gambar'] ?? null,
            'keterangan' => $s['struktur_organisasi_keterangan'] ?? '',
            'nama_rs' => $s['nama_rs'] ?? 'RSUD Landak',
            'settings' => $settings,
        ]);
    }

    public function maklumat()
    {
        $s = $this->settings();
        $settings = SiteSetting::pluck('value', 'key');

        return view('profil.maklumat', [
            'gambar' => $s['maklumat_gambar'] ?? $s['profil_rs_foto'] ?? null,
            'maklumat_gambar' => $s['maklumat_gambar'] ?? null,
            'teks' => $s['maklumat_teks'] ?? '',
            'nama_rs' => $s['nama_rs'] ?? 'RSUD Landak',
            'settings' => $settings,
        ]);
    }

    public function direktur()
    {
        $s = $this->settings();
        $settings = SiteSetting::pluck('value', 'key');

        return view('profil.direktur', [
            'nama' => $s['direktur_nama'] ?? '',
            'jabatan' => $s['direktur_jabatan'] ?? '',
            'nrp' => $s['direktur_nrp'] ?? '',
            'foto' => $s['direktur_foto'] ?? null,
            'sambutan' => $s['direktur_sambutan'] ?? '',
            'pendidikan' => json_decode($s['direktur_pendidikan'] ?? '[]', true) ?: [],
            'riwayat' => json_decode($s['direktur_riwayat'] ?? '[]', true) ?: [],
            'settings' => $settings,
        ]);
    }

    public function skm()
    {
        $settings = SiteSetting::pluck('value', 'key');
        $skms = \App\Models\Skm::aktif()->orderBy('tahun', 'desc')->orderBy('urutan')->get();
        $skmsByYear = $skms->groupBy('tahun');

        return view('profil.skm', [
            'skmsByYear' => $skmsByYear,
            'settings' => $settings,
            'nama_rs' => $settings['nama_rs'] ?? 'RSUD Landak',
        ]);
    }
}
