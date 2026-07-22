<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Berita;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class BeritaController extends Controller
{
    public function index(Request $request)
    {
        $query = Berita::query();
        if ($request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        $beritas = $query->latest()->paginate(10)->withQueryString();
        return view('admin.berita.index', compact('beritas'));
    }

    public function create()
    {
        return view('admin.berita.form', ['berita' => new Berita]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:Berita,Pengumuman,Kegiatan',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'penulis' => 'nullable|string|max:100',
        ]);

        $data = $request->except(['gambar', '_token']);
        $data['aktif'] = $request->boolean('aktif');
        $data['slug'] = Str::slug($request->judul);
        $data['gambar'] = $this->handleImageUpload($request, 'gambar');

        // Ensure unique slug
        $originalSlug = $data['slug'];
        $count = 1;
        while (Berita::where('slug', $data['slug'])->exists()) {
            $data['slug'] = $originalSlug . '-' . $count++;
        }

        Berita::create($data);
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil ditambahkan.');
    }

    public function edit(Berita $berita)
    {
        return view('admin.berita.form', compact('berita'));
    }

    public function update(Request $request, Berita $berita)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'kategori' => 'required|in:Berita,Pengumuman,Kegiatan',
            'konten' => 'required|string',
            'gambar' => 'nullable|image|max:2048',
            'penulis' => 'nullable|string|max:100',
        ]);

        $data = $request->except(['gambar', '_token', '_method']);
        $data['aktif'] = $request->boolean('aktif');
        $data['gambar'] = $this->handleImageUpload($request, 'gambar', $berita->gambar);

        $berita->update($data);
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil diperbarui.');
    }

    public function destroy(Berita $berita)
    {
        if ($berita->gambar) Storage::disk('public')->delete($berita->gambar);
        $berita->delete();
        return redirect()->route('admin.berita.index')->with('success', 'Berita berhasil dihapus.');
    }

    public function toggle(Berita $berita)
    {
        $berita->update(['aktif' => !$berita->aktif]);
        return redirect()->back()->with('success', 'Status berita berhasil diubah.');
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
