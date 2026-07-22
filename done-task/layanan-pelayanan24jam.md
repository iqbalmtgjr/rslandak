# Task: Layanan — Pelayanan 24 Jam

Tambahkan halaman publik **Pelayanan 24 Jam** sebagai sub-menu ketiga dari dropdown Layanan.
Referensi tampilan: screenshot halaman Pelayanan 24 Jam RSI PKU Muhammadiyah Tegal.

Konteks project: lihat `CLAUDE.md` dan `layanan-poliklinik.md`.

---

## Konsep Halaman

```
/layanan/pelayanan-24-jam
  └── Grid 3 kolom card layanan:
        ├── Foto layanan (bisa upload)
        ├── Nama layanan (UPPERCASE bold)
        └── Deskripsi teks
```

Card layout persis referensi:
- Foto di atas (rounded, shadow)
- Nama layanan uppercase bold di bawah foto
- Deskripsi paragraf kecil
- Border bawah hijau tipis (aksen)

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan                                                        |
|-------------|-------------------------------------------------------------------------|
| Database    | rssite_pelayanan24jams                                                  |
| Model       | Pelayanan24Jam                                                          |
| Routes      | Ganti placeholder /layanan/pelayanan-24-jam                             |
| Controller  | LayananController@pelayanan24Jam — load semua aktif                     |
| Views       | layanan/pelayanan-24-jam.blade.php                                      |
| Admin       | Admin/Pelayanan24JamController + views CRUD                             |
| Sidebar     | Entry "Pelayanan 24 Jam" di grup Konten admin                           |

---

## 1. Migration

```php
Schema::create('rssite_pelayanan24jams', function (Blueprint $table) {
    $table->id();
    $table->string('nama', 150);         // "INSTALASI GAWAT DARURAT (IGD)"
    $table->text('deskripsi');           // paragraf deskripsi layanan
    $table->string('foto', 500)->nullable(); // path storage
    $table->integer('urutan')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

---

## 2. Model: app/Models/Pelayanan24Jam.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Pelayanan24Jam extends Model
{
    protected $table    = 'rssite_pelayanan24jams';
    protected $fillable = ['nama', 'deskripsi', 'foto', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function scopeAktif($q) { return $q->where('aktif', true); }

    public function getFotoUrlAttribute(): ?string
    {
        return $this->foto ? asset('storage/' . $this->foto) : null;
    }
}
```

---

## 3. Routes

Di dalam group `prefix('layanan')`, ganti placeholder pelayanan-24-jam:

```php
Route::get('/pelayanan-24-jam', [LayananController::class, 'pelayanan24Jam'])
     ->name('pelayanan-24-jam');
```

Tambah di group admin:

```php
Route::prefix('pelayanan24jam')->name('pelayanan24jam.')->group(function () {
    Route::get('/',            [Admin\Pelayanan24JamController::class, 'index'])  ->name('index');
    Route::get('/create',      [Admin\Pelayanan24JamController::class, 'create']) ->name('create');
    Route::post('/',           [Admin\Pelayanan24JamController::class, 'store'])  ->name('store');
    Route::get('/{id}/edit',   [Admin\Pelayanan24JamController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',        [Admin\Pelayanan24JamController::class, 'update']) ->name('update');
    Route::delete('/{id}',     [Admin\Pelayanan24JamController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle',[Admin\Pelayanan24JamController::class, 'toggle']) ->name('toggle');
});
```

---

## 4. Update LayananController@pelayanan24Jam

Ganti method placeholder yang sudah ada:

```php
public function pelayanan24Jam()
{
    $layanans = \App\Models\Pelayanan24Jam::aktif()
        ->orderBy('urutan')
        ->get();

    return view('layanan.pelayanan-24-jam', compact('layanans'));
}
```

---

## 5. Admin Controller: app/Http/Controllers/Admin/Pelayanan24JamController.php

```php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Pelayanan24Jam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Pelayanan24JamController extends Controller
{
    public function index()
    {
        $layanans = Pelayanan24Jam::orderBy('urutan')->paginate(15);
        return view('admin.pelayanan24jam.index', compact('layanans'));
    }

    public function create()
    {
        return view('admin.pelayanan24jam.form', ['layanan' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'foto'      => 'nullable|image|max:2048',
            'urutan'    => 'nullable|integer',
        ]);

        $path = $request->hasFile('foto')
            ? $request->file('foto')->store('rssite/pelayanan24jam', 'public')
            : null;

        Pelayanan24Jam::create([
            'nama'      => strtoupper(trim($request->nama)),
            'deskripsi' => $request->deskripsi,
            'foto'      => $path,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.pelayanan24jam.index')
                         ->with('success', 'Layanan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $layanan = Pelayanan24Jam::findOrFail($id);
        return view('admin.pelayanan24jam.form', compact('layanan'));
    }

    public function update(Request $request, int $id)
    {
        $layanan = Pelayanan24Jam::findOrFail($id);
        $request->validate([
            'nama'      => 'required|string|max:150',
            'deskripsi' => 'required|string',
            'foto'      => 'nullable|image|max:2048',
            'urutan'    => 'nullable|integer',
        ]);

        $path = $layanan->foto;
        if ($request->hasFile('foto')) {
            if ($path) Storage::disk('public')->delete($path);
            $path = $request->file('foto')->store('rssite/pelayanan24jam', 'public');
        }

        $layanan->update([
            'nama'      => strtoupper(trim($request->nama)),
            'deskripsi' => $request->deskripsi,
            'foto'      => $path,
            'urutan'    => $request->urutan ?? 0,
            'aktif'     => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.pelayanan24jam.index')
                         ->with('success', 'Layanan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $layanan = Pelayanan24Jam::findOrFail($id);
        if ($layanan->foto) Storage::disk('public')->delete($layanan->foto);
        $layanan->delete();
        return redirect()->back()->with('success', 'Layanan berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $layanan = Pelayanan24Jam::findOrFail($id);
        $layanan->update(['aktif' => !$layanan->aktif]);
        return redirect()->back()->with('success', 'Status diperbarui.');
    }
}
```

---

## 6. View Publik: resources/views/layanan/pelayanan-24-jam.blade.php

```blade
@extends('layouts.app')
@section('title', 'Pelayanan 24 Jam — RS TK IV Sintang')
@section('content')

@include('partials.page-header', ['judul' => 'Pelayanan 24 Jam', 'parent' => 'Layanan'])

<section class="py-14 bg-white min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl">

    @if($layanans->isEmpty())
      <div class="text-center py-24 text-gray-400">
        <i class="fa fa-clock text-6xl mb-4 block"></i>
        <p class="text-lg">Informasi layanan sedang diperbarui.</p>
      </div>
    @else

      {{-- Grid 3 kolom persis referensi --}}
      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-7">
        @foreach($layanans as $item)

          <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg
                      transition-all duration-300 hover:-translate-y-0.5
                      overflow-hidden border border-gray-100 group reveal">

            {{-- FOTO (atas card) --}}
            <div class="overflow-hidden h-52 bg-gray-100">
              @if($item->foto_url)
                <img src="{{ $item->foto_url }}"
                     alt="{{ $item->nama }}"
                     class="w-full h-full object-cover
                            group-hover:scale-105 transition-transform duration-500">
              @else
                {{-- Gradient placeholder --}}
                <div class="w-full h-full flex items-center justify-center
                            bg-gradient-to-br from-green-700 to-green-500">
                  <i class="fa fa-procedures text-6xl text-white opacity-30"></i>
                </div>
              @endif
            </div>

            {{-- KONTEN (bawah foto) --}}
            <div class="p-6 relative">

              {{-- Garis aksen hijau bawah (seperti referensi) --}}
              <div class="absolute bottom-0 left-0 right-0 h-1
                          bg-gradient-to-r from-green-700 to-green-400
                          rounded-b-2xl"></div>

              {{-- Nama layanan UPPERCASE bold --}}
              <h3 class="font-bold text-gray-800 text-sm uppercase tracking-wide
                         leading-snug mb-3 group-hover:text-green-700 transition-colors">
                {{ $item->nama }}
              </h3>

              {{-- Deskripsi --}}
              <p class="text-gray-500 text-sm leading-relaxed line-clamp-4">
                {{ $item->deskripsi }}
              </p>

            </div>
          </div>

        @endforeach
      </div>

    @endif

  </div>
</section>

@endsection
```

---

## 7. Views Admin

### admin/pelayanan24jam/index.blade.php

```
@extends('layouts.admin')
@section('title', 'Kelola Pelayanan 24 Jam')
@section('content')

HEADER: Judul + tombol [+ Tambah Layanan]

TABEL (rounded-xl shadow overflow-hidden):
Header: # | Foto | Nama | Urutan | Status | Aksi

Per baris:
- Foto thumbnail 60x45 object-cover rounded-lg (atau ikon placeholder)
- Nama (font-medium)
- Urutan
- Toggle aktif
- Aksi: [Edit] [Hapus confirm]

Pagination

@endsection
```

### admin/pelayanan24jam/form.blade.php

```
@extends('layouts.admin')
@section('title', $layanan ? 'Edit Layanan' : 'Tambah Layanan 24 Jam')
@section('content')

Card max-w-2xl mx-auto:
Form enctype="multipart/form-data" POST/PUT:

1. Nama Layanan* (text)
   placeholder: "Instalasi Gawat Darurat (IGD)"
   hint: "Akan ditampilkan dalam huruf kapital semua"

2. Upload Foto (image, maks 2MB)
   - Preview existing jika edit (img src Storage::url)
   - Preview baru via FileReader
   - Hint: "Rekomendasi rasio 4:3, maks 2MB"

3. Deskripsi* (textarea rows=5)
   placeholder: "Deskripsikan layanan ini..."

4. Urutan (number)

5. Status aktif (toggle)

Tombol: [Simpan] [Batal]

@endsection
```

---

## 8. Seeder

```php
$pelayanan24Jams = [
    [
        'nama'      => 'INSTALASI GAWAT DARURAT (IGD)',
        'deskripsi' => 'Instalasi gawat darurat RS TK IV Sintang merupakan bagian dari unit pelayanan 24 jam yang memiliki sumber daya manusia profesional yang terlatih dan bersertifikat. Didukung oleh dokter jaga bersertifikat dan perawat terampil siaga penuh.',
        'urutan'    => 1,
    ],
    [
        'nama'      => 'FARMASI',
        'deskripsi' => 'Instalasi farmasi RS TK IV Sintang buka 24 jam melayani kebutuhan obat pasien rawat inap dan rawat jalan. Dikelola oleh tenaga apoteker, asisten apoteker, dan administrasi yang profesional.',
        'urutan'    => 2,
    ],
    [
        'nama'      => 'LABORATORIUM',
        'deskripsi' => 'Laboratorium RS TK IV Sintang dilengkapi alat-alat auto analyzer untuk pemeriksaan hematologi dan kimia klinik. Layanan laboratorium buka 24 jam untuk mendukung diagnosis yang cepat dan akurat.',
        'urutan'    => 3,
    ],
    [
        'nama'      => 'RADIOLOGI',
        'deskripsi' => 'Instalasi Radiologi RS TK IV Sintang melayani rontgen, USG, dan berbagai pemeriksaan pencitraan medis lainnya. Didukung tenaga radiografer berpengalaman dan peralatan modern.',
        'urutan'    => 4,
    ],
    [
        'nama'      => 'BANK DARAH',
        'deskripsi' => 'Bank darah RS TK IV Sintang siap memenuhi kebutuhan darah untuk transfusi pasien rawat inap maupun pasien yang dirujuk. Bekerja sama dengan PMI setempat untuk ketersediaan stok darah.',
        'urutan'    => 5,
    ],
    [
        'nama'      => 'AMBULANS',
        'deskripsi' => 'Pelayanan ambulans RS TK IV Sintang beroperasi 24 jam untuk antar jemput pasien. Dilengkapi peralatan medis standar dan dikemudikan oleh tenaga supir yang handal dan berpengalaman.',
        'urutan'    => 6,
    ],
];

foreach ($pelayanan24Jams as $p) {
    \App\Models\Pelayanan24Jam::firstOrCreate(
        ['nama' => $p['nama']],
        array_merge($p, ['aktif' => true])
    );
}
```

---

## 9. Sidebar Admin

Di `layouts/admin.blade.php`, tambahkan di grup Konten:

```blade
<a href="{{ route('admin.pelayanan24jam.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.pelayanan24jam.*') ? 'active' : '' }}">
  <i class="fa fa-clock w-5"></i>
  <span>Pelayanan 24 Jam</span>
</a>
```

---

## Checklist Akhir

- [ ] Migration rssite_pelayanan24jams berjalan
- [ ] Seeder membuat 6 layanan default
- [ ] Route /layanan/pelayanan-24-jam HTTP 200
- [ ] Grid 3 kolom desktop, 2 tablet, 1 mobile
- [ ] Foto tampil dari storage, gradient placeholder jika kosong
- [ ] Nama layanan UPPERCASE bold
- [ ] Garis aksen hijau di bawah setiap card
- [ ] Hover: shadow + scale foto + nama hijau
- [ ] line-clamp-4 deskripsi tidak overflow
- [ ] Admin index: tabel dengan thumbnail, nama, toggle, aksi
- [ ] Admin form: upload foto + preview + validasi 2MB
- [ ] Foto lama terhapus dari storage saat diganti
- [ ] Nama auto-uppercase saat disimpan
- [ ] Hapus: foto fisik ikut terhapus
- [ ] Navbar dropdown "Pelayanan 24 Jam" → route benar
- [ ] Sidebar admin highlight saat aktif
