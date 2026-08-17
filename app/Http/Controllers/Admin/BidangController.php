<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Bidang;
use Illuminate\Http\Request;

class BidangController extends Controller
{
    public function index(Request $request)
    {
        $query = Bidang::query();
        if ($request->search) {
            $query->where('nama', 'like', '%'.$request->search.'%');
        }
        $bidangs = $query->orderBy('urutan')->paginate(10)->withQueryString();

        return view('admin.bidang.index', compact('bidangs'));
    }

    public function create()
    {
        $nextUrutan = Bidang::max('urutan') + 1;
        $bidang = new Bidang(['urutan' => $nextUrutan]);
        return view('admin.bidang.form', compact('bidang'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'nullable|integer',
        ]);

        $data = $request->only(['nama', 'urutan']);
        if (! $request->filled('urutan')) {
            $data['urutan'] = Bidang::max('urutan') + 1;
        }

        Bidang::create($data);

        return redirect()->route('admin.bidang.index')->with('success', 'Bidang berhasil ditambahkan.');
    }

    public function edit(Bidang $bidang)
    {
        return view('admin.bidang.form', compact('bidang'));
    }

    public function update(Request $request, Bidang $bidang)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'urutan' => 'required|integer',
        ]);

        $bidang->update($request->only(['nama', 'urutan']));

        return redirect()->route('admin.bidang.index')->with('success', 'Bidang berhasil diperbarui.');
    }

    public function destroy(Bidang $bidang)
    {
        $bidang->delete();

        return redirect()->route('admin.bidang.index')->with('success', 'Bidang berhasil dihapus.');
    }

    public function reorder(Request $request)
    {
        $request->validate([
            'order' => 'required|array',
        ]);

        foreach ($request->order as $id => $urutan) {
            Bidang::where('id', $id)->update(['urutan' => $urutan]);
        }

        return response()->json(['success' => true]);
    }
}
