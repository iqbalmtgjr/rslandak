# Task: Menu Informasi — Alur Pelayanan (Dinamis, Upload Gambar)

Tambahkan menu **Informasi** dengan dropdown ke website RS TK IV Sintang.
Sub-menu pertama: **Alur Pelayanan** — menampilkan daftar alur pelayanan yang
admin bisa tambah/edit/hapus secara dinamis (nama + gambar upload).

Referensi: screenshot halaman Alur Pelayanan RSI PKU Muhammadiyah Tegal.

Konteks project: lihat `CLAUDE.md`. Stack: Laravel 10, MySQL, Tailwind CDN, Alpine.js.

---

## Konsep Halaman

```
Navbar → Informasi (dropdown)
  └── Alur Pelayanan   ← task ini

/informasi/alur-pelayanan
  ├── Judul "Alur Pelayanan"
  └── Daftar item (dinamis dari DB):
        ├── Sub-judul item (border kiri hijau, e.g. "Alur Pendaftaran Pasien Lama")
        └── Gambar alur (full-width, klik untuk zoom/lightbox)
```

Setiap item alur = satu entri DB dengan:
- Nama/judul (e.g. "Alur Pendaftaran Pasien Lama")
- Gambar (upload PNG/JPG — biasanya berupa infografis/bagan alur)
- Urutan tampil
- Status aktif/non-aktif

Admin bisa tambah alur baru kapan saja (misal: "Berobat Pasien Dinas TNI",
"Alur BPJS Online", dll) tanpa perlu coding.

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan                                                          |
|-------------|---------------------------------------------------------------------------|
| Database    | rssite_alur_pelayanan                                                     |
| Model       | AlurPelayanan                                                             |
| Routes      | /informasi/alur-pelayanan (publik) + /admin/alur-pelayanan (admin CRUD)  |
| Controllers | InformasiController@alurPelayanan + Admin/AlurPelayananController         |
| Views       | informasi/alur-pelayanan.blade.php + admin views                          |
| Navbar      | Dropdown "Informasi" dengan sub-menu Alur Pelayanan                       |
| Sidebar     | Entry "Alur Pelayanan" di grup Konten admin                               |

---

## 1. Migration

```php
Schema::create('rssite_alur_pelayanan', function (Blueprint $table) {
    $table->id();
    $table->string('judul', 200);          // "Alur Pendaftaran Pasien Lama"
    $table->string('gambar', 500);         // path storage — WAJIB ada gambar
    $table->text('keterangan')->nullable(); // teks keterangan opsional di bawah gambar
    $table->integer('urutan')->default(0);
    $table->boolean('aktif')->default(true);
    $table->timestamps();
});
```

---

## 2. Model: app/Models/AlurPelayanan.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AlurPelayanan extends Model
{
    protected $table    = 'rssite_alur_pelayanan';
    protected $fillable = ['judul', 'gambar', 'keterangan', 'urutan', 'aktif'];
    protected $casts    = ['aktif' => 'boolean'];

    public function scopeAktif($q) { return $q->where('aktif', true); }

    public function getGambarUrlAttribute(): string
    {
        return asset('storage/' . $this->gambar);
    }
}
```

---

## 3. Routes

### Publik — tambah di routes/web.php:

```php
Route::prefix('informasi')->name('informasi.')->group(function () {
    Route::get('/alur-pelayanan', [InformasiController::class, 'alurPelayanan'])
         ->name('alur-pelayanan');
    // Placeholder sub-menu Informasi lainnya bisa ditambah di sini
});
```

### Admin — tambah di dalam group admin (middleware auth):

```php
Route::prefix('alur-pelayanan')->name('alur-pelayanan.')->group(function () {
    Route::get('/',            [Admin\AlurPelayananController::class, 'index'])  ->name('index');
    Route::get('/create',      [Admin\AlurPelayananController::class, 'create']) ->name('create');
    Route::post('/',           [Admin\AlurPelayananController::class, 'store'])  ->name('store');
    Route::get('/{id}/edit',   [Admin\AlurPelayananController::class, 'edit'])   ->name('edit');
    Route::put('/{id}',        [Admin\AlurPelayananController::class, 'update']) ->name('update');
    Route::delete('/{id}',     [Admin\AlurPelayananController::class, 'destroy'])->name('destroy');
    Route::post('/{id}/toggle',[Admin\AlurPelayananController::class, 'toggle']) ->name('toggle');
});
```

---

## 4. Controller Publik: app/Http/Controllers/InformasiController.php

```php
<?php
namespace App\Http\Controllers;

use App\Models\AlurPelayanan;

class InformasiController extends Controller
{
    public function alurPelayanan()
    {
        $alurs = AlurPelayanan::aktif()
            ->orderBy('urutan')
            ->get();

        return view('informasi.alur-pelayanan', compact('alurs'));
    }
}
```

---

## 5. Controller Admin: app/Http/Controllers/Admin/AlurPelayananController.php

```php
<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\AlurPelayanan;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AlurPelayananController extends Controller
{
    public function index()
    {
        $alurs = AlurPelayanan::orderBy('urutan')->paginate(15);
        return view('admin.alur-pelayanan.index', compact('alurs'));
    }

    public function create()
    {
        return view('admin.alur-pelayanan.form', ['alur' => null]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul'      => 'required|string|max:200',
            'gambar'     => 'required|image|mimes:jpg,jpeg,png|max:5120',
            // max 5MB — gambar alur/infografis biasanya lebih besar
            'keterangan' => 'nullable|string',
            'urutan'     => 'nullable|integer',
        ], [
            'gambar.required' => 'Gambar alur wajib diupload.',
            'gambar.max'      => 'Ukuran gambar maksimal 5 MB.',
        ]);

        $path = $request->file('gambar')->store('rssite/alur-pelayanan', 'public');

        AlurPelayanan::create([
            'judul'      => $request->judul,
            'gambar'     => $path,
            'keterangan' => $request->keterangan,
            'urutan'     => $request->urutan ?? 0,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.alur-pelayanan.index')
                         ->with('success', 'Alur pelayanan berhasil ditambahkan.');
    }

    public function edit(int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);
        return view('admin.alur-pelayanan.form', compact('alur'));
    }

    public function update(Request $request, int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);

        $request->validate([
            'judul'      => 'required|string|max:200',
            'gambar'     => 'nullable|image|mimes:jpg,jpeg,png|max:5120',
            'keterangan' => 'nullable|string',
            'urutan'     => 'nullable|integer',
        ]);

        $path = $alur->gambar;
        if ($request->hasFile('gambar')) {
            Storage::disk('public')->delete($path);
            $path = $request->file('gambar')->store('rssite/alur-pelayanan', 'public');
        }

        $alur->update([
            'judul'      => $request->judul,
            'gambar'     => $path,
            'keterangan' => $request->keterangan,
            'urutan'     => $request->urutan ?? 0,
            'aktif'      => $request->boolean('aktif', true),
        ]);

        return redirect()->route('admin.alur-pelayanan.index')
                         ->with('success', 'Alur pelayanan berhasil diperbarui.');
    }

    public function destroy(int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);
        Storage::disk('public')->delete($alur->gambar);
        $alur->delete();
        return redirect()->back()->with('success', 'Alur pelayanan berhasil dihapus.');
    }

    public function toggle(int $id)
    {
        $alur = AlurPelayanan::findOrFail($id);
        $alur->update(['aktif' => !$alur->aktif]);
        return redirect()->back()->with('success', 'Status diperbarui.');
    }
}
```

---

## 6. View Publik: resources/views/informasi/alur-pelayanan.blade.php

```blade
@extends('layouts.app')
@section('title', 'Alur Pelayanan — RS TK IV Sintang')
@section('content')

@include('partials.page-header', ['judul' => 'Alur Pelayanan', 'parent' => 'Informasi'])

<section class="py-12 bg-white min-h-screen">
  <div class="container mx-auto px-4 max-w-5xl">

    @if($alurs->isEmpty())
      <div class="text-center py-24 text-gray-400">
        <i class="fa fa-sitemap text-6xl mb-4 block"></i>
        <p class="text-lg">Informasi alur pelayanan sedang diperbarui.</p>
      </div>
    @else

      {{-- Daftar alur — persis referensi: judul border kiri hijau + gambar full-width --}}
      <div class="space-y-14">

        @foreach($alurs as $alur)
          <div class="reveal">

            {{-- Sub-judul dengan border kiri hijau (persis referensi) --}}
            <div class="flex items-center gap-3 mb-5">
              <div class="w-1 h-7 bg-green-700 rounded-full flex-shrink-0"></div>
              <h2 class="font-playfair text-xl font-semibold text-gray-800">
                {{ $alur->judul }}
              </h2>
            </div>

            {{-- Gambar alur — full width, klik untuk zoom (lightbox) --}}
            <div class="rounded-2xl overflow-hidden shadow-md border border-gray-100
                        cursor-zoom-in"
                 @click="openLightbox('{{ $alur->gambar_url }}', '{{ addslashes($alur->judul) }}')"
                 x-data>
              <img src="{{ $alur->gambar_url }}"
                   alt="{{ $alur->judul }}"
                   class="w-full h-auto object-contain hover:scale-[1.01]
                          transition-transform duration-300">
            </div>

            {{-- Keterangan (opsional) --}}
            @if($alur->keterangan)
              <p class="mt-3 text-sm text-gray-500 italic">
                {{ $alur->keterangan }}
              </p>
            @endif

            {{-- Hint klik zoom --}}
            <p class="mt-2 text-xs text-gray-400 flex items-center gap-1">
              <i class="fa fa-search-plus"></i>
              Klik gambar untuk memperbesar
            </p>

          </div>
        @endforeach

      </div>

    @endif

  </div>
</section>

{{-- ===== LIGHTBOX OVERLAY ===== --}}
{{-- Alpine.js global untuk lightbox — taruh sebelum closing body di layouts/app.blade.php
     atau tambahkan inline di halaman ini dengan x-data di div luar --}}

<div x-data="lightboxData()"
     x-show="open"
     x-transition:enter="transition ease-out duration-200"
     x-transition:enter-start="opacity-0"
     x-transition:enter-end="opacity-100"
     x-transition:leave="transition ease-in duration-150"
     x-transition:leave-start="opacity-100"
     x-transition:leave-end="opacity-0"
     @keydown.escape.window="open = false"
     class="fixed inset-0 z-50 flex items-center justify-center p-4"
     style="display: none; background: rgba(0,0,0,0.85);">

  {{-- Klik background → tutup --}}
  <div class="absolute inset-0" @click="open = false"></div>

  {{-- Konten lightbox --}}
  <div class="relative z-10 max-w-5xl w-full mx-auto">

    {{-- Tombol tutup --}}
    <button @click="open = false"
            class="absolute -top-10 right-0 text-white hover:text-gray-300
                   transition-colors text-sm flex items-center gap-2">
      <i class="fa fa-times text-lg"></i>
      <span>Tutup (ESC)</span>
    </button>

    {{-- Judul gambar --}}
    <p class="text-white text-sm font-medium mb-3 text-center opacity-80"
       x-text="caption"></p>

    {{-- Gambar --}}
    <div class="rounded-2xl overflow-hidden shadow-2xl">
      <img :src="src" :alt="caption"
           class="w-full h-auto max-h-[80vh] object-contain bg-white">
    </div>

    {{-- Hint download --}}
    <div class="text-center mt-3">
      <a :href="src" download
         class="inline-flex items-center gap-2 text-white text-xs opacity-60
                hover:opacity-100 transition-opacity">
        <i class="fa fa-download"></i> Simpan Gambar
      </a>
    </div>

  </div>
</div>

@push('scripts')
<script>
// Lightbox global function — dipanggil dari @click setiap gambar alur
function lightboxData() {
  return {
    open:    false,
    src:     '',
    caption: '',
  }
}

// Helper global agar bisa dipanggil dari element Alpine.js lain
function openLightbox(src, caption) {
  // Dispatch custom event ke komponen lightbox
  window.dispatchEvent(new CustomEvent('open-lightbox', { detail: { src, caption } }));
}
</script>

<script>
// Update lightboxData untuk listen event global
document.addEventListener('alpine:init', () => {
  Alpine.data('lightboxData', () => ({
    open:    false,
    src:     '',
    caption: '',
    init() {
      window.addEventListener('open-lightbox', (e) => {
        this.src     = e.detail.src;
        this.caption = e.detail.caption;
        this.open    = true;
      });
    }
  }));
});
</script>
@endpush

@endsection
```

---

## 7. Views Admin

### resources/views/admin/alur-pelayanan/index.blade.php

```
@extends('layouts.admin')
@section('title', 'Kelola Alur Pelayanan')
@section('content')

HEADER:
- Judul "Alur Pelayanan"
- Sub: "Kelola daftar alur pelayanan yang ditampilkan ke pengunjung"
- Tombol kanan: [+ Tambah Alur Pelayanan]

INFO BOX (bg-blue-50 rounded-xl p-4 mb-6):
  "Gambar alur biasanya berupa infografis/bagan. Rekomendasikan format landscape
   (lebar > tinggi) dengan resolusi minimal 1200px. Format: JPG atau PNG, maks 5MB."

TABEL (rounded-xl shadow overflow-hidden):
Header: # | Preview | Judul | Urutan | Status | Tanggal | Aksi

Per baris:
- Nomor
- Preview gambar: <img> thumbnail w-32 h-20 object-cover rounded-lg shadow-sm
  (klik thumbnail → buka gambar full di tab baru)
- Judul (font-medium text-gray-800)
  + keterangan kecil jika ada (text-xs text-gray-400 line-clamp-1)
- Urutan
- Toggle aktif (form POST)
- Tanggal created_at d/m/Y
- Aksi: [Edit] [Hapus confirm]
  — Hapus: confirm JS "Yakin hapus alur '{{ $alur->judul }}'? Gambar akan ikut terhapus."

Pagination default Laravel

@endsection
```

---

### resources/views/admin/alur-pelayanan/form.blade.php

```
@extends('layouts.admin')
@section('title', $alur ? 'Edit Alur Pelayanan' : 'Tambah Alur Pelayanan')
@section('content')

Card max-w-2xl mx-auto bg-white rounded-2xl shadow-sm p-8:

Form enctype="multipart/form-data" POST/PUT:

===== FIELD 1: Judul Alur* =====
Label: "Judul / Nama Alur"
Input text name="judul" required
placeholder: "Alur Pendaftaran Pasien Lama"
Contoh hint: "Contoh: Alur Pendaftaran Pasien Baru, Berobat Pasien Dinas TNI, Alur BPJS, dll"
value="{{ old('judul', $alur->judul ?? '') }}"

===== FIELD 2: Upload Gambar =====
Label: "Gambar Alur Pelayanan" + badge "Wajib" (saat create) / "Opsional — kosongkan jika tidak ganti" (saat edit)

JIKA EDIT ($alur tidak null):
  Tampilkan preview gambar existing:
  <div class="mb-4 rounded-xl overflow-hidden border border-gray-200 shadow-sm">
    <img src="{{ $alur->gambar_url }}"
         alt="{{ $alur->judul }}"
         class="w-full h-auto max-h-64 object-contain bg-gray-50">
  </div>
  <p class="text-xs text-gray-500 mb-3">
    <i class="fa fa-info-circle text-blue-500"></i>
    Gambar saat ini. Upload gambar baru di bawah untuk menggantinya.
  </p>

Area upload (drag & drop):
x-data="{ fileName: '', preview: null }"

<div class="border-2 border-dashed rounded-2xl transition-colors p-8 text-center cursor-pointer"
     :class="preview ? 'border-green-400 bg-green-50' : 'border-gray-300 hover:border-green-400 hover:bg-green-50'"
     @dragover.prevent @drop.prevent="onDrop($event)">

  JIKA preview (setelah pilih file):
  <template x-if="preview">
    <div>
      <img :src="preview" class="max-h-48 mx-auto rounded-xl shadow mb-3 object-contain">
      <p class="text-sm text-green-700 font-medium" x-text="fileName"></p>
      <button type="button" @click="preview=null; fileName=''; $refs.fileInput.value=''"
              class="text-xs text-red-500 hover:underline mt-2">
        Ganti file
      </button>
    </div>
  </template>

  JIKA belum ada preview:
  <template x-if="!preview">
    <div>
      <i class="fa fa-cloud-upload-alt text-5xl text-gray-300 mb-3 block"></i>
      <p class="text-sm text-gray-500 mb-1">Drag & drop atau</p>
      <label class="cursor-pointer font-semibold text-green-700 hover:underline text-sm">
        Pilih File Gambar
        <input type="file" name="gambar" x-ref="fileInput" class="hidden"
               accept=".jpg,.jpeg,.png"
               @change="onFileChange($event)">
      </label>
      <p class="text-xs text-gray-400 mt-2">JPG, PNG — Maksimal 5 MB</p>
      <p class="text-xs text-gray-400">Rekomendasikan resolusi minimal 1200px lebar (landscape)</p>
    </div>
  </template>

</div>

@error('gambar') <p class="text-red-500 text-sm mt-2">{{ $message }}</p> @enderror

Alpine.js methods:
onFileChange(e) {
  const f = e.target.files[0];
  if (!f) return;
  this.fileName = f.name;
  const reader = new FileReader();
  reader.onload = (ev) => { this.preview = ev.target.result; };
  reader.readAsDataURL(f);
},
onDrop(e) {
  const f = e.dataTransfer.files[0];
  if (!f) return;
  this.$refs.fileInput.files = e.dataTransfer.files;
  this.onFileChange({ target: { files: [f] } });
}

===== FIELD 3: Keterangan (opsional) =====
Label: "Keterangan Tambahan (Opsional)"
Textarea rows=3 name="keterangan"
placeholder: "Catatan atau penjelasan singkat tentang alur ini (ditampilkan di bawah gambar)"
value="{{ old('keterangan', $alur->keterangan ?? '') }}"

===== FIELD 4: Urutan =====
Label: "Urutan Tampil"
Input number name="urutan" min=0
value="{{ old('urutan', $alur->urutan ?? 0) }}"
hint: "Semakin kecil angka → tampil lebih atas. Gunakan kelipatan 10 (10, 20, 30) agar mudah menyisipkan urutan baru."

===== FIELD 5: Status =====
Toggle aktif/non-aktif

===== TOMBOL =====
[Simpan Alur Pelayanan] primary
[Batal → route admin.alur-pelayanan.index] secondary

@endsection
```

---

## 8. Navbar — Dropdown Informasi

Di `partials/navbar.blade.php`, tambahkan dropdown **Informasi**:

```blade
<div class="relative" x-data="{ open: false }">
  <button @click="open = !open" @click.away="open = false"
          class="flex items-center gap-1 font-medium transition-colors
                 {{ request()->routeIs('informasi.*')
                    ? 'text-green-700 font-semibold'
                    : 'text-gray-700 hover:text-green-700' }}">
    Informasi
    <i class="fa fa-chevron-down text-xs transition-transform duration-200"
       :class="open ? 'rotate-180' : ''"></i>
  </button>

  <div x-show="open" x-transition
       class="absolute top-full left-0 mt-2 w-56 bg-white rounded-xl shadow-xl
              border border-gray-100 py-2 z-50">

    <a href="{{ route('informasi.alur-pelayanan') }}"
       class="flex items-center gap-3 px-4 py-3 text-sm transition-colors
              {{ request()->routeIs('informasi.alur-pelayanan')
                 ? 'bg-green-50 text-green-700 font-medium'
                 : 'text-gray-700 hover:bg-green-50 hover:text-green-700' }}">
      <i class="fa fa-sitemap w-5 text-green-600"></i>
      Alur Pelayanan
    </a>

    {{-- Sub-menu Informasi lain bisa ditambah di sini nanti --}}
    {{-- Contoh:
    <a href="{{ route('informasi.jam-layanan') }}" class="...">
      <i class="fa fa-clock w-5 text-green-600"></i>
      Jam Layanan
    </a>
    --}}

  </div>
</div>
```

---

## 9. Sidebar Admin

Di `layouts/admin.blade.php`, tambahkan di grup Konten:

```blade
<a href="{{ route('admin.alur-pelayanan.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.alur-pelayanan.*') ? 'active' : '' }}">
  <i class="fa fa-sitemap w-5"></i>
  <span>Alur Pelayanan</span>
</a>
```

---

## 10. Seeder — Data Awal

```php
$alurData = [
    [
        'judul'      => 'Alur Pendaftaran Pasien Lama',
        'keterangan' => 'Pasien yang sudah pernah berobat ke RS TK IV Sintang sebelumnya.',
        'urutan'     => 10,
    ],
    [
        'judul'      => 'Alur Pendaftaran Pasien Baru',
        'keterangan' => 'Pasien yang pertama kali berobat ke RS TK IV Sintang.',
        'urutan'     => 20,
    ],
    [
        'judul'      => 'Alur Berobat Pasien Dinas TNI',
        'keterangan' => 'Khusus anggota TNI aktif dan keluarga yang menggunakan fasilitas dinas.',
        'urutan'     => 30,
    ],
];

foreach ($alurData as $a) {
    // Gambar tidak di-seed karena butuh file fisik
    // Cek dulu ada gambar placeholder
    // Admin upload melalui panel admin setelah setup
    \App\Models\AlurPelayanan::firstOrCreate(
        ['judul' => $a['judul']],
        // gambar tidak diset — admin wajib upload sebelum aktifkan
        array_merge($a, ['aktif' => false]) // non-aktif sampai admin upload gambar
    );
}

// CATATAN ke developer:
// Data alur di-seed dengan status non-aktif karena kolom gambar wajib ada.
// Setelah setup, admin login ke /admin/alur-pelayanan, upload gambar,
// lalu aktifkan masing-masing alur.
```

**PENTING**: Karena kolom `gambar` adalah `string` (bukan nullable dalam logika bisnis),
seed data alur tanpa gambar dengan status non-aktif. Beri catatan ini di README atau
di dashboard admin.

Alternatif: Buat gambar placeholder default yang di-copy ke storage saat seed:

```php
// Alternatif: copy placeholder image ke storage
$placeholder = 'rssite/alur-pelayanan/placeholder.png';
if (!Storage::disk('public')->exists($placeholder)) {
    // Buat file placeholder sederhana dengan GD atau copy dari resources
    // Atau skip dan biarkan admin upload
}
```

---

## 11. CSS Tambahan

Tambahkan ke style di `layouts/app.blade.php`:

```css
/* Cursor zoom untuk gambar alur */
.cursor-zoom-in { cursor: zoom-in; }

/* Lightbox body scroll lock saat terbuka */
body.lightbox-open { overflow: hidden; }

/* Gambar alur: border bawah animasi saat hover */
.alur-img-wrap {
  position: relative;
}
.alur-img-wrap::after {
  content: '';
  position: absolute;
  bottom: 0; left: 0; right: 0;
  height: 3px;
  background: linear-gradient(90deg, #2D6A4F, #52B788);
  border-radius: 0 0 16px 16px;
  opacity: 0;
  transition: opacity 0.3s;
}
.alur-img-wrap:hover::after { opacity: 1; }
```

---

## Checklist Akhir

**Database & Model**
- [ ] Migration rssite_alur_pelayanan berjalan tanpa error
- [ ] Model AlurPelayanan: $fillable, scope aktif, accessor gambar_url
- [ ] Seeder membuat 3 alur default (status non-aktif, tanpa gambar)
- [ ] Seeder menampilkan catatan ke developer tentang upload gambar

**Halaman Publik /informasi/alur-pelayanan**
- [ ] HTTP 200 accessible
- [ ] Judul "Alur Pelayanan" di page header
- [ ] Setiap alur tampil dengan sub-judul (border kiri hijau persis referensi)
- [ ] Gambar full-width dengan rounded + shadow
- [ ] Hover gambar: scale sedikit + kursor zoom-in
- [ ] Klik gambar → lightbox overlay terbuka
- [ ] Lightbox: gambar bisa di-zoom (full screen overlay)
- [ ] Lightbox tutup: klik background, tekan ESC, atau tombol Tutup
- [ ] Link "Simpan Gambar" di lightbox berfungsi (download)
- [ ] Keterangan tampil di bawah gambar jika ada (italic kecil)
- [ ] Hint "Klik gambar untuk memperbesar" tampil di bawah setiap gambar
- [ ] Empty state tampil jika tidak ada alur aktif
- [ ] Scroll reveal animasi saat setiap section muncul di viewport

**Admin Panel /admin/alur-pelayanan**
- [ ] HTTP 200, auth protected
- [ ] Index tabel: thumbnail 128x80 object-cover, judul, urutan, toggle, tanggal, aksi
- [ ] Klik thumbnail di tabel → buka gambar penuh di tab baru
- [ ] Info box panduan ukuran gambar tampil di atas tabel
- [ ] Tambah alur: gambar WAJIB diupload (validasi required)
- [ ] Edit alur: gambar existing ditampilkan, upload baru opsional
- [ ] Drag & drop area berfungsi
- [ ] Preview gambar muncul setelah pilih/drop file (sebelum submit)
- [ ] Validasi: format JPG/PNG, maks 5MB
- [ ] Gambar lama terhapus dari storage saat diganti
- [ ] Hapus alur: gambar fisik ikut terhapus + confirm dialog dengan nama alur
- [ ] Toggle aktif/non-aktif berfungsi
- [ ] Urutan: hint "gunakan kelipatan 10" tampil
- [ ] Flash success/error tampil
- [ ] Pagination berfungsi

**Navbar & Sidebar**
- [ ] Dropdown "Informasi" muncul di navbar dengan sub-menu "Alur Pelayanan"
- [ ] Dropdown menutup saat klik di luar (Alpine.js @click.away)
- [ ] Active highlight navbar "Informasi" saat di halaman informasi
- [ ] Active highlight sub-menu "Alur Pelayanan" saat di halaman tersebut
- [ ] Sidebar admin "Alur Pelayanan" highlight saat aktif

**Ekstensibilitas**
- [ ] Route group prefix('informasi') sudah disiapkan untuk sub-menu lain
- [ ] Dropdown navbar Informasi mudah ditambah entry baru (komentar placeholder ada)
