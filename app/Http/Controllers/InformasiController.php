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
}
