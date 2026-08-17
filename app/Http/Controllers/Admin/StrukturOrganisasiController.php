<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use App\Models\StrukturOrganisasi;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StrukturOrganisasiController extends Controller
{
    public function index(Request $request)
    {
        $query = StrukturOrganisasi::with('bidang');

        if ($request->search) {
            $query->where(function ($q) use ($request) {
                $q->where('nama', 'like', '%'.$request->search.'%')
                    ->orWhere('jabatan', 'like', '%'.$request->search.'%')
                    ->orWhere('nip', 'like', '%'.$request->search.'%');
            });
        }

        if ($request->bidang_id) {
            $query->where('bidang_id', $request->bidang_id);
        }

        $strukturs = $query->orderBy('bidang_id')->orderBy('urutan')->paginate(15)->withQueryString();
        $bidangs = Bidang::orderBy('urutan')->get();

        return view('admin.struktur.index', compact('strukturs', 'bidangs'));
    }

    public function create()
    {
        $bidangs = Bidang::orderBy('urutan')->get();
        if ($bidangs->isEmpty()) {
            return redirect()->route('admin.bidang.index')->with('warning', 'Silakan buat Bidang terlebih dahulu sebelum menambahkan struktur organisasi.');
        }

        return view('admin.struktur.form', [
            'struktur' => new StrukturOrganisasi,
            'bidangs' => $bidangs,
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'bidang_id' => 'required|exists:rssite_bidangs,id',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->except(['foto', '_token']);
        $data['aktif'] = $request->boolean('aktif', true);

        if ($request->hasFile('foto')) {
            $data['foto'] = $request->file('foto')->store('rssite/struktur', 'public');
        }

        if (! $request->filled('urutan')) {
            $data['urutan'] = StrukturOrganisasi::where('bidang_id', $request->bidang_id)->max('urutan') + 1;
        }

        StrukturOrganisasi::create($data);

        return redirect()->route('admin.struktur.index')->with('success', 'Anggota struktur organisasi berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $struktur = StrukturOrganisasi::findOrFail($id);
        $bidangs = Bidang::orderBy('urutan')->get();

        return view('admin.struktur.form', compact('struktur', 'bidangs'));
    }

    public function update(Request $request, $id)
    {
        $struktur = StrukturOrganisasi::findOrFail($id);

        $request->validate([
            'bidang_id' => 'required|exists:rssite_bidangs,id',
            'nama' => 'required|string|max:255',
            'jabatan' => 'required|string|max:255',
            'nip' => 'nullable|string|max:100',
            'foto' => 'nullable|image|max:2048',
            'urutan' => 'required|integer',
        ]);

        $data = $request->except(['foto', '_token', '_method']);
        $data['aktif'] = $request->boolean('aktif');

        if ($request->hasFile('foto')) {
            if ($struktur->foto) {
                Storage::disk('public')->delete($struktur->foto);
            }
            $data['foto'] = $request->file('foto')->store('rssite/struktur', 'public');
        }

        $struktur->update($data);

        return redirect()->route('admin.struktur.index')->with('success', 'Anggota struktur organisasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $struktur = StrukturOrganisasi::findOrFail($id);
        if ($struktur->foto) {
            Storage::disk('public')->delete($struktur->foto);
        }
        $struktur->delete();

        return redirect()->route('admin.struktur.index')->with('success', 'Anggota struktur organisasi berhasil dihapus.');
    }

    public function toggle($id)
    {
        $struktur = StrukturOrganisasi::findOrFail($id);
        $struktur->update(['aktif' => ! $struktur->aktif]);

        return redirect()->back()->with('success', 'Status anggota berhasil diubah.');
    }

    public function getNextUrutan(Request $request)
    {
        $request->validate([
            'bidang_id' => 'required|exists:rssite_bidangs,id',
        ]);

        $nextUrutan = StrukturOrganisasi::where('bidang_id', $request->bidang_id)->max('urutan') + 1;

        return response()->json(['urutan' => $nextUrutan]);
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $id => $urutan) {
            StrukturOrganisasi::where('id', $id)->update(['urutan' => $urutan]);
        }

        return response()->json(['success' => true]);
    }
}
