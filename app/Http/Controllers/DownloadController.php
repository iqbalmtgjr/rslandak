<?php

namespace App\Http\Controllers;

use App\Models\Download;
use App\Models\DownloadKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;

class DownloadController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->input('q', '');
        $slug   = $request->input('kategori', '');

        $query = Download::with('kategori')->where('aktif', true);

        if ($slug) {
            $query->whereHas('kategori', fn($q) => $q->where('slug', $slug));
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('judul', 'like', "%$search%")
                  ->orWhere('deskripsi', 'like', "%$search%")
                  ->orWhere('nama_file', 'like', "%$search%");
            });
        }

        $files     = $query->latest()->paginate(12)->withQueryString();
        $kategoris = DownloadKategori::aktif()->orderBy('urutan')->get();
        $totalFile = Download::where('aktif', true)->count();

        return view('download.index', compact('files', 'kategoris', 'search', 'slug', 'totalFile'));
    }

    public function unduh(int $id): StreamedResponse
    {
        $file = Download::where('aktif', true)->findOrFail($id);

        if (!Storage::disk('public')->exists($file->path_file)) {
            abort(404, 'File tidak ditemukan.');
        }

        $file->increment('jumlah_download');

        return Storage::disk('public')->download(
            $file->path_file,
            $file->nama_file,
            ['Content-Type' => $file->mime_type]
        );
    }
}
