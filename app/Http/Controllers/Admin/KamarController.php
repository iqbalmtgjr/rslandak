<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kamar;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KamarController extends Controller
{
    public function index(Request $request)
    {
        $query = Kamar::query();
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%');
        }
        $kamars = $query->orderBy('urutan')->paginate(10)->withQueryString();
        return view('admin.kamar.index', compact('kamars'));
    }

    public function create()
    {
        return view('admin.kamar.form', ['kamar' => new Kamar]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'    => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'gambar'  => 'nullable|image|max:2048',
            'badge'   => 'nullable|string|max:50',
            'urutan'  => 'nullable|integer',
            'tarif'   => 'nullable|integer|min:0',
            'foto_1'  => 'nullable|image|max:2048',
            'foto_2'  => 'nullable|image|max:2048',
            'foto_3'  => 'nullable|image|max:2048',
            'foto_4'  => 'nullable|image|max:2048',
            'foto_5'  => 'nullable|image|max:2048',
        ]);

        $fotoData = $this->handleFotoGallery($request);

        $data = $request->except(['gambar', '_token', 'fasilitas', 'foto_1', 'foto_2', 'foto_3', 'foto_4', 'foto_5']);
        $data['aktif']    = $request->boolean('aktif');
        $data['gambar']   = $this->handleImageUpload($request, 'gambar');
        $data['fasilitas'] = array_values(array_filter($request->fasilitas ?? [], fn($f) => !empty(trim($f))));
        $data['tarif']    = $request->tarif ?: null;
        $data = array_merge($data, $fotoData);

        Kamar::create($data);
        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil ditambahkan.');
    }

    public function edit(Kamar $kamar)
    {
        return view('admin.kamar.form', compact('kamar'));
    }

    public function update(Request $request, Kamar $kamar)
    {
        $request->validate([
            'nama'    => 'required|string|max:100',
            'deskripsi' => 'required|string',
            'gambar'  => 'nullable|image|max:2048',
            'badge'   => 'nullable|string|max:50',
            'urutan'  => 'nullable|integer',
            'tarif'   => 'nullable|integer|min:0',
            'foto_1'  => 'nullable|image|max:2048',
            'foto_2'  => 'nullable|image|max:2048',
            'foto_3'  => 'nullable|image|max:2048',
            'foto_4'  => 'nullable|image|max:2048',
            'foto_5'  => 'nullable|image|max:2048',
        ]);

        $fotoData = $this->handleFotoGallery($request, $kamar);

        $data = $request->except(['gambar', '_token', '_method', 'fasilitas', 'foto_1', 'foto_2', 'foto_3', 'foto_4', 'foto_5']);
        $data['aktif']    = $request->boolean('aktif');
        $data['gambar']   = $this->handleImageUpload($request, 'gambar', $kamar->gambar);
        $data['fasilitas'] = array_values(array_filter($request->fasilitas ?? [], fn($f) => !empty(trim($f))));
        $data['tarif']    = $request->tarif ?: null;
        $data = array_merge($data, $fotoData);

        $kamar->update($data);
        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil diperbarui.');
    }

    public function destroy(Kamar $kamar)
    {
        if ($kamar->gambar) Storage::disk('public')->delete($kamar->gambar);
        $kamar->delete();
        return redirect()->route('admin.kamar.index')->with('success', 'Kamar berhasil dihapus.');
    }

    public function toggle(Kamar $kamar)
    {
        $kamar->update(['aktif' => !$kamar->aktif]);
        return redirect()->back()->with('success', 'Status kamar berhasil diubah.');
    }

    private function handleImageUpload($request, $field, $oldPath = null): ?string
    {
        if ($request->hasFile($field)) {
            if ($oldPath) Storage::disk('public')->delete($oldPath);
            return $request->file($field)->store('rssite/images', 'public');
        }
        return $oldPath;
    }

    private function handleFotoGallery(Request $request, $kamar = null): array
    {
        $result = [];
        for ($i = 1; $i <= 5; $i++) {
            $field   = "foto_{$i}";
            $oldPath = $kamar?->$field;

            if ($request->hasFile($field)) {
                if ($oldPath) Storage::disk('public')->delete($oldPath);
                $result[$field] = $request->file($field)->store('rssite/kamar-foto', 'public');
            } elseif ($request->boolean("hapus_{$field}") && $oldPath) {
                Storage::disk('public')->delete($oldPath);
                $result[$field] = null;
            } else {
                $result[$field] = $oldPath;
            }
        }
        return $result;
    }
}
