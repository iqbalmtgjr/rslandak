# Task: Halaman Download — Upload File, Kategori, Search & Admin CRUD

Tambahkan halaman publik **Download** ke project website RS TK IV Sintang.
Berbeda dengan Leaflet (link Google Drive), halaman ini admin **upload file langsung**
ke server (PDF, DOCX, XLSX, JPG, PNG, ZIP) dan visitor bisa download langsung dari website.

Konteks project: lihat `CLAUDE.md`. Stack: Laravel 10, MySQL, Tailwind CDN, Alpine.js.

---

## Konsep Halaman

Struktur:
- Publik /download
  - Search box (cari nama file)
  - Sidebar Kategori (filter)
  - Grid card file (ikon tipe, nama, ukuran, jumlah download, tombol Download)
- Admin /admin/download
  - Tab: File | Kategori
  - CRUD Kategori (nama, ikon FA, warna, deskripsi, urutan)
  - CRUD File (upload, edit meta, hapus — file fisik ikut terhapus)

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan                                                         |
|-------------|--------------------------------------------------------------------------|
| Database    | rssite_download_kategoris, rssite_downloads                              |
| Models      | DownloadKategori, Download                                               |
| Routes      | /download (publik) + /admin/download (admin CRUD)                        |
| Controllers | DownloadController (publik) + Admin/DownloadController (admin)           |
| Views       | download/index.blade.php + admin views                                   |
| Storage     | File disimpan di storage/app/public/rssite/downloads/                    |
| Navbar      | Link "Download" langsung                                                 |
| Sidebar     | Entry baru di grup Konten                                                |

---

## 1. Database Migrations

### Tabel: rssite_download_kategoris

```php
Schema::create('rssite_download_kategoris', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 150);
    $table->string('slug', 150)->unique();
    $table->string('ikon', 80)->default('fa-folder');
    $table->string('warna', 20)->default('#2D6A4F');
    $table->text('deskripsi')->nullable();
    $table->integer('urutan')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

### Tabel: rssite_downloads

```php
Schema::create('rssite_downloads', function (Blueprint $table) {
    $table->id();
    $table->foreignId('kategori_id')
          ->constrained('rssite_download_kategoris')
          ->onDelete('restrict');
    $table->string('judul', 255);
    $table->string('deskripsi', 500)->nullable();
    $table->string('nama_file', 255);
    $table->string('path_file', 500);
    $table->string('tipe_file', 20);
    $table->string('mime_type', 100);
    $table->unsignedBigInteger('ukuran_file');
    $table->unsignedInteger('jumlah_download')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

---

## 2. Models

### app/Models/DownloadKategori.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class DownloadKategori extends Model
{
    protected $table    = 'rssite_download_kategoris';
    protected $fillable = ['nama','slug','ikon','warna','deskripsi','urutan','aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    protected static function boot()
    {
        parent::boot();
        static::creating(function ($m) {
            if (empty($m->slug)) $m->slug = Str::slug($m->nama);
        });
    }

    public function downloads()
    {
        return $this->hasMany(Download::class, 'kategori_id')
                    ->where('aktif', true)->latest();
    }

    public function allDownloads()
    {
        return $this->hasMany(Download::class, 'kategori_id')->latest();
    }

    public function scopeAktif($q) { return $q->where('aktif', true); }

    public function getJumlahFileAttribute(): int
    {
        return $this->downloads()->count();
    }
}
```

### app/Models/Download.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Download extends Model
{
    protected $table    = 'rssite_downloads';
    protected $fillable = [
        'kategori_id','judul','deskripsi','nama_file',
        'path_file','tipe_file','mime_type','ukuran_file',
        'jumlah_download','aktif',
    ];
    protected $casts = ['aktif' => 'boolean'];

    public function kategori()
    {
        return $this->belongsTo(DownloadKategori::class, 'kategori_id');
    }

    // URL route download (increment counter)
    public function getUrlDownloadAttribute(): string
    {
        return route('download.unduh', $this->id);
    }

    // Human-readable ukuran file
    public function getUkuranReadableAttribute(): string
    {
        $b = $this->ukuran_file;
        if ($b >= 1048576) return round($b / 1048576, 1) . ' MB';
        if ($b >= 1024)    return round($b / 1024) . ' KB';
        return $b . ' B';
    }

    // FA icon class berdasarkan tipe_file
    public function getIkonFileAttribute(): string
    {
        return match ($this->tipe_file) {
            'pdf'              => 'fa-file-pdf',
            'doc','docx'       => 'fa-file-word',
            'xls','xlsx'       => 'fa-file-excel',
            'ppt','pptx'       => 'fa-file-powerpoint',
            'jpg','jpeg','png',
            'gif','webp'       => 'fa-file-image',
            'zip','rar','7z'   => 'fa-file-archive',
            default            => 'fa-file-alt',
        };
    }

    // Warna teks Tailwind
    public function getWarnaIkonAttribute(): string
    {
        return match ($this->tipe_file) {
            'pdf'              => 'text-red-500',
            'doc','docx'       => 'text-blue-600',
            'xls','xlsx'       => 'text-green-600',
            'ppt','pptx'       => 'text-orange-500',
            'jpg','jpeg','png',
            'gif','webp'       => 'text-purple-500',
            'zip','rar','7z'   => 'text-yellow-600',
            default            => 'text-gray-500',
        };
    }

    // Warna background Tailwind
    public function getBgIkonAttribute(): string
    {
        return match ($this->tipe_file) {
            'pdf'              => 'bg-red-50',
            'doc','docx'       => 'bg-blue-50',
            'xls','xlsx'       => 'bg-green-50',
            'ppt','pptx'       => 'bg-orange-50',
            'jpg','jpeg','png',
            'gif','webp'       => 'bg-purple-50',
            'zip','rar','7z'   => 'bg-yellow-50',
            default            => 'bg-gray-50',
        };
    }
}
```

---

## 3. Routes (routes/web.php)

Tambahkan di bawah route publik:

```php
Route::get('/download',             [DownloadController::class, 'index'])->name('download.index');
Route::get('/download/{id}/unduh',  [DownloadController::class, 'unduh'])->name('download.unduh');
```

Tambahkan di dalam group admin (middleware auth):

```php
Route::prefix('download')->name('download.')->group(function () {
    Route::get('/',                      [Admin\DownloadController::class, 'index'])          ->name('index');
    Route::get('/kategori/create',       [Admin\DownloadController::class, 'createKategori']) ->name('kategori.create');
    Route::post('/kategori',             [Admin\DownloadController::class, 'storeKategori'])  ->name('kategori.store');
    Route::get('/kategori/{id}/edit',    [Admin\DownloadController::class, 'editKategori'])   ->name('kategori.edit');
    Route::put('/kategori/{id}',         [Admin\DownloadController::class, 'updateKategori']) ->name('kategori.update');
    Route::delete('/kategori/{id}',      [Admin\DownloadController::class, 'destroyKategori'])->name('kategori.destroy');
    Route::post('/kategori/{id}/toggle', [Admin\DownloadController::class, 'toggleKategori']) ->name('kategori.toggle');
    Route::get('/file/create',           [Admin\DownloadController::class, 'createFile'])     ->name('file.create');
    Route::post('/file',                 [Admin\DownloadController::class, 'storeFile'])      ->name('file.store');
    Route::get('/file/{id}/edit',        [Admin\DownloadController::class, 'editFile'])       ->name('file.edit');
    Route::put('/file/{id}',             [Admin\DownloadController::class, 'updateFile'])     ->name('file.update');
    Route::delete('/file/{id}',          [Admin\DownloadController::class, 'destroyFile'])    ->name('file.destroy');
    Route::post('/file/{id}/toggle',     [Admin\DownloadController::class, 'toggleFile'])     ->name('file.toggle');
});
```

---

## 4. Controllers

### app/Http/Controllers/DownloadController.php (Publik)

```php
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
        $search   = $request->input('q', '');
        $slug     = $request->input('kategori', '');

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

        return view('download.index', compact('files','kategoris','search','slug','totalFile'));
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
```

### app/Http/Controllers/Admin/DownloadController.php (Admin)

```php
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
        'pdf','doc','docx','xls','xlsx','ppt','pptx',
        'jpg','jpeg','png','gif','zip','rar',
    ];
    private int $maxMb = 20;

    public function index(Request $request)
    {
        $tab = $request->input('tab', 'file');

        $files = Download::with('kategori')
            ->when($request->input('cari'), fn($q,$v) => $q->where('judul','like',"%$v%"))
            ->when($request->input('kat'),  fn($q,$v) => $q->where('kategori_id',$v))
            ->latest()->paginate(15)->withQueryString();

        $kategoris     = DownloadKategori::withCount('allDownloads')->orderBy('urutan')->get();
        $totalDownload = Download::sum('jumlah_download');

        return view('admin.download.index', compact('files','kategoris','tab','totalDownload'));
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
            'warna'     => $request->warna   ?? '#2D6A4F',
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
            'nama'      => 'required|string|max:150|unique:rssite_download_kategoris,nama,'.$id,
            'ikon'      => 'nullable|string|max:80',
            'warna'     => 'nullable|string|max:20',
            'deskripsi' => 'nullable|string|max:500',
            'urutan'    => 'nullable|integer',
        ]);

        $k->update([
            'nama'      => $request->nama,
            'slug'      => Str::slug($request->nama),
            'ikon'      => $request->ikon    ?? 'fa-folder',
            'warna'     => $request->warna   ?? '#2D6A4F',
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
            return redirect()->back()->with('error',
                "Kategori \"{$k->nama}\" masih memiliki {$k->all_downloads_count} file. Hapus semua file dalam kategori ini terlebih dahulu.");
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
            'file_upload' => "required|file|mimes:{$extStr}|max:".($this->maxMb * 1024),
        ], [
            'file_upload.required' => 'File wajib dipilih.',
            'file_upload.mimes'    => 'Tipe file tidak diizinkan. Diizinkan: '.$extStr,
            'file_upload.max'      => 'Ukuran file maksimal '.$this->maxMb.' MB.',
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
            'file_upload' => "nullable|file|mimes:{$extStr}|max:".($this->maxMb * 1024),
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
```

---

## 5. View Publik: resources/views/download/index.blade.php

Buat file ini dengan struktur berikut:

```
@extends('layouts.app')
@section('title', 'Download — RS TK IV Sintang')
@section('content')

[Page Header: "Download"]

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-7xl">

    ===== SEARCH + STATS BAR (bg-white rounded-2xl shadow-sm p-5 mb-8) =====

    Flex row (mobile: kolom):
    KIRI — Form search:
      - Input text name="q" placeholder="Cari nama file atau dokumen..."
        dengan ikon fa-search di kiri
      - Hidden input name="kategori" jika $slug ada
      - Tombol [Cari] hijau
      - Link [Reset] jika $search atau $slug aktif

    KANAN — Stats:
      - Ikon fa-file-alt text-green-600
      - "{{ $totalFile }} file tersedia"
      - Jika filter aktif: "| {{ $files->total() }} hasil ditemukan" text-green-700

    ===== LAYOUT 2 KOLOM: Sidebar (1/4) + Konten (3/4) =====

    ===== SIDEBAR KIRI (sticky top-24) =====
    bg-white rounded-2xl shadow-sm p-5:

    Judul "Kategori" dengan border-b-2 border-green-700

    List link navigasi:
    - "Semua File" → route('download.index', ['q' => $search])
      badge jumlah: $totalFile
      active: !$slug → bg-green-700 text-white
    - Per $kat in $kategoris:
      → route('download.index', ['kategori' => $kat->slug, 'q' => $search])
      badge jumlah: $kat->jumlah_file
      ikon: <i class="fa {{ $kat->ikon }}" style="color: {{ $kat->warna }}">
      active: $slug === $kat->slug → bg-green-700 text-white

    ===== KONTEN UTAMA =====

    JIKA $files->isEmpty():
      Empty state card (bg-white rounded-2xl py-24 text-center):
      - Ikon fa-folder-open text-6xl text-gray-200
      - "File tidak ditemukan" atau "Belum ada file tersedia"
      - Jika $search: tampilkan kata kunci + link reset

    JIKA ada file:

      Info filter aktif (jika $search atau $slug):
      - Badge kategori aktif (bg-green-100 text-green-700)
      - Teks pencarian jika ada

      GRID sm:grid-cols-2 xl:grid-cols-3 gap-5:
      @foreach($files as $item)

        Card (bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-0.5 transition-all group):

        BODY (p-5):
        - Row atas: kotak ikon (w-14 h-14 rounded-xl bg-ikon) + badge tipe (.pdf, .docx, dll)
        - Kategori kecil: fa-folder + $item->kategori->nama
        - Judul (font-semibold text-sm line-clamp-2 group-hover:text-green-700)
        - Deskripsi (text-xs text-gray-400 line-clamp-2) — jika ada
        - Meta row (text-xs text-gray-400):
            fa-hdd + ukuran_readable
            fa-download + jumlah_download + "x"
            tanggal created_at->format('d/m/Y') — ml-auto

        FOOTER card (px-5 pb-5):
        - Tombol full-width "Download" (bg-green-700 rounded-xl)
          href="{{ $item->url_download }}"
          fa-download ikon + teks "Download"

      @endforeach

      PAGINATION (mt-10 flex flex-col items-center gap-3):
      Gunakan pola elipsis yang sama dengan berita.md:
      - Tombol « prev, angka halaman (elipsis jika > 7), angka » next
      - Teks "Menampilkan X–Y dari Z file" di bawah pagination

  </div>
</section>

@endsection
```

---

## 6. Views Admin

### admin/download/index.blade.php

```
@extends('layouts.admin')
@section('title', 'Kelola Download')
@section('content')

HEADER:
- Judul "Kelola Download"
- Tombol kanan: [Upload File] primary + [Tambah Kategori] secondary

STATS ROW (3 card):
- Total File (Download::count()) — ikon fa-file-alt hijau
- Total Download (Download::sum('jumlah_download')) — ikon fa-download biru
- Kategori Aktif (DownloadKategori::aktif()->count()) — ikon fa-folder emas

TAB SWITCHER (?tab=file / ?tab=kategori):
Tabs: [ File ] [ Kategori ]

======== TAB FILE ========

Filter bar:
- Select kategori (loop $kategoris)
- Input search name="cari" value="{{ request('cari') }}"
- Tombol [Filter] + [Reset jika ada filter]

Tabel (rounded-xl overflow-hidden shadow-sm):
Header: # | Tipe | Judul & File | Kategori | Ukuran | Download | Status | Tgl | Aksi

Per baris:
- Nomor urut
- Kotak ikon: w-10 h-10 rounded-lg + ikon FA + warna accessor
- Judul (font-medium) + nama_file (text-xs text-gray-400 di bawah)
- Badge kategori (warna sesuai ikon kategori)
- ukuran_readable
- Badge biru jumlah_download
- Toggle aktif: form POST toggle
- Tanggal d/m/Y
- Aksi: [Edit] [Hapus confirm]

Footer tabel: total baris + pagination

======== TAB KATEGORI ========

Tombol "+ Tambah Kategori" di kanan atas section

Grid 3 kolom card per kategori:
Header card (flex between):
- Kiri: kotak ikon (warna $kat->warna, ikon $kat->ikon) + nama bold + badge jumlah file
- Kanan: toggle aktif pill

Body card:
- Deskripsi (text-sm text-gray-500 jika ada)
- Urutan: "Urutan: {{ $kat->urutan }}"

Footer card (flex gap-2):
- [Edit] → route admin.download.kategori.edit
- [Toggle]
- [Hapus] → confirm JS:
  Jika $kat->all_downloads_count > 0:
    disabled + title="Hapus semua file kategori ini dulu"
  Else:
    form DELETE normal

@endsection
```

---

### admin/download/kategori-form.blade.php

```
@extends('layouts.admin')
@section('title', $kategori ? 'Edit Kategori' : 'Tambah Kategori')
@section('content')

Card max-w-lg mx-auto bg-white rounded-2xl shadow-sm p-8:

Form POST (create) / PUT (edit):

1. Nama Kategori* (text)
   placeholder: "Formulir Pendaftaran"

2. Ikon Font Awesome (text + live preview Alpine.js)
   x-data="{ ikon: '{{ $kategori->ikon ?? 'fa-folder' }}' }"
   - Input: x-model="ikon" placeholder="fa-folder"
   - Preview: <i :class="'fa ' + ikon" class="text-3xl ml-3">
   - Link kecil: "Cari ikon di fontawesome.com"

3. Warna Ikon (color picker + input hex sync)
   x-data="{ warna: '{{ $kategori->warna ?? '#2D6A4F' }}' }"
   - <input type="color" x-model="warna">
   - <input type="text" x-model="warna" maxlength="7" placeholder="#2D6A4F">
   — keduanya x-model ke variabel warna yang sama → auto sync

4. Deskripsi (textarea rows=2 nullable)

5. Urutan (number default 0)

6. Status (toggle checkbox aktif)

Tombol: [Simpan] [Batal → route admin.download.index tab=kategori]

@endsection
```

---

### admin/download/file-form.blade.php

```
@extends('layouts.admin')
@section('title', $file ? 'Edit File' : 'Upload File')
@section('content')

Card max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-8:

JIKA EDIT ($file tidak null):
Alert box (bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6):
  "File saat ini: {{ $file->nama_file }} ({{ $file->ukuran_readable }})"
  Link [Buka File] → route('download.unduh', $file->id) target="_blank"
  "Kosongkan field upload di bawah jika tidak ingin mengganti file."

Form enctype="multipart/form-data" POST/PUT:

1. Kategori* (select dengan ikon):
   @foreach($kategoris as $k)
     <option value="{{ $k->id }}" {{ selected }}>
       {{ $k->nama }}
     </option>
   @endforeach

2. Judul Tampilan* (text)
   placeholder: "Formulir Pendaftaran Rawat Inap"
   hint: "Nama yang ditampilkan kepada pengunjung website"

3. Upload File (WAJIB saat create, OPSIONAL saat edit):
   x-data="{ fileName: '', fileSize: '', dragOver: false }"

   Drag & drop area:
   <div class="border-2 border-dashed rounded-xl p-10 text-center transition-colors cursor-pointer"
        :class="dragOver ? 'border-green-500 bg-green-50' : 'border-gray-300 hover:border-green-400'"
        @dragover.prevent="dragOver = true"
        @dragleave="dragOver = false"
        @drop.prevent="dragOver = false; handleDrop($event)">

     <i class="fa fa-cloud-upload-alt text-5xl text-gray-300 mb-3 block"
        :class="fileName ? 'text-green-500' : 'text-gray-300'"></i>

     <template x-if="!fileName">
       <div>
         <p class="text-sm text-gray-500 mb-1">Drag & drop file di sini atau</p>
         <label class="cursor-pointer text-green-700 font-semibold hover:underline">
           Pilih File
           <input type="file" name="file_upload" class="hidden"
                  @change="onFileChange($event)"
                  accept=".{{ implode(',.', explode(', ', $allowedExt)) }}">
         </label>
         <p class="text-xs text-gray-400 mt-2">
           Diizinkan: {{ $allowedExt }}
         </p>
         <p class="text-xs text-gray-400">Ukuran maksimal: {{ $maxMb }} MB</p>
       </div>
     </template>

     <template x-if="fileName">
       <div>
         <p class="text-green-700 font-semibold text-sm" x-text="'✓ ' + fileName"></p>
         <p class="text-gray-400 text-xs mt-1" x-text="fileSize"></p>
         <button type="button" @click="fileName=''; fileSize=''"
                 class="mt-2 text-xs text-red-500 hover:underline">
           Ganti file
         </button>
       </div>
     </template>

   </div>

   Alpine.js methods (inline x-init atau script):
   function onFileChange(e) {
     const f = e.target.files[0];
     if (!f) return;
     this.fileName = f.name;
     const mb = f.size / 1048576;
     this.fileSize = mb >= 1 ? mb.toFixed(1) + ' MB' : Math.round(f.size/1024) + ' KB';
   }

   @error('file_upload')
     <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
   @enderror

4. Deskripsi (textarea rows=2 nullable)
   placeholder: "Keterangan singkat tentang file ini"

5. Status aktif (toggle)

TOMBOL:
- Saat create: [Upload & Simpan] primary
- Saat edit:   [Simpan Perubahan] primary
- [Batal → route admin.download.index]

@endsection
```

---

## 7. Seeder (DatabaseSeeder.php)

```php
// Download Kategoris — seed struktur awal
$downloadKategoris = [
    ['nama' => 'Formulir',        'ikon' => 'fa-clipboard',     'warna' => '#2D6A4F', 'urutan' => 1],
    ['nama' => 'Regulasi & SK',   'ikon' => 'fa-file-contract', 'warna' => '#C9A84C', 'urutan' => 2],
    ['nama' => 'Panduan Pasien',  'ikon' => 'fa-book-medical',  'warna' => '#3B82F6', 'urutan' => 3],
    ['nama' => 'Laporan Tahunan', 'ikon' => 'fa-chart-bar',     'warna' => '#8B5CF6', 'urutan' => 4],
];

foreach ($downloadKategoris as $kd) {
    \App\Models\DownloadKategori::firstOrCreate(
        ['nama' => $kd['nama']],
        array_merge($kd, [
            'slug'  => \Illuminate\Support\Str::slug($kd['nama']),
            'aktif' => true,
        ])
    );
}

// CATATAN: File tidak di-seed otomatis karena butuh file fisik.
// Admin upload melalui /admin/download setelah setup selesai.
```

---

## 8. Update Navbar & Sidebar

### Navbar publik (partials/navbar.blade.php):

```blade
<a href="{{ route('download.index') }}"
   class="font-medium transition-colors
          {{ request()->routeIs('download.*')
             ? 'text-green-700 font-semibold'
             : 'text-gray-700 hover:text-green-700' }}">
  Download
</a>
```

### Sidebar admin (layouts/admin.blade.php):

```blade
<a href="{{ route('admin.download.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.download.*') ? 'active' : '' }}">
  <i class="fa fa-download w-5"></i>
  <span>Download</span>
</a>
```

---

## 9. Storage & Security

### Jalankan setelah setup:
```bash
php artisan storage:link
```

### Proteksi file:
Semua download melewati route `/download/{id}/unduh` (bukan URL storage langsung).
Ini memastikan:
- Counter jumlah_download terupdate setiap download
- File yang non-aktif tidak bisa diakses visitor

### Tipe file diblokir:
Validasi `mimes` di Laravel hanya izinkan ekstensi yang aman.
File berbahaya (.php, .exe, .sh, .js, dll) otomatis ditolak.

---

## 10. CSS Tambahan (layouts/app.blade.php)

Tambahkan ke blok style:

```css
/* Download button micro-animation */
@keyframes bounce-down {
  0%, 100% { transform: translateY(0); }
  50%       { transform: translateY(3px); }
}
.group:hover .fa-download {
  animation: bounce-down 0.6s ease infinite;
}

/* Sidebar link aktif */
.sidebar-kat-active {
  background: #2D6A4F;
  color: white;
}
```

---

## Checklist Akhir

**Database & Model**
- [ ] Migration rssite_download_kategoris berjalan
- [ ] Migration rssite_downloads berjalan, FK restrict (bukan cascade)
- [ ] Accessor ukuran_readable: "1.2 MB" / "345 KB" / "800 B"
- [ ] Accessor ikon_file: FA class per ekstensi
- [ ] Accessor warna_ikon + bg_ikon: Tailwind class per ekstensi
- [ ] Accessor url_download: route download.unduh/{id}
- [ ] Seeder buat 4 kategori default

**Halaman Publik /download**
- [ ] HTTP 200 accessible
- [ ] Layout 2 kolom (sidebar + grid) di desktop
- [ ] Single kolom di mobile (sidebar di atas, grid di bawah)
- [ ] Search by judul/deskripsi/nama_file berfungsi
- [ ] Filter sidebar kategori berfungsi + highlight aktif
- [ ] Jumlah file per kategori tampil akurat di sidebar
- [ ] Search + filter bisa dikombinasikan
- [ ] Tombol Reset hapus semua filter
- [ ] Grid card: ikon warna-warni per tipe file
- [ ] Badge tipe file (.pdf, .docx, dll) di pojok card
- [ ] Ukuran file readable tampil
- [ ] Counter download tampil
- [ ] Tanggal upload tampil
- [ ] Tombol Download trigger file download ke browser (bukan buka tab baru)
- [ ] Counter jumlah_download increment setiap klik
- [ ] File non-aktif: route unduh kembalikan 404
- [ ] Empty state tampil jika tidak ada hasil
- [ ] Pagination 12 per halaman dengan elipsis
- [ ] Teks "Menampilkan X–Y dari Z file"

**Admin Panel /admin/download**
- [ ] HTTP 200, auth protected
- [ ] Stats row: total file, total download, kategori aktif
- [ ] Tab File dan Kategori bisa switch via ?tab=
- [ ] Filter kategori + search di tabel file berfungsi
- [ ] Ikon + warna tipe file tampil di tabel
- [ ] Toggle aktif file berfungsi
- [ ] Upload file: tersimpan di storage/app/public/rssite/downloads/
- [ ] Auto-detect tipe, mime, ukuran saat upload
- [ ] Validasi ekstensi: file berbahaya (.php, .exe) ditolak
- [ ] Validasi ukuran maks 20 MB dengan pesan yang jelas
- [ ] Edit file tanpa ganti file: hanya ubah judul/kategori/deskripsi
- [ ] Edit file dengan ganti file: file lama terhapus dari storage
- [ ] Hapus file: file fisik terhapus dari storage
- [ ] Drag & drop area berfungsi atau minimal click browse
- [ ] Preview nama + ukuran file setelah pilih (sebelum submit)
- [ ] Form kategori: live preview ikon FA saat ketik
- [ ] Color picker sync dengan input hex
- [ ] Hapus kategori yang ada file: ditolak + pesan error jelas
- [ ] Hapus kategori kosong: berhasil
- [ ] Flash messages sukses/error tampil
- [ ] Navbar "Download" highlight saat aktif
- [ ] Sidebar admin "Download" highlight saat aktif
