<?php

namespace App\Http\Controllers;

use App\Models\Hero;
use App\Models\Layanan;
use App\Models\Dokter;
use App\Models\Berita;
use App\Models\Kamar;
use App\Models\SiteSetting;

class HomeController extends Controller
{
    public function index()
    {
        $heroes = Hero::where('aktif', true)->orderBy('urutan')->get();
        $layanans = Layanan::where('aktif', true)->orderBy('urutan')->get();
        $dokters = Dokter::where('aktif', true)->orderBy('urutan')->take(3)->get();
        $kamars = Kamar::where('aktif', true)->orderBy('urutan')->get();
        $beritas = Berita::where('aktif', true)->latest()->take(3)->get();
        $settings = SiteSetting::pluck('value', 'key');

        return view('home', compact('heroes', 'layanans', 'dokters', 'kamars', 'beritas', 'settings'));
    }

}
