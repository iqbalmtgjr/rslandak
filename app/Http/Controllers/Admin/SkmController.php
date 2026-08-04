<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Skm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SkmController extends Controller
{
    public function index(Request $request)
    {
        $query = Skm::query();
        if ($request->search) {
            $query->where('judul', 'like', '%'.$request->search.'%')
                ->orWhere('tahun', 'like', '%'.$request->search.'%');
        }
        $skms = $query->orderBy('tahun', 'desc')->orderBy('urutan')->paginate(10)->withQueryString();

        return view('admin.skm.index', compact('skms'));
    }

    public function create()
    {
        return view('admin.skm.form', ['skm' => new Skm]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'tahun' => 'required|string|max:4',
            'judul' => 'required|string|max:255',
            'gambar' => 'required|image|max:3072',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['gambar']);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('gambar')) {
            $data['gambar'] = $request->file('gambar')->store('rssite/skm', 'public');
        }

        Skm::create($data);

        return redirect()->route('admin.skm.index')->with('success', 'Hasil penilaian SKM berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $skm = Skm::findOrFail($id);

        return view('admin.skm.form', compact('skm'));
    }

    public function update(Request $request, int $id)
    {
        $skm = Skm::findOrFail($id);

        $request->validate([
            'tahun' => 'required|string|max:4',
            'judul' => 'required|string|max:255',
            'gambar' => 'nullable|image|max:3072',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['gambar']);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['urutan'] = $request->urutan ?? 0;

        if ($request->hasFile('gambar')) {
            if ($skm->gambar) {
                Storage::disk('public')->delete($skm->gambar);
            }
            $data['gambar'] = $request->file('gambar')->store('rssite/skm', 'public');
        }

        $skm->update($data);

        return redirect()->route('admin.skm.index')->with('success', 'Hasil penilaian SKM berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $skm = Skm::findOrFail($id);
        if ($skm->gambar) {
            Storage::disk('public')->delete($skm->gambar);
        }
        $skm->delete();

        return redirect()->route('admin.skm.index')->with('success', 'Hasil penilaian SKM berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $skm = Skm::findOrFail($id);
        $skm->update(['aktif' => ! $skm->aktif]);

        return redirect()->back()->with('success', 'Status SKM berhasil diubah.');
    }
}
