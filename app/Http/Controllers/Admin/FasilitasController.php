<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Fasilitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class FasilitasController extends Controller
{
    public function index(Request $request)
    {
        $query = Fasilitas::query();
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $fasilitas = $query->orderBy('urutan')->paginate(10)->withQueryString();
        return view('admin.fasilitas.index', compact('fasilitas'));
    }

    public function create()
    {
        return view('admin.fasilitas.form', ['item' => new Fasilitas]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'urutan' => 'nullable|integer',
        ]);

        Fasilitas::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $this->handleImageUpload($request, 'gambar'),
            'untuk_difabel' => $request->boolean('untuk_difabel'),
            'urutan' => $request->urutan ?? 0,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = Fasilitas::findOrFail($id);
        return view('admin.fasilitas.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = Fasilitas::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'urutan' => 'nullable|integer',
        ]);

        $item->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $this->handleImageUpload($request, 'gambar', $item->gambar),
            'untuk_difabel' => $request->boolean('untuk_difabel'),
            'urutan' => $request->urutan ?? 0,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = Fasilitas::findOrFail($id);
        if ($item->gambar) Storage::disk('public')->delete($item->gambar);
        $item->delete();
        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $item = Fasilitas::findOrFail($id);
        $item->update(['aktif' => !$item->aktif]);
        return redirect()->back()->with('success', 'Status fasilitas berhasil diubah.');
    }

    private function handleImageUpload($request, $field, $oldPath = null): ?string
    {
        if ($request->hasFile($field)) {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            return $request->file($field)->store('rssite/images', 'public');
        }
        return $oldPath;
    }
}
