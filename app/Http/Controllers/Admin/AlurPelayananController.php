<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlurPelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlurPelayananController extends Controller
{
    public function index()
    {
        $alurs = AlurPelayanan::orderBy('urutan')->paginate(15);
        return view('admin.alur-pelayanan.index', compact('alurs'));
    }

    public function create()
    {
        return view('admin.alur-pelayanan.form', ['alur' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'      => 'required|string|max:200',
            'gambar'     => 'required|image|mimes:jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
            'urutan'     => 'nullable|integer',
        ], [
            'gambar.required' => 'Gambar alur wajib diupload.',
            'gambar.max'      => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $path = $request->file('gambar')->store('rssite/alur-pelayanan', 'public');

        AlurPelayanan::create([
            'judul'      => $request->judul,
            'gambar'     => $path,
            'keterangan' => $request->keterangan,
            'urutan'     => $request->urutan ?? 0,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.alur-pelayanan.index')
            ->with('success', 'Alur pelayanan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);
        return view('admin.alur-pelayanan.form', compact('alur'));
    }

    public function update(Request $request, int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);

        $request->validate([
            'judul'      => 'required|string|max:200',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
            'urutan'     => 'nullable|integer',
        ]);

        $path = $alur->gambar;
        if ($request->hasFile('gambar')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('gambar')->store('rssite/alur-pelayanan', 'public');
        }

        $alur->update([
            'judul'      => $request->judul,
            'gambar'     => $path,
            'keterangan' => $request->keterangan,
            'urutan'     => $request->urutan ?? 0,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.alur-pelayanan.index')
            ->with('success', 'Alur pelayanan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);
        if ($alur->gambar) Storage::disk('public')->delete($alur->gambar);
        $alur->delete();
        return redirect()->back()->with('success', 'Alur pelayanan berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);
        $alur->update(['aktif' => !$alur->aktif]);
        return redirect()->back()->with('success', 'Status diperbarui.');
    }
}
