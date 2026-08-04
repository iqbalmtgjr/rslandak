<?php

namespace App\Http\Controllers;

use App\Models\LeafletKategori;

class LeafletController extends Controller
{
    public function index()
    {
        $leaflets = LeafletKategori::with('items')
            ->aktif()->tipe('Leaflet')
            ->orderBy('urutan')->get();

        $posters = LeafletKategori::with('items')
            ->aktif()->tipe('Poster')
            ->orderBy('urutan')->get();

        return view('leaflet.index', compact('leaflets', 'posters'));
    }
}
