<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pendaftaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class PendaftaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Pendaftaran::latest();

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('layanan')) {
            $query->where('jenis_layanan', $request->layanan);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(function ($sq) use ($q) {
                $sq->where('nama_lengkap', 'like', "%$q%")
                   ->orWhere('kode', 'like', "%$q%")
                   ->orWhere('nik', 'like', "%$q%")
                   ->orWhere('nomor_telepon', 'like', "%$q%");
            });
        }

        $pendaftarans = $query->paginate(15)->withQueryString();

        $stats = [
            'total'        => Pendaftaran::count(),
            'menunggu'     => Pendaftaran::where('status', 'Menunggu')->count(),
            'dikonfirmasi' => Pendaftaran::where('status', 'Dikonfirmasi')->count(),
            'selesai'      => Pendaftaran::where('status', 'Selesai')->count(),
        ];

        return view('admin.pendaftaran.index', compact('pendaftarans', 'stats'));
    }

    public function show(int $id)
    {
        $p = Pendaftaran::findOrFail($id);
        return view('admin.pendaftaran.show', ['p' => $p]);
    }

    public function updateStatus(Request $request, int $id)
    {
        $p = Pendaftaran::findOrFail($id);
        $request->validate([
            'status'        => 'required|in:Menunggu,Dikonfirmasi,Selesai,Dibatalkan',
            'catatan_admin' => 'nullable|string|max:500',
        ]);

        $p->update([
            'status'        => $request->status,
            'catatan_admin' => $request->catatan_admin,
        ]);

        return redirect()->back()->with('success', "Status pendaftaran {$p->kode} diperbarui ke \"{$request->status}\".");
    }

    public function destroy(int $id)
    {
        $p = Pendaftaran::findOrFail($id);
        if ($p->foto_ktp)  Storage::disk('public')->delete($p->foto_ktp);
        if ($p->foto_bpjs) Storage::disk('public')->delete($p->foto_bpjs);
        $p->delete();
        return redirect()->route('admin.pendaftaran.index')->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
