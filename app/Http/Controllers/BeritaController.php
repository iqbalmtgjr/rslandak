<?php

namespace App\Http\Controllers;

use App\Models\Berita;
use App\Models\SiteSetting;
use Illuminate\Http\Request;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $settings = SiteSetting::pluck('value', 'key');
        $query = Berita::where('aktif', true)->latest();

<<<<<<< HEAD
        if ($request->filled('kategori') && in_array($request->kategori, ['Berita', 'Pengumuman', 'Kegiatan', 'Promosi Kesehatan'])) {
=======
        if ($request->filled('kategori') && in_array($request->kategori, ['Berita', 'Pengumuman', 'Kegiatan'])) {
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
<<<<<<< HEAD
            $query->where('judul', 'like', '%'.$request->q.'%');
        }

        $beritas = $query->paginate(9)->withQueryString();
        $kategori = $request->input('kategori', '');
        $search = $request->input('q', '');

        $countAll = Berita::where('aktif', true)->count();
        $countBerita = Berita::where('aktif', true)->where('kategori', 'Berita')->count();
        $countPengumuman = Berita::where('aktif', true)->where('kategori', 'Pengumuman')->count();
        $countKegiatan = Berita::where('aktif', true)->where('kategori', 'Kegiatan')->count();
        $countPkrs = Berita::where('aktif', true)->where('kategori', 'Promosi Kesehatan')->count();
=======
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $beritas          = $query->paginate(9)->withQueryString();
        $kategori         = $request->input('kategori', '');
        $search           = $request->input('q', '');

        $countAll         = Berita::where('aktif', true)->count();
        $countBerita      = Berita::where('aktif', true)->where('kategori', 'Berita')->count();
        $countPengumuman  = Berita::where('aktif', true)->where('kategori', 'Pengumuman')->count();
        $countKegiatan    = Berita::where('aktif', true)->where('kategori', 'Kegiatan')->count();
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7

        return view('berita.index', compact(
            'beritas',
            'kategori',
            'search',
            'countAll',
            'countBerita',
            'countPengumuman',
            'countKegiatan',
<<<<<<< HEAD
            'countPkrs',
=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            'settings'
        ));
    }

    public function show(string $slug)
    {
        $settings = SiteSetting::pluck('value', 'key');
        $berita = Berita::where('slug', $slug)->where('aktif', true)->firstOrFail();

        $berita->increment('views');

        $terkait = Berita::where('aktif', true)
            ->where('kategori', $berita->kategori)
            ->where('id', '!=', $berita->id)
            ->latest()
            ->limit(3)
            ->get();

        $terbaru = Berita::where('aktif', true)
            ->where('id', '!=', $berita->id)
            ->latest()
            ->limit(5)
            ->get();

        return view('berita.show', compact('berita', 'terkait', 'terbaru', 'settings'));
    }
}
