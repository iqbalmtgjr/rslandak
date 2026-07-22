# Task: Layanan — Rawat Inap (Dropdown Tipe Kelas + Gallery 5 Foto)

Tambahkan halaman publik **Rawat Inap** sebagai sub-menu kedua dari dropdown Layanan.
Referensi tampilan: screenshot halaman Rawat Inap RSI PKU Muhammadiyah Tegal.

Konteks project: lihat `CLAUDE.md` dan `layanan-poliklinik.md`.
Tabel `rssite_kamars` sudah ada dari CLAUDE.md — task ini MEMPERLUAS tabel tersebut
dengan tambahan kolom foto gallery dan tarif, plus halaman publik yang dinamis.

---

## Konsep Halaman

```
/layanan/rawat-inap
  ├── Dropdown "Pilih Tipe Kamar" (Alpine.js — tanpa reload halaman)
  └── Konten terpilih (tampil berubah saat dropdown berganti):
        ├── KIRI: Gallery foto (1 foto utama + 4 thumbnail, klik thumbnail → ganti foto utama)
        └── KANAN: Nama kelas, fasilitas (numbered list), tarif / hari
```

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan / Diubah                                                    |
|-------------|------------------------------------------------------------------------------|
| Database    | ALTER rssite_kamars — tambah kolom: tarif, foto_1..foto_5, foto_utama        |
| Migration   | Migration baru add_rawat_inap_fields_to_rssite_kamars                        |
| Model       | Update Kamar — accessor fotos[] dan tarif_readable                           |
| Routes      | Ganti placeholder /layanan/rawat-inap → LayananController@rawatInap          |
| Controller  | LayananController@rawatInap — load semua kamar aktif + json foto             |
| Views       | layanan/rawat-inap.blade.php (Alpine.js tab + gallery)                       |
| Admin       | Update admin/kamar/form.blade.php — tambah field tarif + 5 upload foto       |
| Admin Ctrl  | Update Admin/KamarController — handle upload 5 foto + hapus foto lama        |

---

## 1. Migration Baru — Tambah Kolom ke rssite_kamars

Buat file migration: `2024_01_03_000000_add_rawat_inap_fields_to_rssite_kamars.php`

```php
public function up(): void
{
    Schema::table('rssite_kamars', function (Blueprint $table) {
        // Tarif per malam (nullable — bisa dikosongkan jika tarif tidak ingin ditampilkan)
        $table->unsignedBigInteger('tarif')->nullable()->after('badge')
              ->comment('Tarif per hari dalam rupiah');

        // 5 slot foto (path storage)
        $table->string('foto_1', 500)->nullable()->after('tarif');
        $table->string('foto_2', 500)->nullable()->after('foto_1');
        $table->string('foto_3', 500)->nullable()->after('foto_2');
        $table->string('foto_4', 500)->nullable()->after('foto_3');
        $table->string('foto_5', 500)->nullable()->after('foto_4');
    });
}

public function down(): void
{
    Schema::table('rssite_kamars', function (Blueprint $table) {
        $table->dropColumn(['tarif','foto_1','foto_2','foto_3','foto_4','foto_5']);
    });
}
```

---

## 2. Update Model Kamar (app/Models/Kamar.php)

Tambahkan kolom baru ke `$fillable` dan accessor berikut:

```php
// Tambahkan ke $fillable yang sudah ada:
protected $fillable = [
    'nama', 'deskripsi', 'fasilitas', 'gambar', 'badge', 'urutan', 'aktif',
    // TAMBAHAN:
    'tarif', 'foto_1', 'foto_2', 'foto_3', 'foto_4', 'foto_5',
];

// Accessor: ambil semua foto yang tidak null sebagai array URL
// Urutan: foto_1 jadi hero utama, sisanya thumbnail
public function getFotosAttribute(): array
{
    $fotos = [];
    foreach (['foto_1','foto_2','foto_3','foto_4','foto_5'] as $col) {
        if ($this->$col) {
            $fotos[] = asset('storage/' . $this->$col);
        }
    }
    return $fotos;
}

// Foto pertama (hero utama)
public function getFotoUtamaAttribute(): ?string
{
    $fotos = $this->fotos;
    return $fotos[0] ?? null;
}

// Foto thumbnail (foto ke-2 sampai ke-5)
public function getFotoThumbnailsAttribute(): array
{
    $fotos = $this->fotos;
    return array_slice($fotos, 1); // ambil index 1 ke atas
}

// Tarif formatted rupiah
public function getTarifReadableAttribute(): ?string
{
    if (!$this->tarif) return null;
    return 'Rp. ' . number_format($this->tarif, 0, ',', '.') . ',00';
}

// Fasilitas sebagai array (JSON decode)
// Pastikan cast sudah ada: 'fasilitas' => 'array'
```

---

## 3. Routes — Update Placeholder

Di `routes/web.php`, di dalam group `prefix('layanan')`, ganti:

```php
// HAPUS baris ini jika ada dari layanan-poliklinik.md:
// Route::get('/rawat-inap', [LayananController::class, 'rawatInap'])->name('rawat-inap');

// GANTI DENGAN (atau pastikan sudah ada):
Route::get('/rawat-inap', [LayananController::class, 'rawatInap'])->name('rawat-inap');
```

Tidak perlu menambah route baru — route sudah ada dari layanan-poliklinik.md.
Hanya perlu update method `rawatInap()` di controller.

---

## 4. Update LayananController@rawatInap

Di `app/Http/Controllers/LayananController.php`, ganti method `rawatInap()`:

```php
public function rawatInap()
{
    // Load semua kamar aktif, urut by urutan
    // Eager load tidak perlu karena semua data ada di tabel
    $kamars = \App\Models\Kamar::where('aktif', true)
        ->orderBy('urutan')
        ->get();

    // Kirim data sebagai JSON-safe collection untuk Alpine.js
    // Alpine.js akan render konten tanpa reload halaman
    $kamarsJson = $kamars->map(function ($k) {
        return [
            'id'          => $k->id,
            'nama'        => $k->nama,
            'deskripsi'   => $k->deskripsi,
            'fasilitas'   => is_array($k->fasilitas) ? $k->fasilitas : json_decode($k->fasilitas ?? '[]', true),
            'badge'       => $k->badge,
            'tarif'       => $k->tarif,
            'tarif_text'  => $k->tarif_readable,
            'fotos'       => $k->fotos,         // array URL foto
            'foto_utama'  => $k->foto_utama,    // URL foto pertama
        ];
    });

    return view('layanan.rawat-inap', compact('kamars', 'kamarsJson'));
}
```

---

## 5. View Publik: resources/views/layanan/rawat-inap.blade.php

```blade
@extends('layouts.app')
@section('title', 'Rawat Inap — RS TK IV Sintang')
@section('content')

@include('partials.page-header', ['judul' => 'Rawat Inap', 'parent' => 'Layanan'])

<section class="py-12 bg-white min-h-screen">
  <div class="container mx-auto px-4 max-w-6xl"
       x-data="rawatInap({{ $kamarsJson }})">

    {{-- ===== DROPDOWN PILIH TIPE KAMAR ===== --}}
    {{-- Persis seperti referensi: label "Pilih Tipe Kamar" di atas, dropdown di bawah --}}

    <div class="mb-10">
      <label class="block text-sm font-medium text-gray-700 mb-2">
        Pilih Tipe Kamar
      </label>

      {{-- Custom dropdown styled (mirip referensi screenshot) --}}
      <div class="relative w-72">
        <select x-model="selectedId"
                @change="onSelect()"
                class="w-full appearance-none border border-gray-300 rounded-lg
                       px-4 py-3 pr-10 text-sm text-gray-700 bg-white
                       focus:outline-none focus:border-green-500 focus:ring-1 focus:ring-green-200
                       cursor-pointer">
          <option value="">-- Pilih Tipe Kamar --</option>
          @foreach($kamars as $k)
            <option value="{{ $k->id }}">{{ $k->nama }}</option>
          @endforeach
        </select>
        {{-- Ikon dropdown kustom --}}
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-3
                    bg-green-700 rounded-r-lg">
          <i class="fa fa-chevron-down text-white text-xs"></i>
        </div>
      </div>
    </div>

    {{-- ===== KONTEN KAMAR (tersembunyi saat belum pilih) ===== --}}

    <div x-show="selected !== null" x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 translate-y-4"
         x-transition:enter-end="opacity-100 translate-y-0">

      {{-- Layout 2 kolom: Gallery Kiri (7/12) + Info Kanan (5/12) --}}
      <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

        {{-- ===== KIRI: GALLERY FOTO ===== --}}
        <div class="lg:col-span-7">

          {{-- Foto Utama (Hero) --}}
          <div class="relative rounded-2xl overflow-hidden shadow-md mb-3"
               style="min-height: 320px; background: #DCFCE7;">

            {{-- Foto jika ada --}}
            <template x-if="selected && selected.fotos && selected.fotos.length > 0">
              <img :src="activeFoto"
                   :alt="selected.nama"
                   class="w-full h-80 object-cover transition-opacity duration-300">
            </template>

            {{-- Placeholder jika tidak ada foto --}}
            <template x-if="!selected || !selected.fotos || selected.fotos.length === 0">
              <div class="w-full h-80 flex items-center justify-center bg-green-50">
                <div class="text-center text-green-700 opacity-40">
                  <i class="fa fa-bed text-7xl block mb-3"></i>
                  <p class="text-sm font-medium">Foto belum tersedia</p>
                </div>
              </div>
            </template>

            {{-- Badge kelas di pojok kiri atas (seperti "VIP ROOM" di referensi) --}}
            <template x-if="selected && selected.badge">
              <div class="absolute top-4 left-4 bg-green-700 text-white
                          text-xs font-bold px-3 py-1.5 rounded-lg uppercase tracking-wider shadow">
                <span x-text="selected.badge"></span>
              </div>
            </template>

          </div>

          {{-- Thumbnail Row (foto 2–5) --}}
          <template x-if="selected && selected.fotos && selected.fotos.length > 1">
            <div class="grid grid-cols-4 gap-2">
              <template x-for="(foto, idx) in selected.fotos" :key="idx">
                <button type="button"
                        @click="activeFoto = foto"
                        class="rounded-xl overflow-hidden border-2 transition-all duration-200 aspect-square"
                        :class="activeFoto === foto
                          ? 'border-green-600 shadow-md ring-2 ring-green-300'
                          : 'border-transparent hover:border-green-400'">
                  <img :src="foto" :alt="'Foto ' + (idx+1)"
                       class="w-full h-full object-cover">
                </button>
              </template>
              {{-- Placeholder slot thumbnail kosong --}}
              <template x-for="n in (4 - Math.min(selected.fotos.length, 4))" :key="'empty'+n">
                <div class="rounded-xl bg-gray-100 border-2 border-dashed border-gray-200
                            flex items-center justify-center aspect-square">
                  <i class="fa fa-image text-gray-300 text-xl"></i>
                </div>
              </template>
            </div>
          </template>

        </div>

        {{-- ===== KANAN: INFO KELAS ===== --}}
        <div class="lg:col-span-5">

          {{-- Nama Kelas dengan border kiri emas (persis referensi) --}}
          <div class="border-l-4 border-yellow-500 pl-5 mb-6">
            <h2 class="font-playfair text-3xl font-bold text-gray-800"
                x-text="selected ? 'Fasilitas ' + selected.nama : ''"></h2>
          </div>

          {{-- Deskripsi (jika ada) --}}
          <template x-if="selected && selected.deskripsi">
            <p class="text-gray-600 text-sm mb-5 leading-relaxed"
               x-text="selected.deskripsi"></p>
          </template>

          {{-- Fasilitas (numbered list persis referensi) --}}
          <template x-if="selected && selected.fasilitas && selected.fasilitas.length > 0">
            <ol class="space-y-2 mb-6">
              <template x-for="(item, idx) in selected.fasilitas" :key="idx">
                <li class="flex items-start gap-2 text-gray-700 text-sm">
                  <span class="font-medium text-gray-500 flex-shrink-0"
                        x-text="(idx + 1) + '.'"></span>
                  <span x-text="item"></span>
                </li>
              </template>
            </ol>
          </template>

          {{-- Tarif (persis referensi: "Tarif : Rp. 1.020.000,00 / Hari") --}}
          <template x-if="selected && selected.tarif_text">
            <div class="pt-4 border-t border-gray-200">
              <p class="text-gray-800 text-base font-medium">
                Tarif :
                <span class="text-green-700 font-bold text-lg" x-text="selected.tarif_text"></span>
                <span class="text-gray-500 font-normal text-sm"> / Hari</span>
              </p>
              <p class="text-xs text-gray-400 mt-1">
                *Tarif belum termasuk biaya tindakan medis dan obat-obatan
              </p>
            </div>
          </template>

          {{-- Jika tarif tidak diset --}}
          <template x-if="selected && !selected.tarif_text">
            <div class="pt-4 border-t border-gray-200">
              <p class="text-sm text-gray-400 italic">
                Untuk informasi tarif, silakan hubungi bagian administrasi.
              </p>
            </div>
          </template>

          {{-- CTA --}}
          <div class="mt-6 flex flex-col sm:flex-row gap-3">
            <a href="{{ route('home') }}#kontak"
               class="flex-1 flex items-center justify-center gap-2 py-3 px-5
                      bg-green-700 text-white rounded-xl text-sm font-medium
                      hover:bg-green-800 transition-colors">
              <i class="fa fa-phone"></i> Hubungi Kami
            </a>
            <a href="https://wa.me/{{ preg_replace('/\D/', '', \App\Models\SiteSetting::get('telepon','0')) }}"
               target="_blank"
               class="flex-1 flex items-center justify-center gap-2 py-3 px-5
                      bg-green-500 text-white rounded-xl text-sm font-medium
                      hover:bg-green-600 transition-colors">
              <i class="fab fa-whatsapp"></i> WhatsApp
            </a>
          </div>

        </div>

      </div>
    </div>

    {{-- State awal: belum pilih kamar --}}
    <div x-show="selected === null"
         class="py-20 text-center text-gray-400">
      <i class="fa fa-hand-point-up text-5xl mb-4 block"></i>
      <p class="text-base">Pilih tipe kamar di atas untuk melihat detail fasilitas</p>
    </div>

  </div>
</section>

{{-- ===== ALPINE.JS COMPONENT ===== --}}
@push('scripts')
<script>
function rawatInap(kamarsData) {
  return {
    kamars:     kamarsData,
    selectedId: '',
    selected:   null,
    activeFoto: null,

    onSelect() {
      if (!this.selectedId) {
        this.selected   = null;
        this.activeFoto = null;
        return;
      }

      // Cari kamar berdasarkan id (convert ke int karena value select adalah string)
      this.selected = this.kamars.find(k => k.id == this.selectedId) || null;

      // Set foto aktif ke foto pertama
      if (this.selected && this.selected.fotos && this.selected.fotos.length > 0) {
        this.activeFoto = this.selected.fotos[0];
      } else {
        this.activeFoto = null;
      }
    },

    // Auto-select kamar pertama jika ada
    init() {
      if (this.kamars.length > 0) {
        // Tidak auto-select, biarkan user pilih sendiri (sesuai referensi)
        this.selected = null;
      }
    }
  }
}
</script>
@endpush

@endsection
```

---

## 6. Update Admin — KamarController

Di `app/Http/Controllers/Admin/KamarController.php`, update method `store()` dan `update()`:

### Private helper method (tambahkan ke class):

```php
/**
 * Handle upload 5 foto untuk kamar.
 * Iterasi field foto_1 sampai foto_5.
 * Jika ada file baru → upload + hapus yang lama.
 * Jika tidak ada file baru → pertahankan path lama.
 *
 * @param  Request $request
 * @param  Kamar|null $kamar  — null saat create
 * @return array              — ['foto_1' => 'path', 'foto_2' => null, ...]
 */
private function handleFotoGallery(Request $request, $kamar = null): array
{
    $result = [];
    for ($i = 1; $i <= 5; $i++) {
        $field   = "foto_{$i}";
        $oldPath = $kamar?->$field;

        if ($request->hasFile($field)) {
            // Hapus foto lama jika ada
            if ($oldPath) {
                Storage::disk('public')->delete($oldPath);
            }
            $result[$field] = $request->file($field)->store('rssite/kamar-foto', 'public');
        } elseif ($request->boolean("hapus_{$field}") && $oldPath) {
            // Admin centang "Hapus Foto" → hapus file dan set null
            Storage::disk('public')->delete($oldPath);
            $result[$field] = null;
        } else {
            // Tidak ada perubahan → pertahankan path lama
            $result[$field] = $oldPath;
        }
    }
    return $result;
}
```

### Update store():

```php
public function store(Request $request)
{
    $request->validate([
        'nama'      => 'required|string|max:100',
        'deskripsi' => 'nullable|string',
        'badge'     => 'nullable|string|max:50',
        'urutan'    => 'nullable|integer',
        'tarif'     => 'nullable|integer|min:0',
        'foto_1'    => 'nullable|image|max:2048',
        'foto_2'    => 'nullable|image|max:2048',
        'foto_3'    => 'nullable|image|max:2048',
        'foto_4'    => 'nullable|image|max:2048',
        'foto_5'    => 'nullable|image|max:2048',
        // fasilitas[] dinamis dari Alpine.js
        'fasilitas' => 'nullable|array',
    ]);

    $fotoData = $this->handleFotoGallery($request);

    Kamar::create(array_merge([
        'nama'      => $request->nama,
        'deskripsi' => $request->deskripsi,
        'fasilitas' => json_encode(array_filter($request->input('fasilitas', []))),
        'badge'     => $request->badge,
        'urutan'    => $request->urutan ?? 0,
        'aktif'     => $request->boolean('aktif', true),
        'tarif'     => $request->tarif,
        // foto_1 juga digunakan sebagai gambar utama (kolom lama)
        'gambar'    => null, // kolom lama tidak dipakai lagi
    ], $fotoData));

    return redirect()->route('admin.kamar.index')
                     ->with('success', 'Kamar berhasil ditambahkan.');
}
```

### Update update():

```php
public function update(Request $request, $id)
{
    $kamar = Kamar::findOrFail($id);

    $request->validate([
        'nama'      => 'required|string|max:100',
        'deskripsi' => 'nullable|string',
        'badge'     => 'nullable|string|max:50',
        'urutan'    => 'nullable|integer',
        'tarif'     => 'nullable|integer|min:0',
        'foto_1'    => 'nullable|image|max:2048',
        'foto_2'    => 'nullable|image|max:2048',
        'foto_3'    => 'nullable|image|max:2048',
        'foto_4'    => 'nullable|image|max:2048',
        'foto_5'    => 'nullable|image|max:2048',
        'fasilitas' => 'nullable|array',
    ]);

    $fotoData = $this->handleFotoGallery($request, $kamar);

    $kamar->update(array_merge([
        'nama'      => $request->nama,
        'deskripsi' => $request->deskripsi,
        'fasilitas' => json_encode(array_filter($request->input('fasilitas', []))),
        'badge'     => $request->badge,
        'urutan'    => $request->urutan ?? 0,
        'aktif'     => $request->boolean('aktif', true),
        'tarif'     => $request->tarif,
    ], $fotoData));

    return redirect()->route('admin.kamar.index')
                     ->with('success', 'Kamar berhasil diperbarui.');
}
```

---

## 7. Update Admin View — admin/kamar/form.blade.php

Tambahkan field baru ke form kamar yang sudah ada:

### Tambahkan setelah field "Badge":

```
===== FIELD TARIF (tambah di antara Badge dan Urutan) =====

Label: "Tarif per Hari (Rp)"
Input: type="number" name="tarif" min="0" step="1000"
       value="{{ old('tarif', $kamar->tarif ?? '') }}"
       placeholder="1020000"
Hint: "Masukkan angka saja tanpa titik/koma. Kosongkan jika tidak ingin ditampilkan."
Preview live (Alpine.js):
  x-data="{ tarif: '{{ $kamar->tarif ?? '' }}' }"
  x-model="tarif" (di input)
  Preview: "Rp. {{ number_format(tarif) }},00 / Hari" jika tarif terisi

===== FIELD GALLERY FOTO (5 slot) =====

Section header: "Foto Gallery (maksimal 5 foto)"
Sub: "Foto pertama akan menjadi foto utama yang ditampilkan. Format: JPG, PNG. Maks 2MB per foto."

Grid 2 kolom sm:3 kolom (5 slot):

@for ($i = 1; $i <= 5; $i++)

  <div class="border-2 border-dashed border-gray-200 rounded-2xl overflow-hidden
              hover:border-green-400 transition-colors group">

    {{-- Preview foto existing (saat edit) --}}
    @if(isset($kamar) && $kamar->{"foto_{$i}"})

      {{-- Ada foto existing --}}
      <div class="relative">
        <img src="{{ asset('storage/' . $kamar->{"foto_{$i}"}) }}"
             id="preview_foto_{{ $i }}"
             class="w-full h-36 object-cover">

        {{-- Overlay tombol saat hover --}}
        <div class="absolute inset-0 bg-black bg-opacity-40 opacity-0 group-hover:opacity-100
                    transition-opacity flex items-center justify-center gap-2">
          <label class="cursor-pointer bg-white text-green-700 rounded-lg px-3 py-1.5
                        text-xs font-medium hover:bg-green-50">
            Ganti
            <input type="file" name="foto_{{ $i }}" class="hidden"
                   accept=".jpg,.jpeg,.png"
                   onchange="previewFoto(this, 'preview_foto_{{ $i }}')">
          </label>
          <label class="flex items-center gap-1 bg-red-100 text-red-600 rounded-lg
                        px-3 py-1.5 text-xs font-medium hover:bg-red-200 cursor-pointer">
            <input type="checkbox" name="hapus_foto_{{ $i }}" value="1" class="w-3 h-3">
            Hapus
          </label>
        </div>
      </div>

    @else

      {{-- Slot kosong — area klik upload --}}
      <label class="flex flex-col items-center justify-center h-36 cursor-pointer
                    hover:bg-green-50 transition-colors">
        <i class="fa fa-camera text-3xl text-gray-300 mb-2"
           id="icon_foto_{{ $i }}"></i>
        <span class="text-xs text-gray-400">Foto {{ $i }}</span>
        @if($i === 1)
          <span class="text-xs text-green-600 font-medium mt-1">★ Foto Utama</span>
        @endif
        <input type="file" name="foto_{{ $i }}" class="hidden"
               accept=".jpg,.jpeg,.png"
               onchange="previewFoto(this, null, 'icon_foto_{{ $i }}', this.closest('label'))">
      </label>

    @endif

    {{-- Label slot --}}
    <div class="text-center py-2 border-t border-gray-100 bg-gray-50">
      <span class="text-xs text-gray-500">
        {{ $i === 1 ? 'Foto Utama' : "Foto $i" }}
      </span>
    </div>

  </div>

@endfor

===== JAVASCRIPT untuk preview foto =====
Tambahkan di bawah form (sebelum @endsection):

<script>
function previewFoto(input, existingImgId, iconId, labelEl) {
  if (!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = function(e) {
    if (existingImgId) {
      // Ganti gambar yang sudah ada
      document.getElementById(existingImgId).src = e.target.result;
    } else if (iconId && labelEl) {
      // Slot kosong → tampilkan preview di dalam label
      labelEl.innerHTML =
        '<img src="' + e.target.result + '" class="w-full h-36 object-cover">' +
        '<input type="file" name="' + input.name + '" class="hidden" accept=".jpg,.jpeg,.png">';
    }
  };
  reader.readAsDataURL(input.files[0]);
}
</script>
```

---

## 8. Update Seeder — Tambah Tarif & Fasilitas Lengkap

Di `DatabaseSeeder.php`, update data kamar dengan tarif dan fasilitas lengkap:

```php
// Update kamar yang sudah ada dengan tarif
$kamarData = [
    [
        'nama'      => 'Kelas VIP',
        'badge'     => 'VIP',
        'tarif'     => 1020000,
        'fasilitas' => [
            'Bed Pasien 3 Krank',
            'Overbed Table',
            'AC',
            'Televisi LED',
            'Lemari Es',
            'Water Heater',
            'Katle Elektrik',
            'Free Hotspot Area',
            'Sofa Bed (untuk keluarga)',
            'Kamar mandi dalam',
        ],
        'deskripsi' => 'Kamar VIP dengan fasilitas premium untuk kenyamanan pasien dan keluarga.',
        'urutan'    => 1,
    ],
    [
        'nama'      => 'Kelas I',
        'badge'     => 'Kelas I',
        'tarif'     => 450000,
        'fasilitas' => [
            'Bed Pasien 2 Krank',
            'Overbed Table',
            'AC',
            'Televisi',
            'Lemari Pakaian',
            'Kamar mandi dalam',
            'Kapasitas 2 tempat tidur',
        ],
        'deskripsi' => 'Kamar kelas satu dengan fasilitas nyaman dan AC.',
        'urutan'    => 2,
    ],
    [
        'nama'      => 'Kelas II',
        'badge'     => 'Kelas II',
        'tarif'     => 225000,
        'fasilitas' => [
            'Bed Pasien 2 Krank',
            'Overbed Table',
            'Kipas Angin',
            'Lemari Pakaian',
            'Kamar mandi bersama',
            'Kapasitas 4 tempat tidur',
        ],
        'deskripsi' => 'Kamar kelas dua dengan fasilitas standar.',
        'urutan'    => 3,
    ],
    [
        'nama'      => 'Kelas III',
        'badge'     => 'BPJS',
        'tarif'     => 75000,
        'fasilitas' => [
            'Bed Pasien 2 Krank',
            'Overbed Table',
            'Kipas Angin',
            'Kamar mandi bersama',
            'Kapasitas 6 tempat tidur',
            'Menerima pasien BPJS Kesehatan',
        ],
        'deskripsi' => 'Kamar kelas tiga, menerima pasien BPJS Kesehatan.',
        'urutan'    => 4,
    ],
];

foreach ($kamarData as $kd) {
    \App\Models\Kamar::updateOrCreate(
        ['nama' => $kd['nama']],
        array_merge($kd, [
            'aktif'     => true,
            'fasilitas' => json_encode($kd['fasilitas']),
            // foto_1..foto_5 dikosongkan — admin upload via panel
        ])
    );
}
```

---

## 9. Update Navbar

Di `partials/navbar.blade.php`, pastikan link "Rawat Inap" di dropdown Layanan
mengarah ke route yang benar (sudah ada dari layanan-poliklinik.md):

```blade
{{-- Sudah ada di layanan-poliklinik.md, pastikan route name benar --}}
['route' => 'layanan.rawat-inap', 'ikon' => 'fa-bed', 'label' => 'Rawat Inap'],
```

Route name `layanan.rawat-inap` harus cocok dengan definisi di routes/web.php.

---

## 10. CSS Tambahan

Tambahkan ke style di `layouts/app.blade.php`:

```css
/* Gallery thumbnail active state */
.foto-thumb-active {
  border-color: #2D6A4F !important;
  box-shadow: 0 0 0 3px rgba(45,106,79,0.2);
}

/* Foto hero transition */
.foto-hero img {
  transition: opacity 0.3s ease;
}

/* Numbered list fasilitas */
.fasilitas-list li {
  padding: 4px 0;
  border-bottom: 1px dashed #F3F4F6;
}
.fasilitas-list li:last-child {
  border-bottom: none;
}

/* Tarif display */
.tarif-display {
  background: linear-gradient(135deg, #F0FDF4, #DCFCE7);
  border-left: 4px solid #2D6A4F;
  border-radius: 0 12px 12px 0;
  padding: 12px 16px;
}
```

---

## Checklist Akhir

**Database & Model**
- [ ] Migration add_rawat_inap_fields_to_rssite_kamars berjalan tanpa error
- [ ] Kolom tarif, foto_1..foto_5 tersedia di tabel rssite_kamars
- [ ] Model Kamar: $fillable diperbarui dengan kolom baru
- [ ] Accessor fotos[] mengembalikan array URL hanya untuk foto yang tidak null
- [ ] Accessor foto_utama mengembalikan foto pertama atau null
- [ ] Accessor foto_thumbnails mengembalikan foto ke-2 sampai ke-5
- [ ] Accessor tarif_readable: "Rp. 1.020.000,00" atau null jika tidak diset
- [ ] Seeder update 4 kamar dengan tarif dan fasilitas lengkap

**Halaman Publik /layanan/rawat-inap**
- [ ] HTTP 200 accessible
- [ ] Dropdown "Pilih Tipe Kamar" menampilkan semua kamar aktif
- [ ] Ikon chevron putih di background hijau sesuai referensi screenshot
- [ ] State awal: konten tersembunyi + pesan "pilih tipe kamar"
- [ ] Pilih kamar → konten muncul dengan animasi fade+slide
- [ ] Foto utama (foto_1) tampil sebagai hero besar
- [ ] Placeholder gradient tampil jika belum ada foto
- [ ] Badge kelas tampil di pojok kiri atas foto utama
- [ ] Thumbnail row tampil untuk foto ke-2 sampai ke-5
- [ ] Klik thumbnail → hero foto berganti (tanpa reload, Alpine.js)
- [ ] Thumbnail aktif mendapat border highlight hijau
- [ ] Slot thumbnail kosong tampil sebagai kotak dashed (jika foto < 4)
- [ ] Nama kelas tampil dengan border kiri kuning emas (persis referensi)
- [ ] Fasilitas tampil sebagai numbered list (1. 2. 3. ...)
- [ ] Tarif tampil: "Tarif : Rp. X,00 / Hari"
- [ ] Catatan kecil tarif belum termasuk tindakan medis
- [ ] Jika tarif null → teks "hubungi administrasi"
- [ ] Tombol CTA: Hubungi Kami + WhatsApp
- [ ] Ganti dropdown ke kelas lain → semua konten update (foto, fasilitas, tarif)
- [ ] Responsive: 1 kolom di mobile (gallery atas, info bawah)

**Admin Panel — Form Kamar**
- [ ] Field tarif tampil di form (number input)
- [ ] Preview live tarif dalam format rupiah
- [ ] 5 slot foto tampil dalam grid
- [ ] Slot kosong: klik → browse file → preview langsung
- [ ] Slot dengan foto: hover → tombol "Ganti" + checkbox "Hapus"
- [ ] Ganti foto: upload baru → preview di tempat → simpan → foto lama dihapus dari storage
- [ ] Hapus foto: centang checkbox → simpan → foto dihapus dari storage + kolom null
- [ ] Foto_1 ditandai "★ Foto Utama"
- [ ] Validasi: setiap foto maks 2MB, format JPG/PNG
- [ ] Update tanpa mengubah foto: foto lama tetap tersimpan
- [ ] Urutan foto dipertahankan (foto_1 selalu foto_1, dst)
- [ ] Flash success/error tampil setelah simpan

**Integrasi**
- [ ] Navbar dropdown Layanan: "Rawat Inap" → /layanan/rawat-inap
- [ ] Route layanan.rawat-inap aktif dan match dengan controller
- [ ] @push('scripts') Alpine.js function rawatInap() di-load dengan benar
- [ ] $kamarsJson ter-encode dengan benar (tidak ada error JSON di console browser)
