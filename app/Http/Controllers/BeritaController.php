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

        if ($request->filled('kategori') && in_array($request->kategori, ['Berita', 'Pengumuman', 'Kegiatan'])) {
            $query->where('kategori', $request->kategori);
        }

        if ($request->filled('q')) {
            $query->where('judul', 'like', '%' . $request->q . '%');
        }

        $beritas          = $query->paginate(9)->withQueryString();
        $kategori         = $request->input('kategori', '');
        $search           = $request->input('q', '');

        $countAll         = Berita::where('aktif', true)->count();
        $countBerita      = Berita::where('aktif', true)->where('kategori', 'Berita')->count();
        $countPengumuman  = Berita::where('aktif', true)->where('kategori', 'Pengumuman')->count();
        $countKegiatan    = Berita::where('aktif', true)->where('kategori', 'Kegiatan')->count();

        return view('berita.index', compact(
            'beritas', 'kategori', 'search',
            'countAll', 'countBerita', 'countPengumuman', 'countKegiatan',
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
