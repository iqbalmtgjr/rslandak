<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pkrs;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PkrsController extends Controller
{
    public function index(Request $request)
    {
        $query = Pkrs::query();
        if ($request->search) {
            $query->where('judul', 'like', '%'.$request->search.'%');
        }
        $items = $query->latest()->paginate(10)->withQueryString();

        return view('admin.pkrs.index', compact('items'));
    }

    public function create()
    {
        return view('admin.pkrs.form', ['item' => new Pkrs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'penulis' => 'nullable|string|max:100',
        ]);

        $data = $request->except(['gambar', '_token']);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['slug'] = Str::slug($request->judul);
        $data['gambar'] = $this->handleImageUpload($request, 'gambar');

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (Pkrs::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug.'-'.$count++;
        }

        Pkrs::create($data);

        return redirect()->route('admin.pkrs.index')->with('success', 'Edukasi PKRS berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $item = Pkrs::findOrFail($id);

        return view('admin.pkrs.form', compact('item'));
    }

    public function update(Request $request, int $id)
    {
        $item = Pkrs::findOrFail($id);

        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'penulis' => 'nullable|string|max:100',
        ]);

        $data = $request->except(['gambar', '_token', '_method']);
        $data['aktif'] = $request->boolean('aktif', true);
        $data['gambar'] = $this->handleImageUpload($request, 'gambar', $item->gambar);

        $item->update($data);

        return redirect()->route('admin.pkrs.index')->with('success', 'Edukasi PKRS berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $item = Pkrs::findOrFail($id);
        if ($item->gambar) {
            Storage::disk('public')->delete($item->gambar);
        }
        $item->delete();

        return redirect()->route('admin.pkrs.index')->with('success', 'Edukasi PKRS berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $item = Pkrs::findOrFail($id);
        $item->update(['aktif' => ! $item->aktif]);

        return redirect()->back()->with('success', 'Status edukasi PKRS berhasil diubah.');
    }

    private function handleImageUpload($request, $field, $oldPath = null): ?string
    {
        if ($request->hasFile($field)) {
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }

            return $request->file($field)->store('rssite/images', 'public');
        }

        return $oldPath;
    }
}
