<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Saran;
use Illuminate\Http\Request;

class SaranController extends Controller
{
    public function index(Request $request)
    {
        $query = Saran::query();
        if ($request->search) {
            $query->where('pesan', 'like', '%'.$request->search.'%')
                ->orWhere('tipe', 'like', '%'.$request->search.'%');
        }
        $sarans = $query->latest()->paginate(15)->withQueryString();

        $countLikes = Saran::where('tipe', 'like')->count();
        $countDislikes = Saran::where('tipe', 'dislike')->count();

        return view('admin.saran.index', compact('sarans', 'countLikes', 'countDislikes'));
    }

    public function destroy(int $id)
    {
        $saran = Saran::findOrFail($id);
        $saran->delete();

        return redirect()->route('admin.saran.index')->with('success', 'Saran berhasil dihapus.');
    }
}
