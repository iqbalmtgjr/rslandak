<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelayanan24Jam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Pelayanan24JamController extends Controller
{
    public function index()
    {
        $items = Pelayanan24Jam::orderBy('urutan')->paginate(20);
        return view('admin.pelayanan24jam.index', compact('items'));
    }

    public function create()
    {
        return view('admin.pelayanan24jam.form', ['item' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'foto'    => 'nullable|image|max:2048',
            'urutan'  => 'nullable|integer',
        ]);

        $data = [
            'nama'      => strtoupper($request->nama),
            'deskripsi' => $request->deskripsi,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('rssite/pelayanan24jam', 'public');
        }

        Pelayanan24Jam::create($data);
        return redirect()->route('admin.pelayanan24jam.index')->with('success', 'Pelayanan 24 Jam berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = Pelayanan24Jam::findOrFail($id);
        return view('admin.pelayanan24jam.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = Pelayanan24Jam::findOrFail($id);

        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'foto'      => 'nullable|image|max:2048',
            'urutan'    => 'nullable|integer',
        ]);

        $data = [
            'nama'      => strtoupper($request->nama),
            'deskripsi' => $request->deskripsi,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('foto')) {
            if ($item->foto) Storage::disk('public')->delete($item->foto);
            $data['foto'] = $request->file('foto')->store('rssite/pelayanan24jam', 'public');
        }

        $item->update($data);
        return redirect()->route('admin.pelayanan24jam.index')->with('success', 'Pelayanan 24 Jam berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = Pelayanan24Jam::findOrFail($id);
        if ($item->foto) Storage::disk('public')->delete($item->foto);
        $item->delete();
        return redirect()->back()->with('success', 'Pelayanan 24 Jam berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $item = Pelayanan24Jam::findOrFail($id);
        $item->update(['aktif' => !$item->aktif]);
        return redirect()->back()->with('success', 'Status pelayanan diperbarui.');
    }
}
