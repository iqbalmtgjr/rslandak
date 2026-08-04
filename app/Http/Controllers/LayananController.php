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
<<<<<<< HEAD
            ->withCount(['dokters as jumlah_dokter' => fn ($q) => $q->where('rssite_dokters.aktif', true)])
            ->orderBy('urutan')->get();

=======
            ->withCount(['dokters as jumlah_dokter' => fn($q) => $q->where('rssite_dokters.aktif', true)])
            ->orderBy('urutan')->get();
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return view('layanan.poliklinik.index', compact('polikliniks'));
    }

    public function poliklinikDetail(string $slug)
    {
        $poli = Poliklinik::aktif()->where('slug', $slug)
<<<<<<< HEAD
            ->with(['dokters' => fn ($q) => $q->where('rssite_dokters.aktif', true)
                ->orderBy('rssite_dokter_poliklinik.urutan')])
            ->firstOrFail();

=======
            ->with(['dokters' => fn($q) => $q->where('rssite_dokters.aktif', true)
                ->orderBy('rssite_dokter_poliklinik.urutan')])
            ->firstOrFail();
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return view('layanan.poliklinik.show', compact('poli'));
    }

    public function rawatInap()
    {
        $kamars = Kamar::where('aktif', true)->orderBy('urutan')->get();
<<<<<<< HEAD
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

=======
        $kamarsJson = $kamars->map(fn($k) => [
            'id'         => $k->id,
            'nama'       => $k->nama,
            'deskripsi'  => $k->deskripsi,
            'fasilitas'  => is_array($k->fasilitas) ? $k->fasilitas : json_decode($k->fasilitas ?? '[]', true),
            'badge'      => $k->badge,
            'tarif'      => $k->tarif,
            'tarif_text' => $k->tarif_readable,
            'fotos'      => $k->fotos,
            'foto_utama' => $k->foto_utama,
        ]);
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return view('layanan.rawat-inap', compact('kamars', 'kamarsJson'));
    }

    public function pelayanan24Jam()
    {
        $items = Pelayanan24Jam::aktif()->orderBy('urutan')->get();
<<<<<<< HEAD

=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return view('layanan.pelayanan-24-jam', compact('items'));
    }

    public function fasilitas()
    {
<<<<<<< HEAD
        $allFasilitas = Fasilitas::aktif()->orderBy('urutan')->get();
        $fasilitasByKategori = $allFasilitas->groupBy('kategori');

        return view('layanan.fasilitas', [
            'fasilitasByKategori' => $fasilitasByKategori,
            'settings' => SiteSetting::pluck('value', 'key'),
        ]);
=======
        $items = Fasilitas::aktif()->where('untuk_difabel', false)->orderBy('urutan')->get();
        return view('layanan.fasilitas', compact('items'));
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
    }

    public function fasilitasDifabel()
    {
<<<<<<< HEAD
        return redirect()->route('layanan.fasilitas', ['tab' => 'difabel']);
=======
        $items = Fasilitas::aktif()->where('untuk_difabel', true)->orderBy('urutan')->get();
        return view('layanan.fasilitas-difabel', compact('items'));
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
    }

    public function pengaduan()
    {
        return view('layanan.pengaduan');
    }
<<<<<<< HEAD

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
=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
}
