<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LeafletKategori;
use App\Models\LeafletItem;
use Illuminate\Http\Request;

class LeafletController extends Controller
{
    public function index(Request $request)
    {
        $tipe = $request->input('tipe', 'Leaflet');

        $kategoris = LeafletKategori::with('allItems')
            ->when($tipe, fn($q) => $q->tipe($tipe))
            ->orderBy('urutan')
            ->get();

        $countLeaflet = LeafletKategori::tipe('Leaflet')->count();
        $countPoster  = LeafletKategori::tipe('Poster')->count();

        return view('admin.leaflet.index', compact('kategoris', 'tipe', 'countLeaflet', 'countPoster'));
    }

    // =================== KATEGORI ===================

    public function createKategori()
    {
        return view('admin.leaflet.kategori-form', ['kategori' => null]);
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'tipe'   => 'required|in:Leaflet,Poster',
            'nama'   => 'required|string|max:150',
            'urutan' => 'nullable|integer',
        ]);

        LeafletKategori::create([
            'tipe'   => $request->tipe,
            'nama'   => strtoupper(trim($request->nama)),
            'urutan' => $request->urutan ?? 0,
            'aktif'  => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.leaflet.index', ['tipe' => $request->tipe])
                         ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori(int $id)
    {
        $kategori = LeafletKategori::findOrFail($id);
        return view('admin.leaflet.kategori-form', compact('kategori'));
    }

    public function updateKategori(Request $request, int $id)
    {
        $kategori = LeafletKategori::findOrFail($id);
        $request->validate([
            'tipe'   => 'required|in:Leaflet,Poster',
            'nama'   => 'required|string|max:150',
            'urutan' => 'nullable|integer',
        ]);

        $kategori->update([
            'tipe'   => $request->tipe,
            'nama'   => strtoupper(trim($request->nama)),
            'urutan' => $request->urutan ?? 0,
            'aktif'  => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.leaflet.index', ['tipe' => $request->tipe])
                         ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(int $id)
    {
        LeafletKategori::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Kategori dan semua isinya berhasil dihapus.');
    }

    public function toggleKategori(int $id)
    {
        $k = LeafletKategori::findOrFail($id);
        $k->update(['aktif' => !$k->aktif]);
        return redirect()->back()->with('success', 'Status kategori diperbarui.');
    }

    // =================== ITEM ===================

    public function createItem(Request $request)
    {
        $kategoris   = LeafletKategori::orderBy('tipe')->orderBy('urutan')->get();
        $selectedKat = $request->input('kategori_id');
        return view('admin.leaflet.item-form', ['item' => null, 'kategoris' => $kategoris, 'selectedKat' => $selectedKat]);
    }

    public function storeItem(Request $request)
    {
        $request->validate([
            'kategori_id' => 'required|exists:rssite_leaflet_kategoris,id',
            'nama'        => 'required|string|max:255',
            'url_gdrive'  => 'required|url|max:2000',
            'deskripsi'   => 'nullable|string|max:255',
            'urutan'      => 'nullable|integer',
        ]);

        LeafletItem::create([
            'kategori_id' => $request->kategori_id,
            'nama'        => $request->nama,
            'url_gdrive'  => $request->url_gdrive,
            'deskripsi'   => $request->deskripsi,
            'urutan'      => $request->urutan ?? 0,
            'aktif'       => $request->boolean('aktif', true),
        ]);

        $tipe = LeafletKategori::find($request->kategori_id)?->tipe ?? 'Leaflet';
        return redirect()->route('admin.leaflet.index', ['tipe' => $tipe])
                         ->with('success', 'Item berhasil ditambahkan.');
    }

    public function editItem(int $id)
    {
        $item      = LeafletItem::findOrFail($id);
        $kategoris = LeafletKategori::orderBy('tipe')->orderBy('urutan')->get();
        return view('admin.leaflet.item-form', compact('item', 'kategoris'));
    }

    public function updateItem(Request $request, int $id)
    {
        $item = LeafletItem::findOrFail($id);
        $request->validate([
            'kategori_id' => 'required|exists:rssite_leaflet_kategoris,id',
            'nama'        => 'required|string|max:255',
            'url_gdrive'  => 'required|url|max:2000',
            'deskripsi'   => 'nullable|string|max:255',
            'urutan'      => 'nullable|integer',
        ]);

        $item->update([
            'kategori_id' => $request->kategori_id,
            'nama'        => $request->nama,
            'url_gdrive'  => $request->url_gdrive,
            'deskripsi'   => $request->deskripsi,
            'urutan'      => $request->urutan ?? 0,
            'aktif'       => $request->boolean('aktif', true),
        ]);

        $tipe = $item->kategori?->tipe ?? 'Leaflet';
        return redirect()->route('admin.leaflet.index', ['tipe' => $tipe])
                         ->with('success', 'Item berhasil diperbarui.');
    }

    public function destroyItem(int $id)
    {
        LeafletItem::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Item berhasil dihapus.');
    }

    public function toggleItem(int $id)
    {
        $item = LeafletItem::findOrFail($id);
        $item->update(['aktif' => !$item->aktif]);
        return redirect()->back()->with('success', 'Status item diperbarui.');
    }
}
