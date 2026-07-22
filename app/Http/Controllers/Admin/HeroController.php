<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Hero;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class HeroController extends Controller
{
    public function index(Request $request)
    {
        $query = Hero::query();
        if ($request->search) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }
        $heroes = $query->orderBy('urutan')->paginate(10)->withQueryString();
        return view('admin.hero.index', compact('heroes'));
    }

    public function create()
    {
        return view('admin.hero.form', ['hero' => new Hero]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'sub_judul' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
            'tombol_teks' => 'nullable|string|max:100',
            'tombol_url' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['gambar', '_token']);
        $data['aktif'] = $request->boolean('aktif');
        $data['gambar'] = $this->handleImageUpload($request, 'gambar');

        Hero::create($data);
        return redirect()->route('admin.hero.index')->with('success', 'Hero berhasil ditambahkan.');
    }

    public function edit(Hero $hero)
    {
        return view('admin.hero.form', compact('hero'));
    }

    public function update(Request $request, Hero $hero)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'sub_judul' => 'nullable|string',
            'gambar' => 'nullable|image|max:2048',
            'tombol_teks' => 'nullable|string|max:100',
            'tombol_url' => 'nullable|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['gambar', '_token', '_method']);
        $data['aktif'] = $request->boolean('aktif');
        $data['gambar'] = $this->handleImageUpload($request, 'gambar', $hero->gambar);

        $hero->update($data);
        return redirect()->route('admin.hero.index')->with('success', 'Hero berhasil diperbarui.');
    }

    public function destroy(Hero $hero)
    {
        if ($hero->gambar) Storage::disk('public')->delete($hero->gambar);
        $hero->delete();
        return redirect()->route('admin.hero.index')->with('success', 'Hero berhasil dihapus.');
    }

    public function toggle(Hero $hero)
    {
        $hero->update(['aktif' => !$hero->aktif]);
        return redirect()->back()->with('success', 'Status hero berhasil diubah.');
    }

    public function reorder(Request $request)
    {
        foreach ($request->order as $id => $urutan) {
            Hero::where('id', $id)->update(['urutan' => $urutan]);
        }
        return response()->json(['success' => true]);
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
