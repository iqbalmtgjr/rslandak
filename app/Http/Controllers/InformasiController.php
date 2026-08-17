<?php

namespace App\Http\Controllers;

use App\Models\AlurPelayanan;
use App\Models\Dokter;

class InformasiController extends Controller
{
    public function alurPelayanan()
    {
        $alurs = AlurPelayanan::aktif()->orderBy('urutan')->get();
        return view('informasi.alur-pelayanan', compact('alurs'));
    }

    public function dokter()
    {
        $dokters = Dokter::where('aktif', true)->orderBy('urutan')->get();
        return view('informasi.dokter', compact('dokters'));
    }

    public function pkrs()
    {
        $items = \App\Models\Pkrs::aktif()->latest()->paginate(9);
        return view('informasi.pkrs', compact('items'));
    }

    public function pkrsShow($slug)
    {
        $item = \App\Models\Pkrs::aktif()->where('slug', $slug)->firstOrFail();
        $item->increment('views');

        $recent = \App\Models\Pkrs::aktif()->where('id', '!=', $item->id)->latest()->take(4)->get();

        return view('informasi.pkrs-detail', compact('item', 'recent'));
    }
}
