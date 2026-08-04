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
<<<<<<< HEAD
            $query->where('nama', 'like', '%'.$request->search.'%');
        }
        $fasilitas = $query->orderBy('urutan')->paginate(10)->withQueryString();

=======
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $fasilitas = $query->orderBy('urutan')->paginate(10)->withQueryString();
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
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
<<<<<<< HEAD
            'kategori' => 'required|string|in:klinik,parkir,difabel,prioritas',
=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        ]);

        Fasilitas::create([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $this->handleImageUpload($request, 'gambar'),
<<<<<<< HEAD
            'kategori' => $request->kategori,
            'untuk_difabel' => $request->kategori === 'difabel',
=======
            'untuk_difabel' => $request->boolean('untuk_difabel'),
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            'urutan' => $request->urutan ?? 0,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = Fasilitas::findOrFail($id);
<<<<<<< HEAD

=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
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
<<<<<<< HEAD
            'kategori' => 'required|string|in:klinik,parkir,difabel,prioritas',
=======
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        ]);

        $item->update([
            'nama' => $request->nama,
            'deskripsi' => $request->deskripsi,
            'gambar' => $this->handleImageUpload($request, 'gambar', $item->gambar),
<<<<<<< HEAD
            'kategori' => $request->kategori,
            'untuk_difabel' => $request->kategori === 'difabel',
=======
            'untuk_difabel' => $request->boolean('untuk_difabel'),
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
            'urutan' => $request->urutan ?? 0,
            'aktif' => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = Fasilitas::findOrFail($id);
<<<<<<< HEAD
        if ($item->gambar) {
            Storage::disk('public')->delete($item->gambar);
        }
        $item->delete();

=======
        if ($item->gambar) Storage::disk('public')->delete($item->gambar);
        $item->delete();
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return redirect()->route('admin.fasilitas.index')->with('success', 'Fasilitas berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $item = Fasilitas::findOrFail($id);
<<<<<<< HEAD
        $item->update(['aktif' => ! $item->aktif]);

=======
        $item->update(['aktif' => !$item->aktif]);
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return redirect()->back()->with('success', 'Status fasilitas berhasil diubah.');
    }

    private function handleImageUpload($request, $field, $oldPath = null): ?string
    {
        if ($request->hasFile($field)) {
<<<<<<< HEAD
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            return $request->file($field)->store('rssite/images', 'public');
        }

=======
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            return $request->file($field)->store('rssite/images', 'public');
        }
>>>>>>> 6604c80ceab75fc841c8d2e9ff5dbd5c54a0d5e7
        return $oldPath;
    }
}
