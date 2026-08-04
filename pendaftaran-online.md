# Task: Pendaftaran Online — Form + Notifikasi + Konfirmasi via WhatsApp

Tambahkan halaman **Pendaftaran Online** ke website RS TK IV Sintang.
Pasien mengisi form, data tersimpan ke DB, muncul notifikasi sukses,
lalu ada tombol konfirmasi yang membuka WhatsApp ke nomor pendaftaran.

Konteks project: lihat `CLAUDE.md`. Stack: Laravel 10, MySQL, Tailwind CDN, Alpine.js.

---

## Alur Lengkap

```
Visitor buka /daftar-online
  └── Isi form pendaftaran (validasi client + server)
        └── Submit → data tersimpan DB + upload foto KTP & BPJS
              └── Halaman sukses / notifikasi:
                    ├── Ringkasan data yang didaftarkan
                    ├── Nomor pendaftaran (auto-generate)
                    └── Tombol "Konfirmasi via WhatsApp"
                          └── Buka wa.me/6283830331205?text=...
                                (pesan otomatis berisi nomor & nama pendaftar)
```

---

## Ringkasan Perubahan

| Area        | Yang Ditambahkan                                                        |
|-------------|-------------------------------------------------------------------------|
| Database    | rssite_pendaftarans                                                     |
| Model       | Pendaftaran                                                             |
| Routes      | /daftar-online (form) + /daftar-online/sukses/{kode} (notifikasi)      |
| Controller  | PendaftaranController@index + @store + @sukses                          |
| Views       | pendaftaran/index.blade.php + pendaftaran/sukses.blade.php              |
| Navbar      | Link "Daftar Online" (tombol CTA gold di navbar)                        |
| Admin       | Admin/PendaftaranController + views (list + detail)                     |
| Sidebar     | Entry "Pendaftaran Online" di grup admin                                |

---

## 1. Migration

```php
Schema::create('rssite_pendaftarans', function (Blueprint $table) {
    $table->id();

    // Kode unik pendaftaran: RS-YYYYMMDD-XXXX (e.g. RS-20250525-0042)
    $table->string('kode', 30)->unique();

    // Data pasien
    $table->string('nama_lengkap', 200);
    $table->string('nik', 20)->nullable();
    $table->string('tempat_lahir', 100)->nullable();
    $table->date('tanggal_lahir')->nullable();
    $table->enum('jenis_kelamin', ['Laki-laki', 'Perempuan']);
    $table->string('nomor_telepon', 20);
    $table->text('alamat');

    // Status & layanan
    $table->enum('status_pasien', ['Pasien Baru', 'Pasien Lama'])->default('Pasien Baru');
    $table->enum('jenis_layanan', ['Umum', 'BPJS', 'Asuransi Lain', 'TNI/POLRI'])->default('Umum');
    $table->string('nama_asuransi', 100)->nullable(); // jika jenis_layanan = Asuransi Lain

    // Tujuan
    $table->string('poli_tujuan', 150);
    $table->text('catatan')->nullable();

    // Upload dokumen
    $table->string('foto_ktp', 500)->nullable();       // wajib
    $table->string('foto_bpjs', 500)->nullable();      // wajib jika BPJS

    // Status admin
    $table->enum('status', ['Menunggu', 'Dikonfirmasi', 'Selesai', 'Dibatalkan'])
          ->default('Menunggu');
    $table->text('catatan_admin')->nullable();

    // Tracking WA konfirmasi
    $table->boolean('sudah_konfirmasi_wa')->default(false);
    $table->timestamp('dikonfirmasi_at')->nullable();

    $table->timestamps();
});
```

---

## 2. Model: app/Models/Pendaftaran.php

```php
<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Pendaftaran extends Model
{
    protected $table    = 'rssite_pendaftarans';
    protected $fillable = [
        'kode', 'nama_lengkap', 'nik', 'tempat_lahir', 'tanggal_lahir',
        'jenis_kelamin', 'nomor_telepon', 'alamat',
        'status_pasien', 'jenis_layanan', 'nama_asuransi',
        'poli_tujuan', 'catatan',
        'foto_ktp', 'foto_bpjs',
        'status', 'catatan_admin',
        'sudah_konfirmasi_wa', 'dikonfirmasi_at',
    ];

    protected $casts = [
        'tanggal_lahir'       => 'date',
        'sudah_konfirmasi_wa' => 'boolean',
        'dikonfirmasi_at'     => 'datetime',
    ];

    // Generate kode unik: RS-YYYYMMDD-XXXX
    public static function generateKode(): string
    {
        $prefix = 'RS-' . now()->format('Ymd') . '-';
        $last   = static::where('kode', 'like', $prefix . '%')
                        ->orderByDesc('kode')->first();
        $seq    = $last ? (intval(substr($last->kode, -4)) + 1) : 1;
        return $prefix . str_pad($seq, 4, '0', STR_PAD_LEFT);
    }

    // Accessor: URL foto KTP
    public function getFotoKtpUrlAttribute(): ?string
    {
        return $this->foto_ktp ? asset('storage/' . $this->foto_ktp) : null;
    }

    // Accessor: URL foto BPJS
    public function getFotoBpjsUrlAttribute(): ?string
    {
        return $this->foto_bpjs ? asset('storage/' . $this->foto_bpjs) : null;
    }

    // Accessor: tanggal lahir formatted
    public function getTanggalLahirReadableAttribute(): string
    {
        if (!$this->tanggal_lahir) return '-';
        return Carbon::parse($this->tanggal_lahir)->translatedFormat('d F Y');
    }

    // Accessor: teks pesan WhatsApp otomatis
    public function getPesanWaAttribute(): string
    {
        $nomor  = \App\Models\SiteSetting::get('wa_pendaftaran', '6283830331205');
        $lines  = [
            '*KONFIRMASI PENDAFTARAN ONLINE*',
            '*RS TK IV Sintang*',
            '─────────────────────',
            "Nomor Pendaftaran : *{$this->kode}*",
            "Nama              : {$this->nama_lengkap}",
            "NIK               : " . ($this->nik ?: '-'),
            "TTL               : " . ($this->tempat_lahir ? $this->tempat_lahir . ', ' : '') . $this->tanggal_lahir_readable,
            "Jenis Kelamin     : {$this->jenis_kelamin}",
            "No. Telepon       : {$this->nomor_telepon}",
            "Status Pasien     : {$this->status_pasien}",
            "Jenis Layanan     : {$this->jenis_layanan}",
            "Poli Tujuan       : {$this->poli_tujuan}",
            '─────────────────────',
            'Mohon konfirmasi pendaftaran ini. Terima kasih.',
        ];
        $pesan  = implode("\n", $lines);
        $nomor  = preg_replace('/^0/', '62', preg_replace('/\D/', '', $nomor));
        return "https://wa.me/{$nomor}?text=" . rawurlencode($pesan);
    }

    // Badge warna status
    public function getStatusBadgeAttribute(): array
    {
        return match($this->status) {
            'Menunggu'    => ['bg' => 'bg-yellow-100', 'text' => 'text-yellow-700'],
            'Dikonfirmasi'=> ['bg' => 'bg-blue-100',   'text' => 'text-blue-700'],
            'Selesai'     => ['bg' => 'bg-green-100',  'text' => 'text-green-700'],
            'Dibatalkan'  => ['bg' => 'bg-red-100',    'text' => 'text-red-500'],
            default       => ['bg' => 'bg-gray-100',   'text' => 'text-gray-600'],
        };
    }
}
```

---

## 3. Routes

```php
// Pendaftaran Online — Publik
Route::get('/daftar-online',              [PendaftaranController::class, 'index']) ->name('pendaftaran.form');
Route::post('/daftar-online',             [PendaftaranController::class, 'store']) ->name('pendaftaran.store');
Route::get('/daftar-online/sukses/{kode}',[PendaftaranController::class, 'sukses'])->name('pendaftaran.sukses');

// Admin Pendaftaran
Route::prefix('pendaftaran')->name('pendaftaran.')->middleware('auth')->group(function () {
    Route::get('/',            [Admin\PendaftaranController::class, 'index'])         ->name('index');
    Route::get('/{id}',        [Admin\PendaftaranController::class, 'show'])          ->name('show');
    Route::post('/{id}/status',[Admin\PendaftaranController::class, 'updateStatus'])  ->name('status');
    Route::delete('/{id}',     [Admin\PendaftaranController::class, 'destroy'])       ->name('destroy');
});
```

---

## 4. Controller Publik: app/Http/Controllers/PendaftaranController.php

```php
<?php
namespace App\Http\Controllers;

use App\Models\Pendaftaran;
use App\Models\Poliklinik;
use Illuminate\Http\Request;

class PendaftaranController extends Controller
{
    public function index()
    {
        // Load daftar poli aktif untuk dropdown pilihan
        $polikliniks = Poliklinik::aktif()->orderBy('urutan')->pluck('nama');

        return view('pendaftaran.index', compact('polikliniks'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama_lengkap'   => 'required|string|max:200',
            'nik'            => 'nullable|string|digits:16',
            'tempat_lahir'   => 'nullable|string|max:100',
            'tanggal_lahir'  => 'nullable|date|before:today',
            'jenis_kelamin'  => 'required|in:Laki-laki,Perempuan',
            'nomor_telepon'  => 'required|string|min:10|max:20',
            'alamat'         => 'required|string',
            'status_pasien'  => 'required|in:Pasien Baru,Pasien Lama',
            'jenis_layanan'  => 'required|in:Umum,BPJS,Asuransi Lain,TNI/POLRI',
            'nama_asuransi'  => 'nullable|string|max:100',
            'poli_tujuan'    => 'required|string|max:150',
            'catatan'        => 'nullable|string|max:1000',
            'foto_ktp'       => 'required|image|mimes:jpg,jpeg,png|max:3072',
            'foto_bpjs'      => 'required_if:jenis_layanan,BPJS|nullable|image|mimes:jpg,jpeg,png|max:3072',
        ], [
            'nama_lengkap.required'  => 'Nama lengkap wajib diisi.',
            'nik.digits'             => 'NIK harus 16 digit angka.',
            'jenis_kelamin.required' => 'Jenis kelamin wajib dipilih.',
            'nomor_telepon.required' => 'Nomor telepon wajib diisi.',
            'nomor_telepon.min'      => 'Nomor telepon minimal 10 digit.',
            'alamat.required'        => 'Alamat lengkap wajib diisi.',
            'status_pasien.required' => 'Status pasien wajib dipilih.',
            'jenis_layanan.required' => 'Jenis layanan wajib dipilih.',
            'poli_tujuan.required'   => 'Poli tujuan wajib dipilih.',
            'foto_ktp.required'      => 'Foto KTP wajib diupload.',
            'foto_ktp.image'         => 'Foto KTP harus berupa gambar (JPG/PNG).',
            'foto_ktp.max'           => 'Foto KTP maksimal 3 MB.',
            'foto_bpjs.required_if'  => 'Foto kartu BPJS wajib diupload untuk jenis layanan BPJS.',
            'foto_bpjs.max'          => 'Foto BPJS maksimal 3 MB.',
        ]);

        // Upload foto KTP
        $pathKtp  = $request->file('foto_ktp')->store('rssite/pendaftaran/ktp', 'public');

        // Upload foto BPJS (hanya jika BPJS)
        $pathBpjs = null;
        if ($request->hasFile('foto_bpjs')) {
            $pathBpjs = $request->file('foto_bpjs')->store('rssite/pendaftaran/bpjs', 'public');
        }

        $pendaftaran = Pendaftaran::create([
            'kode'           => Pendaftaran::generateKode(),
            'nama_lengkap'   => $request->nama_lengkap,
            'nik'            => $request->nik,
            'tempat_lahir'   => $request->tempat_lahir,
            'tanggal_lahir'  => $request->tanggal_lahir,
            'jenis_kelamin'  => $request->jenis_kelamin,
            'nomor_telepon'  => $request->nomor_telepon,
            'alamat'         => $request->alamat,
            'status_pasien'  => $request->status_pasien,
            'jenis_layanan'  => $request->jenis_layanan,
            'nama_asuransi'  => $request->nama_asuransi,
            'poli_tujuan'    => $request->poli_tujuan,
            'catatan'        => $request->catatan,
            'foto_ktp'       => $pathKtp,
            'foto_bpjs'      => $pathBpjs,
            'status'         => 'Menunggu',
        ]);

        return redirect()->route('pendaftaran.sukses', $pendaftaran->kode);
    }

    public function sukses(string $kode)
    {
        $pendaftaran = Pendaftaran::where('kode', $kode)->firstOrFail();
        return view('pendaftaran.sukses', compact('pendaftaran'));
    }
}
```

---

## 5. Controller Admin: app/Http/Controllers/Admin/PendaftaranController.php

```php
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

        // Filter status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Filter jenis layanan
        if ($request->filled('layanan')) {
            $query->where('jenis_layanan', $request->layanan);
        }

        // Search nama / kode / NIK
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

        // Stats untuk badge di header
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
            'status'         => 'required|in:Menunggu,Dikonfirmasi,Selesai,Dibatalkan',
            'catatan_admin'  => 'nullable|string|max:500',
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

        // Hapus file foto dari storage
        if ($p->foto_ktp)  Storage::disk('public')->delete($p->foto_ktp);
        if ($p->foto_bpjs) Storage::disk('public')->delete($p->foto_bpjs);

        $p->delete();
        return redirect()->route('admin.pendaftaran.index')
                         ->with('success', 'Data pendaftaran berhasil dihapus.');
    }
}
```

---

## 6. View Publik: resources/views/pendaftaran/index.blade.php

```blade
@extends('layouts.app')
@section('title', 'Pendaftaran Online — RS TK IV Sintang')
@section('content')

@include('partials.page-header', ['judul' => 'Pendaftaran Online'])

<section class="py-12 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-3xl">

    {{-- INFO BOX di atas form --}}
    <div class="bg-blue-50 border border-blue-200 rounded-2xl p-5 mb-8 flex gap-4">
      <i class="fa fa-info-circle text-blue-500 text-2xl flex-shrink-0 mt-0.5"></i>
      <div class="text-sm text-blue-700 space-y-1">
        <p class="font-semibold">Informasi Penting:</p>
        <ul class="list-disc list-inside space-y-1 text-blue-600">
          <li>Pendaftaran online berlaku untuk kunjungan hari ini atau maksimal H+1</li>
          <li>Setelah mendaftar, <strong>wajib konfirmasi via WhatsApp</strong> ke bagian pendaftaran</li>
          <li>Bawa KTP asli dan kartu BPJS (jika ada) saat datang ke RS</li>
          <li>Jadwal poli dapat berubah sewaktu-waktu</li>
        </ul>
      </div>
    </div>

    {{-- FORM CARD --}}
    <div class="bg-white rounded-2xl shadow-sm p-8"
         x-data="formPendaftaran()">

      <h2 class="font-playfair text-xl font-bold text-gray-800 mb-6 pb-4 border-b border-gray-100">
        <i class="fa fa-clipboard-list text-green-600 mr-2"></i>
        Formulir Pendaftaran Online
      </h2>

      <form method="POST" action="{{ route('pendaftaran.store') }}"
            enctype="multipart/form-data"
            @submit="submitting = true"
            class="space-y-6">
        @csrf

        {{-- ===== SECTION 1: Data Diri ===== --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4
                     flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">1</span>
            Data Diri Pasien
          </h3>

          <div class="space-y-4">

            {{-- Nama Lengkap --}}
            <div>
              <label class="form-label">Nama Lengkap <span class="text-red-500">*</span></label>
              <input type="text" name="nama_lengkap"
                     value="{{ old('nama_lengkap') }}"
                     placeholder="Masukkan nama lengkap sesuai KTP"
                     class="form-input @error('nama_lengkap') border-red-400 @enderror">
              @error('nama_lengkap')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- NIK --}}
            <div>
              <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
              <input type="text" name="nik"
                     value="{{ old('nik') }}"
                     placeholder="16 digit angka NIK"
                     maxlength="16"
                     inputmode="numeric"
                     class="form-input @error('nik') border-red-400 @enderror">
              @error('nik')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Tempat & Tanggal Lahir (2 kolom) --}}
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
              <div>
                <label class="form-label">Tempat Lahir</label>
                <input type="text" name="tempat_lahir"
                       value="{{ old('tempat_lahir') }}"
                       placeholder="Kota/Kabupaten tempat lahir"
                       class="form-input @error('tempat_lahir') border-red-400 @enderror">
                @error('tempat_lahir')
                  <p class="form-error">{{ $message }}</p>
                @enderror
              </div>
              <div>
                <label class="form-label">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir"
                       value="{{ old('tanggal_lahir') }}"
                       max="{{ date('Y-m-d') }}"
                       class="form-input @error('tanggal_lahir') border-red-400 @enderror">
                @error('tanggal_lahir')
                  <p class="form-error">{{ $message }}</p>
                @enderror
              </div>
            </div>

            {{-- Jenis Kelamin --}}
            <div>
              <label class="form-label">Jenis Kelamin <span class="text-red-500">*</span></label>
              <div class="flex gap-6 mt-2">
                <label class="flex items-center gap-2 cursor-pointer group">
                  <input type="radio" name="jenis_kelamin" value="Laki-laki"
                         {{ old('jenis_kelamin') === 'Laki-laki' ? 'checked' : '' }}
                         class="w-4 h-4 text-green-600 focus:ring-green-500">
                  <span class="text-sm text-gray-700 group-hover:text-green-700 transition-colors">
                    <i class="fa fa-mars text-blue-500 mr-1"></i> Laki-laki
                  </span>
                </label>
                <label class="flex items-center gap-2 cursor-pointer group">
                  <input type="radio" name="jenis_kelamin" value="Perempuan"
                         {{ old('jenis_kelamin') === 'Perempuan' ? 'checked' : '' }}
                         class="w-4 h-4 text-green-600 focus:ring-green-500">
                  <span class="text-sm text-gray-700 group-hover:text-green-700 transition-colors">
                    <i class="fa fa-venus text-pink-500 mr-1"></i> Perempuan
                  </span>
                </label>
              </div>
              @error('jenis_kelamin')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Nomor Telepon --}}
            <div>
              <label class="form-label">Nomor Telepon <span class="text-red-500">*</span></label>
              <div class="flex">
                <span class="inline-flex items-center px-3 rounded-l-xl border border-r-0
                             border-gray-300 bg-gray-50 text-gray-500 text-sm">
                  +62
                </span>
                <input type="tel" name="nomor_telepon"
                       value="{{ old('nomor_telepon') }}"
                       placeholder="08xxxxxxxxxx"
                       inputmode="numeric"
                       class="form-input rounded-l-none flex-1
                              @error('nomor_telepon') border-red-400 @enderror">
              </div>
              @error('nomor_telepon')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Alamat Lengkap --}}
            <div>
              <label class="form-label">Alamat Lengkap <span class="text-red-500">*</span></label>
              <textarea name="alamat" rows="3"
                        placeholder="Jalan, RT/RW, Kelurahan, Kecamatan, Kabupaten/Kota"
                        class="form-input @error('alamat') border-red-400 @enderror">{{ old('alamat') }}</textarea>
              @error('alamat')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

          </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-100"></div>

        {{-- ===== SECTION 2: Status & Layanan ===== --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4
                     flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">2</span>
            Status & Jenis Layanan
          </h3>

          <div class="space-y-4">

            {{-- Status Pasien --}}
            <div>
              <label class="form-label">Status Pasien <span class="text-red-500">*</span></label>
              <div class="grid grid-cols-2 gap-3 mt-2">
                @foreach(['Pasien Baru', 'Pasien Lama'] as $status)
                  <label class="relative flex items-center gap-3 p-4 border-2 rounded-xl cursor-pointer
                                transition-all hover:border-green-500
                                {{ old('status_pasien', 'Pasien Baru') === $status
                                   ? 'border-green-600 bg-green-50'
                                   : 'border-gray-200' }}">
                    <input type="radio" name="status_pasien" value="{{ $status }}"
                           {{ old('status_pasien', 'Pasien Baru') === $status ? 'checked' : '' }}
                           class="sr-only">
                    <i class="fa {{ $status === 'Pasien Baru' ? 'fa-user-plus' : 'fa-user-check' }}
                               text-xl {{ old('status_pasien', 'Pasien Baru') === $status ? 'text-green-600' : 'text-gray-400' }}"></i>
                    <span class="text-sm font-medium text-gray-700">{{ $status }}</span>
                  </label>
                @endforeach
              </div>
              @error('status_pasien')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Jenis Layanan/Asuransi --}}
            <div>
              <label class="form-label">Jenis Layanan / Asuransi <span class="text-red-500">*</span></label>
              <select name="jenis_layanan"
                      x-model="jenisLayanan"
                      class="form-input @error('jenis_layanan') border-red-400 @enderror">
                @foreach(['Umum', 'BPJS', 'Asuransi Lain', 'TNI/POLRI'] as $jenis)
                  <option value="{{ $jenis }}"
                          {{ old('jenis_layanan', 'Umum') === $jenis ? 'selected' : '' }}>
                    {{ $jenis }}
                  </option>
                @endforeach
              </select>
              @error('jenis_layanan')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Nama Asuransi (muncul jika pilih "Asuransi Lain") --}}
            <div x-show="jenisLayanan === 'Asuransi Lain'" x-transition>
              <label class="form-label">Nama Asuransi</label>
              <input type="text" name="nama_asuransi"
                     value="{{ old('nama_asuransi') }}"
                     placeholder="Contoh: Prudential, AXA Mandiri, dll"
                     class="form-input">
            </div>

          </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-100"></div>

        {{-- ===== SECTION 3: Tujuan Berobat ===== --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4
                     flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">3</span>
            Tujuan Berobat
          </h3>

          <div class="space-y-4">

            {{-- Poli Tujuan --}}
            <div>
              <label class="form-label">Poli Tujuan <span class="text-red-500">*</span></label>
              <select name="poli_tujuan"
                      class="form-input @error('poli_tujuan') border-red-400 @enderror">
                <option value="">-- Pilih Poli Tujuan --</option>
                @foreach($polikliniks as $poli)
                  <option value="{{ $poli }}"
                          {{ old('poli_tujuan') === $poli ? 'selected' : '' }}>
                    {{ $poli }}
                  </option>
                @endforeach
                {{-- Opsi manual jika poli tidak ada di list --}}
                <option value="Lainnya">Lainnya (sebutkan di catatan)</option>
              </select>
              @error('poli_tujuan')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Catatan --}}
            <div>
              <label class="form-label">Catatan / Keluhan Utama</label>
              <textarea name="catatan" rows="3"
                        maxlength="1000"
                        placeholder="Opsional — tuliskan keluhan utama atau informasi tambahan"
                        class="form-input">{{ old('catatan') }}</textarea>
              <p class="text-xs text-gray-400 mt-1">Maksimal 1000 karakter</p>
            </div>

          </div>
        </div>

        {{-- Divider --}}
        <div class="border-t border-gray-100"></div>

        {{-- ===== SECTION 4: Upload Dokumen ===== --}}
        <div>
          <h3 class="text-sm font-semibold text-green-700 uppercase tracking-wide mb-4
                     flex items-center gap-2">
            <span class="w-6 h-6 bg-green-700 text-white rounded-full flex items-center justify-center text-xs">4</span>
            Upload Dokumen
          </h3>

          <div class="space-y-5">

            {{-- Upload KTP --}}
            <div>
              <label class="form-label">
                Foto KTP <span class="text-red-500">*</span>
                <span class="text-xs text-gray-400 font-normal ml-1">(JPG/PNG, maks 3MB)</span>
              </label>
              <div class="upload-area" x-data="{ preview: null, name: '' }"
                   @dragover.prevent @drop.prevent="onDrop($event, 'foto_ktp')">

                <template x-if="preview">
                  <div class="text-center">
                    <img :src="preview" class="max-h-40 mx-auto rounded-xl shadow mb-2 object-contain">
                    <p class="text-xs text-green-700 font-medium" x-text="name"></p>
                    <button type="button" @click="preview=null; name=''; $refs.inputKtp.value=''"
                            class="text-xs text-red-500 hover:underline mt-1">Ganti</button>
                  </div>
                </template>

                <template x-if="!preview">
                  <label class="flex flex-col items-center cursor-pointer py-2">
                    <i class="fa fa-id-card text-4xl text-gray-300 mb-2"></i>
                    <span class="text-sm text-gray-500">Upload Foto KTP</span>
                    <span class="text-xs text-green-600 font-medium hover:underline mt-1">Pilih File</span>
                    <input type="file" name="foto_ktp" x-ref="inputKtp" class="hidden"
                           accept=".jpg,.jpeg,.png"
                           @change="onFileChange($event)">
                  </label>
                </template>

              </div>
              @error('foto_ktp')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

            {{-- Upload BPJS (hanya jika jenis_layanan = BPJS) --}}
            <div x-show="jenisLayanan === 'BPJS'" x-transition>
              <label class="form-label">
                Foto Kartu BPJS <span class="text-red-500">*</span>
                <span class="text-xs text-gray-400 font-normal ml-1">(Wajib jika BPJS, JPG/PNG, maks 3MB)</span>
              </label>
              <div class="upload-area" x-data="{ preview: null, name: '' }"
                   @dragover.prevent @drop.prevent="onDrop($event, 'foto_bpjs')">

                <template x-if="preview">
                  <div class="text-center">
                    <img :src="preview" class="max-h-40 mx-auto rounded-xl shadow mb-2 object-contain">
                    <p class="text-xs text-green-700 font-medium" x-text="name"></p>
                    <button type="button" @click="preview=null; name=''; $refs.inputBpjs.value=''"
                            class="text-xs text-red-500 hover:underline mt-1">Ganti</button>
                  </div>
                </template>

                <template x-if="!preview">
                  <label class="flex flex-col items-center cursor-pointer py-2">
                    <i class="fa fa-id-badge text-4xl text-gray-300 mb-2"></i>
                    <span class="text-sm text-gray-500">Upload Foto Kartu BPJS</span>
                    <span class="text-xs text-green-600 font-medium hover:underline mt-1">Pilih File</span>
                    <input type="file" name="foto_bpjs" x-ref="inputBpjs" class="hidden"
                           accept=".jpg,.jpeg,.png"
                           @change="onFileChange($event)">
                  </label>
                </template>

              </div>
              @error('foto_bpjs')
                <p class="form-error">{{ $message }}</p>
              @enderror
            </div>

          </div>
        </div>

        {{-- ===== SUBMIT BUTTON ===== --}}
        <div class="pt-4 border-t border-gray-100">

          {{-- Disclaimer --}}
          <p class="text-xs text-gray-400 mb-4">
            Dengan menekan tombol di bawah, saya menyatakan bahwa data yang saya masukkan
            adalah benar dan saya bersedia mengikuti prosedur pendaftaran RS TK IV Sintang.
          </p>

          <button type="submit"
                  :disabled="submitting"
                  class="w-full py-4 px-6 bg-green-700 text-white font-semibold rounded-xl
                         text-base hover:bg-green-800 transition-colors
                         disabled:opacity-60 disabled:cursor-not-allowed
                         flex items-center justify-center gap-3">
            <template x-if="!submitting">
              <span><i class="fa fa-paper-plane mr-2"></i>Kirim Pendaftaran</span>
            </template>
            <template x-if="submitting">
              <span>
                <i class="fa fa-spinner fa-spin mr-2"></i>Memproses...
              </span>
            </template>
          </button>

        </div>

      </form>
    </div>

  </div>
</section>

@push('scripts')
<script>
function formPendaftaran() {
  return {
    jenisLayanan: '{{ old('jenis_layanan', 'Umum') }}',
    submitting:   false,

    onFileChange(e) {
      const f = e.target.files[0];
      if (!f) return;
      this.name = f.name;
      const reader = new FileReader();
      reader.onload = (ev) => { this.preview = ev.target.result; };
      reader.readAsDataURL(f);
    },

    onDrop(e, fieldName) {
      const f = e.dataTransfer.files[0];
      if (!f) return;
      this.name = f.name;
      const reader = new FileReader();
      reader.onload = (ev) => { this.preview = ev.target.result; };
      reader.readAsDataURL(f);
      // Assign file ke input yang sesuai
      const dt = new DataTransfer();
      dt.items.add(f);
      const ref = fieldName === 'foto_ktp' ? this.$refs.inputKtp : this.$refs.inputBpjs;
      if (ref) ref.files = dt.files;
    }
  }
}
</script>
@endpush

@endsection
```

---

## 7. View Sukses: resources/views/pendaftaran/sukses.blade.php

```blade
@extends('layouts.app')
@section('title', 'Pendaftaran Berhasil — RS TK IV Sintang')
@section('content')

<section class="py-16 bg-gray-50 min-h-screen">
  <div class="container mx-auto px-4 max-w-2xl">

    {{-- ===== NOTIFIKASI SUKSES ===== --}}
    <div class="bg-white rounded-3xl shadow-lg overflow-hidden">

      {{-- Header hijau --}}
      <div class="bg-gradient-to-r from-green-700 to-green-500 px-8 py-10 text-center">
        <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center mx-auto mb-4 shadow-lg">
          <i class="fa fa-check-circle text-green-600 text-4xl"></i>
        </div>
        <h1 class="font-playfair text-2xl font-bold text-white mb-2">
          Pendaftaran Berhasil!
        </h1>
        <p class="text-green-100 text-sm">
          Data Anda telah kami terima. Simpan nomor pendaftaran berikut.
        </p>
      </div>

      <div class="px-8 py-8">

        {{-- Nomor Pendaftaran (highlight besar) --}}
        <div class="bg-green-50 border-2 border-green-200 rounded-2xl p-5 text-center mb-6">
          <p class="text-xs text-green-600 font-medium uppercase tracking-wide mb-1">
            Nomor Pendaftaran Anda
          </p>
          <p class="font-mono text-3xl font-bold text-green-800 tracking-widest">
            {{ $pendaftaran->kode }}
          </p>
          <p class="text-xs text-gray-400 mt-2">
            {{ now()->translatedFormat('l, d F Y') }}
          </p>
        </div>

        {{-- Ringkasan Data --}}
        <div class="mb-6">
          <h3 class="font-semibold text-gray-700 text-sm mb-3 pb-2 border-b border-gray-100">
            Ringkasan Pendaftaran
          </h3>
          <div class="space-y-2">
            @php
            $rows = [
              ['Nama Lengkap',   $pendaftaran->nama_lengkap],
              ['NIK',            $pendaftaran->nik ?: '-'],
              ['Jenis Kelamin',  $pendaftaran->jenis_kelamin],
              ['Nomor Telepon',  $pendaftaran->nomor_telepon],
              ['Status Pasien',  $pendaftaran->status_pasien],
              ['Jenis Layanan',  $pendaftaran->jenis_layanan],
              ['Poli Tujuan',    $pendaftaran->poli_tujuan],
            ];
            @endphp
            @foreach($rows as [$label, $value])
              <div class="flex justify-between items-start py-2 border-b border-gray-50 text-sm">
                <span class="text-gray-500 w-36 flex-shrink-0">{{ $label }}</span>
                <span class="text-gray-800 font-medium text-right">{{ $value }}</span>
              </div>
            @endforeach
            @if($pendaftaran->catatan)
              <div class="py-2 text-sm">
                <p class="text-gray-500 mb-1">Catatan</p>
                <p class="text-gray-700 bg-gray-50 rounded-lg p-3 text-xs">{{ $pendaftaran->catatan }}</p>
              </div>
            @endif
          </div>
        </div>

        {{-- INFO: Langkah selanjutnya --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-2xl p-4 mb-6">
          <p class="text-sm font-semibold text-yellow-800 mb-2 flex items-center gap-2">
            <i class="fa fa-exclamation-triangle"></i>
            Langkah Selanjutnya — WAJIB
          </p>
          <ol class="text-sm text-yellow-700 space-y-1.5 list-decimal list-inside">
            <li>Klik tombol <strong>"Konfirmasi via WhatsApp"</strong> di bawah</li>
            <li>Kirim pesan yang sudah otomatis terisi ke bagian pendaftaran RS</li>
            <li>Tunggu konfirmasi dari petugas sebelum datang ke RS</li>
            <li>Bawa <strong>KTP asli</strong> dan kartu BPJS (jika ada) saat datang</li>
          </ol>
        </div>

        {{-- ===== TOMBOL KONFIRMASI WA ===== --}}
        <a href="{{ $pendaftaran->pesan_wa }}"
           target="_blank"
           rel="noopener noreferrer"
           class="block w-full py-4 px-6 rounded-2xl text-center font-bold text-white text-base
                  transition-all duration-200 hover:scale-[1.02] active:scale-[0.98]
                  shadow-lg hover:shadow-xl"
           style="background: linear-gradient(135deg, #25D366, #128C7E);">
          <i class="fab fa-whatsapp text-2xl mr-2 align-middle"></i>
          Konfirmasi via WhatsApp
          <span class="block text-xs font-normal mt-0.5 opacity-80">
            Klik untuk membuka WhatsApp bagian pendaftaran
          </span>
        </a>

        {{-- Nomor WA terlihat (jika tombol tidak terbuka) --}}
        <p class="text-center text-xs text-gray-400 mt-3">
          Atau hubungi langsung:
          <a href="tel:+6283830331205"
             class="text-green-600 font-medium hover:underline">
            0838-3033-1205
          </a>
        </p>

        {{-- Tombol kembali --}}
        <div class="mt-6 pt-4 border-t border-gray-100 flex gap-3">
          <a href="{{ route('pendaftaran.form') }}"
             class="flex-1 text-center py-3 border border-gray-300 text-gray-600 rounded-xl
                    text-sm hover:bg-gray-50 transition-colors">
            <i class="fa fa-plus mr-1"></i> Daftar Lagi
          </a>
          <a href="{{ route('home') }}"
             class="flex-1 text-center py-3 bg-green-700 text-white rounded-xl
                    text-sm hover:bg-green-800 transition-colors font-medium">
            <i class="fa fa-home mr-1"></i> Kembali ke Beranda
          </a>
        </div>

      </div>
    </div>

  </div>
</section>

@endsection
```

---

## 8. Views Admin

### admin/pendaftaran/index.blade.php

```
@extends('layouts.admin')
@section('title', 'Kelola Pendaftaran Online')
@section('content')

HEADER: Judul "Pendaftaran Online"

STATS ROW (4 card):
- Total (abu): $stats['total'] — fa-clipboard-list
- Menunggu (kuning): $stats['menunggu'] — fa-clock → highlight jika > 0
- Dikonfirmasi (biru): $stats['dikonfirmasi'] — fa-check
- Selesai (hijau): $stats['selesai'] — fa-check-double

FILTER BAR (form GET flex):
- Input search name="q" placeholder="Cari nama, kode, NIK, telepon..."
- Select status: Semua | Menunggu | Dikonfirmasi | Selesai | Dibatalkan
- Select layanan: Semua | Umum | BPJS | Asuransi Lain | TNI/POLRI
- Tombol [Filter] [Reset]

TABEL (rounded-xl shadow overflow-hidden):
Header: # | Kode | Nama | Poli | Layanan | Tgl Daftar | Status | Aksi

Per baris:
- Nomor
- Kode (font-mono text-sm text-green-700 font-bold)
- Nama + jenis kelamin kecil (♂/♀)
- Poli tujuan
- Badge layanan: Umum abu, BPJS biru, TNI hijau tua
- Tanggal created_at d/m/Y H:i
- Badge status dengan warna dari $p->status_badge
- Aksi: [Detail] [Hapus confirm]

Pagination

@endsection
```

### admin/pendaftaran/show.blade.php

```
@extends('layouts.admin')
@section('title', 'Detail Pendaftaran ' . $p->kode)
@section('content')

HEADER: Breadcrumb + Judul kode + Badge status

LAYOUT 2 KOLOM (lg:grid-cols-3):

KIRI (2/3):

CARD 1 — Data Pasien:
Tabel dua kolom (label: value):
- Nomor Pendaftaran: kode (font-mono bold)
- Nama Lengkap
- NIK
- TTL: tempat_lahir + tanggal_lahir_readable
- Jenis Kelamin
- Nomor Telepon (link tel:)
- Alamat

CARD 2 — Data Berobat:
- Status Pasien
- Jenis Layanan (badge)
- Nama Asuransi (jika ada)
- Poli Tujuan
- Catatan (jika ada — dalam box gray)

CARD 3 — Foto Dokumen:
Grid 2 kolom:
- Foto KTP: <img src="$p->foto_ktp_url"> clickable (buka full di tab baru)
             + link [Lihat KTP]
- Foto BPJS: sama (tampilkan hanya jika ada)

KANAN (1/3):

CARD — Update Status (sticky):
Judul: "Update Status Pendaftaran"

Form POST route admin.pendaftaran.status:
- Select status: Menunggu | Dikonfirmasi | Selesai | Dibatalkan
  (pre-selected: $p->status)
- Textarea catatan_admin placeholder "Catatan untuk pasien (opsional)"
  value: $p->catatan_admin
- Tombol [Simpan Status] full-width

Divider

CARD — Konfirmasi WA:
- Info nomor WA pendaftaran
- Tombol [Buka WhatsApp Pasien] → wa.me + nomor pasien
  (link ke nomor HP pasien, bukan nomor pendaftaran RS)
- Tombol [Lihat Pesan Konfirmasi] → $p->pesan_wa (link WA lengkap dengan pesan otomatis)

CARD — Info Waktu:
- Daftar: created_at->format('d/m/Y H:i')
- Update terakhir: updated_at->diffForHumans()

@endsection
```

---

## 9. SiteSetting — Nomor WA Pendaftaran

Tambahkan key setting baru di DatabaseSeeder:

```php
\App\Models\SiteSetting::firstOrCreate(
    ['key' => 'wa_pendaftaran'],
    ['value' => '6283830331205']
);
```

Ini memungkinkan nomor WA pendaftaran diubah via admin setting tanpa edit kode.
Tambahkan field ini di halaman `admin/setting/index.blade.php` section "Kontak":

```blade
{{-- Di dalam form setting, section Identitas RS --}}
<div>
  <label class="form-label">WhatsApp Pendaftaran Online</label>
  <div class="flex">
    <span class="prefix-input">+62</span>
    <input type="text" name="wa_pendaftaran"
           value="{{ $settings['wa_pendaftaran'] ?? '6283830331205' }}"
           placeholder="6283830331205"
           class="form-input rounded-l-none">
  </div>
  <p class="hint">Format: 62xxxxxxxxxx (tanpa + dan tanpa spasi)</p>
</div>
```

---

## 10. Navbar — Tombol "Daftar Online"

Tombol CTA di navbar sudah ada dari `CLAUDE.md` (warna gold). Pastikan href-nya:

```blade
<a href="{{ route('pendaftaran.form') }}"
   class="bg-yellow-500 text-white px-5 py-2.5 rounded-xl text-sm font-semibold
          hover:bg-yellow-400 transition-colors shadow-sm">
  <i class="fa fa-clipboard-list mr-1"></i> Daftar Online
</a>
```

---

## 11. Sidebar Admin

```blade
<a href="{{ route('admin.pendaftaran.index') }}"
   class="sidebar-link {{ request()->routeIs('admin.pendaftaran.*') ? 'active' : '' }}">
  <i class="fa fa-clipboard-list w-5"></i>
  <span>Pendaftaran Online</span>
  {{-- Badge notifikasi jika ada yang menunggu --}}
  @php $menunggu = \App\Models\Pendaftaran::where('status','Menunggu')->count(); @endphp
  @if($menunggu > 0)
    <span class="ml-auto bg-red-500 text-white text-xs rounded-full
                 w-5 h-5 flex items-center justify-center flex-shrink-0">
      {{ $menunggu > 9 ? '9+' : $menunggu }}
    </span>
  @endif
</a>
```

---

## 12. CSS Tambahan (layouts/app.blade.php)

```css
/* Form labels & inputs konsisten */
.form-label {
  display: block;
  font-size: 0.875rem;
  font-weight: 500;
  color: #374151;
  margin-bottom: 0.375rem;
}
.form-input {
  width: 100%;
  padding: 0.625rem 0.875rem;
  border: 1px solid #D1D5DB;
  border-radius: 0.75rem;
  font-size: 0.875rem;
  color: #111827;
  background: white;
  transition: border-color 0.15s, box-shadow 0.15s;
  outline: none;
}
.form-input:focus {
  border-color: #2D6A4F;
  box-shadow: 0 0 0 3px rgba(45,106,79,0.1);
}
.form-error {
  font-size: 0.75rem;
  color: #EF4444;
  margin-top: 0.25rem;
}
.upload-area {
  border: 2px dashed #D1D5DB;
  border-radius: 1rem;
  padding: 1.5rem;
  text-align: center;
  transition: border-color 0.2s, background 0.2s;
  cursor: pointer;
  min-height: 120px;
  display: flex;
  align-items: center;
  justify-content: center;
}
.upload-area:hover {
  border-color: #2D6A4F;
  background: #F0FDF4;
}

/* Radio card selection effect */
input[type=radio]:checked + * { color: #2D6A4F; }

/* Nomor pendaftaran mono font */
.kode-pendaftaran {
  font-family: 'Courier New', monospace;
  letter-spacing: 0.15em;
}
```

---

## Checklist Akhir

**Database & Model**
- [ ] Migration rssite_pendaftarans berjalan
- [ ] generateKode() menghasilkan format RS-YYYYMMDD-XXXX unik
- [ ] Accessor pesan_wa membangun URL wa.me dengan pesan lengkap
- [ ] Accessor status_badge mengembalikan class Tailwind sesuai status
- [ ] SiteSetting key 'wa_pendaftaran' ter-seed dengan nilai 6283830331205

**Halaman Form /daftar-online**
- [ ] HTTP 200 accessible
- [ ] 4 section form (Data Diri, Status & Layanan, Tujuan, Dokumen) tampil rapi
- [ ] Field Nama Lengkap, NIK, TTL, Jenis Kelamin, Telepon, Alamat tampil
- [ ] Radio status pasien (Pasien Baru / Pasien Lama) — card style
- [ ] Dropdown jenis layanan (Umum/BPJS/Asuransi Lain/TNI)
- [ ] Field nama asuransi muncul hanya jika pilih "Asuransi Lain" (Alpine.js)
- [ ] Dropdown poli tujuan dari DB rssite_polikliniks
- [ ] Textarea catatan opsional
- [ ] Upload KTP: drag & drop + preview sebelum submit
- [ ] Upload BPJS: tampil HANYA jika jenis_layanan = BPJS (Alpine.js x-show)
- [ ] Tombol submit: disable + spinner saat proses
- [ ] Validasi server: semua field wajib tervalidasi
- [ ] Validasi: foto BPJS required_if jenis_layanan = BPJS
- [ ] Validasi: NIK harus 16 digit jika diisi
- [ ] Error message tampil per field
- [ ] old() value ter-restore setelah validasi gagal
- [ ] Upload foto KTP tersimpan ke storage/rssite/pendaftaran/ktp/
- [ ] Upload foto BPJS tersimpan ke storage/rssite/pendaftaran/bpjs/
- [ ] Kode unik ter-generate dan disimpan ke DB

**Halaman Sukses /daftar-online/sukses/{kode}**
- [ ] HTTP 200 setelah submit berhasil
- [ ] Kode tidak valid / tidak ditemukan → 404
- [ ] Ikon centang hijau + judul "Pendaftaran Berhasil!"
- [ ] Nomor pendaftaran tampil besar (font-mono, letter-spacing)
- [ ] Ringkasan data pasien tampil lengkap
- [ ] INFO BOX langkah selanjutnya tampil jelas
- [ ] Tombol "Konfirmasi via WhatsApp" warna hijau WhatsApp
- [ ] Klik tombol → buka wa.me dengan pesan otomatis berisi kode + data pasien
- [ ] Pesan WA berisi: nomor pendaftaran, nama, NIK, TTL, poli, layanan
- [ ] Nomor WA tampil teks (0838-3033-1205) sebagai fallback
- [ ] Tombol "Daftar Lagi" dan "Kembali ke Beranda" tampil di bawah

**Admin Panel /admin/pendaftaran**
- [ ] HTTP 200, auth protected
- [ ] Stats row: Total, Menunggu (kuning), Dikonfirmasi (biru), Selesai (hijau)
- [ ] Filter search (nama/kode/NIK/telepon) berfungsi
- [ ] Filter status berfungsi
- [ ] Filter jenis layanan berfungsi
- [ ] Tabel: kode mono hijau, badge status berwarna, badge layanan
- [ ] Pagination berfungsi
- [ ] Halaman detail: semua data pasien tampil lengkap
- [ ] Foto KTP tampil + link buka full di tab baru
- [ ] Foto BPJS tampil hanya jika ada
- [ ] Form update status: pilih status + catatan admin → simpan
- [ ] Tombol WA ke pasien: membuka wa.me dengan nomor HP pasien
- [ ] Tombol lihat pesan konfirmasi: URL wa.me lengkap dengan teks otomatis
- [ ] Hapus pendaftaran: foto KTP + BPJS ikut terhapus dari storage

**Navbar & Sidebar**
- [ ] Tombol "Daftar Online" gold di navbar → route pendaftaran.form
- [ ] Sidebar admin "Pendaftaran Online" dengan badge merah jika ada yang menunggu
- [ ] Badge menghilang jika tidak ada pendaftaran menunggu
- [ ] Nomor WA di setting bisa diubah via admin/setting
