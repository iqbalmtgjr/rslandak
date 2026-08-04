<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Download;
use App\Models\DownloadKategori;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class DownloadController extends Controller
{
    private array $allowedExt = [
        'pdf',
        'doc',
        'docx',
        'xls',
        'xlsx',
        'ppt',
        'pptx',
        'jpg',
        'jpeg',
        'png',
        'gif',
        'zip',
        'rar',
    ];
    private int $maxMb = 20;

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'file');

        $files = Download::with('kategori')
            ->when($request->input('cari'), fn($q, $v) => $q->where('judul', 'like', "%$v%"))
            ->when($request->input('kat'),  fn($q, $v) => $q->where('kategori_id', $v))
            ->latest()->paginate(15)->withQueryString();

        $kategoris     = DownloadKategori::withCount('allDownloads')->orderBy('urutan')->get();
        $totalDownload = Download::sum('jumlah_download');

        return view('admin.download.index', compact('files', 'kategoris', 'tab', 'totalDownload'));
    }

    // ======== KATEGORI ========

    public function createKategori()
    {
        return view('admin.download.kategori-form', ['kategori' => null]);
    }

    public function storeKategori(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150|unique:rssite_download_kategoris,nama',
            'ikon'      => 'nullable|string|max:80',
            'warna'     => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer',
        ]);

        DownloadKategori::create([
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'ikon'      => $request->ikon    ?? 'fa-folder',
            'warna'     => $request->warna   ?? '#2563EB',
            'deskripsi' => $request->deskripsi,
            'urutan'    => $request->urutan  ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.download.index', ['tab' => 'kategori'])
            ->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function editKategori(int $id)
    {
        $kategori = DownloadKategori::findOrFail($id);
        return view('admin.download.kategori-form', compact('kategori'));
    }

    public function updateKategori(Request $request, int $id)
    {
        $k = DownloadKategori::findOrFail($id);
        $request->validate([
            'nama'      => 'required|string|max:150|unique:rssite_download_kategoris,nama,' . $id,
            'ikon'      => 'nullable|string|max:80',
            'warna'     => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer',
        ]);

        $k->update([
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'ikon'      => $request->ikon    ?? 'fa-folder',
            'warna'     => $request->warna   ?? '#2563EB',
            'deskripsi' => $request->deskripsi,
            'urutan'    => $request->urutan  ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.download.index', ['tab' => 'kategori'])
            ->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroyKategori(int $id)
    {
        $k = DownloadKategori::withCount('allDownloads')->findOrFail($id);

        if ($k->all_downloads_count > 0) {
            return redirect()->back()->with(
                'error',
                "Kategori \"{$k->nama}\" masih memiliki {$k->all_downloads_count} file. Hapus semua file dalam kategori ini terlebih dahulu."
            );
        }

        $k->delete();
        return redirect()->route('admin.download.index', ['tab' => 'kategori'])
            ->with('success', 'Kategori berhasil dihapus.');
    }

    public function toggleKategori(int $id)
    {
        $k = DownloadKategori::findOrFail($id);
        $k->update(['aktif' => !$k->aktif]);
        return redirect()->back()->with('success', 'Status kategori diperbarui.');
    }

    // ======== FILE ========

    public function createFile(Request $request)
    {
        $kategoris   = DownloadKategori::aktif()->orderBy('urutan')->get();
        $selectedKat = $request->input('kategori_id');
        return view('admin.download.file-form', [
            'file'        => null,
            'kategoris'   => $kategoris,
            'selectedKat' => $selectedKat,
            'allowedExt'  => implode(', ', $this->allowedExt),
            'maxMb'       => $this->maxMb,
        ]);
    }

    public function storeFile(Request $request)
    {
        $extStr = implode(',', $this->allowedExt);
        $request->validate([
            'kategori_id' => 'required|exists:rssite_download_kategoris,id',
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string|max:500',
            'file_upload' => "required|file|mimes:{$extStr}|max:" . ($this->maxMb * 1024),
        ], [
            'file_upload.required' => 'File wajib dipilih.',
            'file_upload.mimes'    => 'Tipe file tidak diizinkan. Diizinkan: ' . $extStr,
            'file_upload.max'      => 'Ukuran file maksimal ' . $this->maxMb . ' MB.',
        ]);

        $up   = $request->file('file_upload');
        $ext  = strtolower($up->getClientOriginalExtension());
        $path = $up->store('rssite/downloads', 'public');

        Download::create([
            'kategori_id'     => $request->kategori_id,
            'judul'           => $request->judul,
            'deskripsi'       => $request->deskripsi,
            'nama_file'       => $up->getClientOriginalName(),
            'path_file'       => $path,
            'tipe_file'       => $ext,
            'mime_type'       => $up->getMimeType(),
            'ukuran_file'     => $up->getSize(),
            'jumlah_download' => 0,
            'aktif'           => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.download.index')
            ->with('success', 'File berhasil diupload.');
    }

    public function editFile(int $id)
    {
        $file      = Download::findOrFail($id);
        $kategoris = DownloadKategori::aktif()->orderBy('urutan')->get();
        return view('admin.download.file-form', [
            'file'        => $file,
            'kategoris'   => $kategoris,
            'selectedKat' => null,
            'allowedExt'  => implode(', ', $this->allowedExt),
            'maxMb'       => $this->maxMb,
        ]);
    }

    public function updateFile(Request $request, int $id)
    {
        $file   = Download::findOrFail($id);
        $extStr = implode(',', $this->allowedExt);

        $request->validate([
            'kategori_id' => 'required|exists:rssite_download_kategoris,id',
            'judul'       => 'required|string|max:255',
            'deskripsi'   => 'nullable|string|max:500',
            'file_upload' => "nullable|file|mimes:{$extStr}|max:" . ($this->maxMb * 1024),
        ]);

        $data = [
            'kategori_id' => $request->kategori_id,
            'judul'       => $request->judul,
            'deskripsi'   => $request->deskripsi,
            'aktif'       => $request->boolean('aktif', true),
        ];

        if ($request->hasFile('file_upload')) {
            Storage::disk('public')->delete($file->path_file);
            $up   = $request->file('file_upload');
            $path = $up->store('rssite/downloads', 'public');
            $data = array_merge($data, [
                'nama_file'   => $up->getClientOriginalName(),
                'path_file'   => $path,
                'tipe_file'   => strtolower($up->getClientOriginalExtension()),
                'mime_type'   => $up->getMimeType(),
                'ukuran_file' => $up->getSize(),
            ]);
        }

        $file->update($data);

        return redirect()->route('admin.download.index')
            ->with('success', 'File berhasil diperbarui.');
    }

    public function destroyFile(int $id)
    {
        $file = Download::findOrFail($id);
        Storage::disk('public')->delete($file->path_file);
        $file->delete();
        return redirect()->back()->with('success', 'File berhasil dihapus.');
    }

    public function toggleFile(int $id)
    {
        $file = Download::findOrFail($id);
        $file->update(['aktif' => !$file->aktif]);
        return redirect()->back()->with('success', 'Status file diperbarui.');
    }
}
