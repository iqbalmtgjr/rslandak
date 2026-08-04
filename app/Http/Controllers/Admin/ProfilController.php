<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ProfilController extends Controller
{
    private array $profilKeys = [
        'profil_visi',
        'profil_motto',
        'profil_rs_sejarah',
        'profil_rs_legalitas',
        'direktur_nama',
        'direktur_jabatan',
        'direktur_nrp',
        'direktur_sambutan',
        'struktur_organisasi_keterangan',
        'maklumat_teks',
    ];

    private array $jsonKeys = [
        'profil_misi',
        'profil_rs_nilai',
        'direktur_pendidikan',
        'direktur_riwayat',
<<<<<<< HEAD
        'struktur_organisasi_json',
=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
    ];

    private array $imageKeys = [
        'profil_rs_foto',
        'direktur_foto',
        'struktur_organisasi_gambar',
        'maklumat_gambar',
    ];

    public function index()
    {
        $allKeys = array_merge($this->profilKeys, $this->jsonKeys, $this->imageKeys);
        $settings = SiteSetting::whereIn('key', $allKeys)->pluck('value', 'key');
        $settings->put('logo', SiteSetting::get('logo'));
        return view('admin.profil.index', compact('settings'));
    }

    public function update(Request $request)
    {
        foreach ($this->profilKeys as $key) {
            SiteSetting::updateOrCreate(['key' => $key], ['value' => $request->input($key)]);
        }

        // profil_misi: array of strings
        $misi = array_values(array_filter($request->input('profil_misi', [])));
        SiteSetting::updateOrCreate(['key' => 'profil_misi'], ['value' => json_encode($misi)]);

        // profil_rs_nilai: array of objects
        $nilai = array_values(array_filter($request->input('nilai', []), fn($n) => !empty($n['judul'])));
        SiteSetting::updateOrCreate(['key' => 'profil_rs_nilai'], ['value' => json_encode($nilai)]);

        // direktur_pendidikan
        $pend = array_values(array_filter($request->input('direktur_pendidikan', [])));
        SiteSetting::updateOrCreate(['key' => 'direktur_pendidikan'], ['value' => json_encode($pend)]);

        // direktur_riwayat
        $riw = array_values(array_filter($request->input('direktur_riwayat', [])));
        SiteSetting::updateOrCreate(['key' => 'direktur_riwayat'], ['value' => json_encode($riw)]);

<<<<<<< HEAD
        // struktur_organisasi_json: nested division/members list
        $struktur = $request->input('struktur_organisasi', []);
        if (is_array($struktur)) {
            foreach ($struktur as $i => $bidang) {
                if (isset($bidang['anggota']) && is_array($bidang['anggota'])) {
                    foreach ($bidang['anggota'] as $j => $anggota) {
                        $fotoPath = $anggota['foto_existing'] ?? null;
                        if ($request->hasFile("struktur_organisasi.{$i}.anggota.{$j}.foto")) {
                            if ($fotoPath) {
                                Storage::disk('public')->delete($fotoPath);
                            }
                            $file = $request->file("struktur_organisasi.{$i}.anggota.{$j}.foto");
                            $fotoPath = $file->store('rssite/profil/struktur', 'public');
                        }
                        $struktur[$i]['anggota'][$j]['foto'] = $fotoPath;
                        unset($struktur[$i]['anggota'][$j]['foto_existing']);
                    }
                } else {
                    $struktur[$i]['anggota'] = [];
                }
            }
            SiteSetting::updateOrCreate(['key' => 'struktur_organisasi_json'], ['value' => json_encode(array_values($struktur))]);
        }

=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        foreach ($this->imageKeys as $key) {
            if ($request->hasFile($key)) {
                $old = SiteSetting::where('key', $key)->value('value');
                if ($old) Storage::disk('public')->delete($old);
                $path = $request->file($key)->store('rssite/profil', 'public');
                SiteSetting::updateOrCreate(['key' => $key], ['value' => $path]);
            }
        }

        return redirect()->route('admin.profil.index')->with('success', 'Data profil berhasil disimpan.');
    }
}
