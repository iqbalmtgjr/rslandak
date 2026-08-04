<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use App\Models\Poliklinik;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PoliklinikController extends Controller
{
    public function index()
    {
        $polikliniks = Poliklinik::withCount('dokters')->orderBy('urutan')->paginate(20);
        return view('admin.poliklinik.index', compact('polikliniks'));
    }

    public function create()
    {
        return view('admin.poliklinik.form', ['poli' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'prosedur'  => 'nullable|string',
            'urutan'    => 'nullable|integer',
            'ikon_file' => 'nullable|image|max:1024',
            'ikon_fa'   => 'nullable|string|max:100',
        ]);

        $data = [
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'prosedur'  => $request->prosedur,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('ikon_file')) {
            $data['ikon']      = $request->file('ikon_file')->store('rssite/poli-ikon', 'public');
            $data['tipe_ikon'] = 'img';
        } elseif ($request->filled('ikon_fa')) {
            $data['ikon']      = $request->ikon_fa;
            $data['tipe_ikon'] = 'fa';
        }

        Poliklinik::create($data);
        return redirect()->route('admin.poliklinik.index')->with('success', 'Poliklinik berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        return view('admin.poliklinik.form', compact('poli'));
    }

    public function update(Request $request, int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'nullable|string',
            'prosedur'  => 'nullable|string',
            'urutan'    => 'nullable|integer',
            'ikon_file' => 'nullable|image|max:1024',
            'ikon_fa'   => 'nullable|string|max:100',
        ]);

        $data = [
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'deskripsi' => $request->deskripsi,
            'prosedur'  => $request->prosedur,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('ikon_file')) {
            if ($poli->tipe_ikon === 'img' && $poli->ikon) Storage::disk('public')->delete($poli->ikon);
            $data['ikon']      = $request->file('ikon_file')->store('rssite/poli-ikon', 'public');
            $data['tipe_ikon'] = 'img';
        } elseif ($request->filled('ikon_fa')) {
            if ($poli->tipe_ikon === 'img' && $poli->ikon) Storage::disk('public')->delete($poli->ikon);
            $data['ikon']      = $request->ikon_fa;
            $data['tipe_ikon'] = 'fa';
        }

        $poli->update($data);
        return redirect()->route('admin.poliklinik.index')->with('success', 'Poliklinik berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        if ($poli->tipe_ikon === 'img' && $poli->ikon) Storage::disk('public')->delete($poli->ikon);
        $poli->delete();
        return redirect()->back()->with('success', 'Poliklinik berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        $poli->update(['aktif' => !$poli->aktif]);
        return redirect()->back()->with('success', 'Status poliklinik diperbarui.');
    }

    public function manageDokter(int $id)
    {
        $poli        = Poliklinik::with('dokters')->findOrFail($id);
        $semuaDokter = Dokter::where('aktif', true)->orderBy('nama')->get();
        $assignedIds = $poli->dokters->pluck('id')->toArray();
        return view('admin.poliklinik.dokter', compact('poli', 'semuaDokter', 'assignedIds'));
    }

    public function syncDokter(Request $request, int $id)
    {
        $poli = Poliklinik::findOrFail($id);
        $request->validate([
            'dokter_ids'   => 'nullable|array',
            'dokter_ids.*' => 'exists:rssite_dokters,id',
        ]);

        $syncData = [];
        foreach ($request->input('dokter_ids', []) as $urutan => $dokterId) {
            $syncData[$dokterId] = ['urutan' => $urutan + 1];
        }
        $poli->dokters()->sync($syncData);

        return redirect()->route('admin.poliklinik.dokter', $id)->with('success', 'Dokter poli berhasil disimpan.');
    }
}
