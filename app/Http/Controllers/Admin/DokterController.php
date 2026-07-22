<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Dokter;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class DokterController extends Controller
{
    public function index(Request $request)
    {
        $query = Dokter::query();
        if ($request->search) {
            $query->where('nama', 'like', '%' . $request->search . '%')
                  ->orWhere('spesialisasi', 'like', '%' . $request->search . '%');
        }
        $dokters = $query->orderBy('urutan')->paginate(10)->withQueryString();
        return view('admin.dokter.index', compact('dokters'));
    }

    public function create()
    {
        return view('admin.dokter.form', ['dokter' => new Dokter]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
            'bio' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['foto', '_token', 'jadwal']);
        $data['aktif'] = $request->boolean('aktif');
        $data['foto'] = $this->handleImageUpload($request, 'foto');
        $data['jadwal'] = $request->jadwal ? array_values(array_filter($request->jadwal, fn($j) => !empty($j['hari']))) : [];

        Dokter::create($data);
        return redirect()->route('admin.dokter.index')->with('success', 'Dokter berhasil ditambahkan.');
    }

    public function edit(Dokter $dokter)
    {
        return view('admin.dokter.form', compact('dokter'));
    }

    public function update(Request $request, Dokter $dokter)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'spesialisasi' => 'required|string|max:255',
            'foto' => 'nullable|image|max:2048',
            'bio' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['foto', '_token', '_method', 'jadwal']);
        $data['aktif'] = $request->boolean('aktif');
        $data['foto'] = $this->handleImageUpload($request, 'foto', $dokter->foto);
        $data['jadwal'] = $request->jadwal ? array_values(array_filter($request->jadwal, fn($j) => !empty($j['hari']))) : [];

        $dokter->update($data);
        return redirect()->route('admin.dokter.index')->with('success', 'Dokter berhasil diperbarui.');
    }

    public function destroy(Dokter $dokter)
    {
        if ($dokter->foto) Storage::disk('public')->delete($dokter->foto);
        $dokter->delete();
        return redirect()->route('admin.dokter.index')->with('success', 'Dokter berhasil dihapus.');
    }

    public function toggle(Dokter $dokter)
    {
        $dokter->update(['aktif' => !$dokter->aktif]);
        return redirect()->back()->with('success', 'Status dokter berhasil diubah.');
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
