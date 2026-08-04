<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use App\Models\Layanan;
use App\Models\Dokter;
use App\Models\Berita;
use App\Models\Kamar;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'hero_aktif' => Hero::where('aktif', true)->count(),
            'hero_total' => Hero::count(),
            'layanan_total' => Layanan::count(),
            'dokter_aktif' => Dokter::where('aktif', true)->count(),
            'berita_total' => Berita::count(),
            'berita_views' => Berita::sum('views'),
            'kamar_total' => Kamar::count(),
        ];
        $berita_terbaru = Berita::latest()->take(5)->get();
        return view('admin.dashboard', compact('stats', 'berita_terbaru'));
    }
}
