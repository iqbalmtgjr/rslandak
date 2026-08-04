<?php

namespace App\Http\Controllers;

use App\Models\Fasilitas;
use App\Models\Kamar;
use App\Models\Pelayanan24Jam;
use App\Models\Poliklinik;
use App\Models\SiteSetting;

class LayananController extends Controller
{
    public function poliklinik()
    {
        $polikliniks = Poliklinik::aktif()
            ->withCount(['dokters as jumlah_dokter' => fn ($q) => $q->where('rssite_dokters.aktif', true)])
            ->orderBy('urutan')->get();

        return view('layanan.poliklinik.index', compact('polikliniks'));
    }

    public function poliklinikDetail(string $slug)
    {
        $poli = Poliklinik::aktif()->where('slug', $slug)
            ->with(['dokters' => fn ($q) => $q->where('rssite_dokters.aktif', true)
                ->orderBy('rssite_dokter_poliklinik.urutan')])
            ->firstOrFail();

        return view('layanan.poliklinik.show', compact('poli'));
    }

    public function rawatInap()
    {
        $kamars = Kamar::where('aktif', true)->orderBy('urutan')->get();
        $kamarsJson = $kamars->map(fn ($k) => [
            'id' => $k->id,
            'nama' => $k->nama,
            'deskripsi' => $k->deskripsi,
            'fasilitas' => is_array($k->fasilitas) ? $k->fasilitas : json_decode($k->fasilitas ?? '[]', true),
            'badge' => $k->badge,
            'tarif' => $k->tarif,
            'tarif_text' => $k->tarif_readable,
            'fotos' => $k->fotos,
            'foto_utama' => $k->foto_utama,
        ]);

        return view('layanan.rawat-inap', compact('kamars', 'kamarsJson'));
    }

    public function pelayanan24Jam()
    {
        $items = Pelayanan24Jam::aktif()->orderBy('urutan')->get();

        return view('layanan.pelayanan-24-jam', compact('items'));
    }

    public function fasilitas()
    {
        $allFasilitas = Fasilitas::aktif()->orderBy('urutan')->get();
        $fasilitasByKategori = $allFasilitas->groupBy('kategori');

        return view('layanan.fasilitas', [
            'fasilitasByKategori' => $fasilitasByKategori,
            'settings' => SiteSetting::pluck('value', 'key'),
        ]);
    }

    public function fasilitasDifabel()
    {
        return redirect()->route('layanan.fasilitas', ['tab' => 'difabel']);
    }

    public function pengaduan()
    {
        return view('layanan.pengaduan');
    }

    public function saranStore(\Illuminate\Http\Request $request)
    {
        $request->validate([
            'tipe' => 'required|string|in:like,dislike',
            'pesan' => 'nullable|string|max:2000',
        ]);

        \App\Models\Saran::create([
            'tipe' => $request->tipe,
            'pesan' => $request->pesan,
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Terima kasih atas saran dan feedback Anda!',
        ]);
    }
}
