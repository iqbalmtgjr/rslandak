<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Layanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LayananController extends Controller
{
    public function index(Request $request)
    {
        $query = Layanan::query();
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $layanans = $query->orderBy('urutan')->paginate(10)->withQueryString();
        return view('admin.layanan.index', compact('layanans'));
    }

    public function create()
    {
        return view('admin.layanan.form', ['layanan' => new Layanan]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'ikon' => 'required|string|max:100',
            'gambar' => 'nullable|image|max:2048',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['gambar', '_token']);
        $data['aktif'] = $request->boolean('aktif');
        $data['gambar'] = $this->handleImageUpload($request, 'gambar');

        Layanan::create($data);
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(Layanan $layanan)
    {
        return view('admin.layanan.form', compact('layanan'));
    }

    public function update(Request $request, Layanan $layanan)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'ikon' => 'required|string|max:100',
            'gambar' => 'nullable|image|max:2048',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['gambar', '_token', '_method']);
        $data['aktif'] = $request->boolean('aktif');
        $data['gambar'] = $this->handleImageUpload($request, 'gambar', $layanan->gambar);

        $layanan->update($data);
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(Layanan $layanan)
    {
        if ($layanan->gambar) Storage::disk('public')->delete($layanan->gambar);
        $layanan->delete();
        return redirect()->route('admin.layanan.index')->with('success', 'Layanan berhasil dihapus.');
    }

    public function toggle(Layanan $layanan)
    {
        $layanan->update(['aktif' => !$layanan->aktif]);
        return redirect()->back()->with('success', 'Status layanan berhasil diubah.');
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
