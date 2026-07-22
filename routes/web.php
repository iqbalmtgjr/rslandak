<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\BeritaController;
use App\Http\Controllers\ProfilController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\LeafletController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\HeroController;
use App\Http\Controllers\Admin\LayananController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\BeritaController as AdminBeritaController;
use App\Http\Controllers\Admin\KamarController;
use App\Http\Controllers\Admin\SiteSettingController;
use App\Http\Controllers\Admin\ProfilController as AdminProfilController;
use App\Http\Controllers\Admin\DownloadController as AdminDownloadController;
use App\Http\Controllers\Admin\LeafletController as AdminLeafletController;
use App\Http\Controllers\LayananController as PublicLayananController;
use App\Http\Controllers\InformasiController;
use App\Http\Controllers\Admin\PoliklinikController as AdminPoliklinikController;
use App\Http\Controllers\Admin\Pelayanan24JamController as AdminPelayanan24JamController;
use App\Http\Controllers\Admin\AlurPelayananController as AdminAlurPelayananController;
use App\Http\Controllers\PendaftaranController;
use App\Http\Controllers\Admin\PendaftaranController as AdminPendaftaranController;

// === PUBLIC ===
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/berita',        [BeritaController::class, 'index'])->name('berita.index');
Route::get('/berita/{slug}', [BeritaController::class, 'show'])->name('berita.show');

// Download — Publik
Route::get('/download',            [DownloadController::class, 'index'])->name('download.index');
Route::get('/download/{id}/unduh', [DownloadController::class, 'unduh'])->name('download.unduh');

// Layanan — Publik
Route::prefix('layanan')->name('layanan.')->group(function () {
    Route::get('/poliklinik',        [PublicLayananController::class, 'poliklinik'])->name('poliklinik.index');
    Route::get('/poliklinik/{slug}', [PublicLayananController::class, 'poliklinikDetail'])->name('poliklinik.show');
    Route::get('/rawat-inap',        [PublicLayananController::class, 'rawatInap'])->name('rawat-inap');
    Route::get('/pelayanan-24-jam',  [PublicLayananController::class, 'pelayanan24Jam'])->name('pelayanan-24-jam');
});

// Leaflet & Poster — Publik
Route::get('/leaflet-poster', [LeafletController::class, 'index'])->name('leaflet.index');

// Informasi — Publik
Route::prefix('informasi')->name('informasi.')->group(function () {
    Route::get('/alur-pelayanan', [InformasiController::class, 'alurPelayanan'])->name('alur-pelayanan');
    Route::get('/dokter',         [InformasiController::class, 'dokter'])        ->name('dokter');
});

// Profil RS — Publik
Route::prefix('profil')->name('profil.')->group(function () {
    Route::get('/',          fn() => redirect()->route('profil.visi-misi'))->name('index');
    Route::get('/visi-misi', [ProfilController::class, 'visiMisi'])->name('visi-misi');
    Route::get('/profil-rs', [ProfilController::class, 'profilRs'])->name('profil-rs');
    Route::get('/direktur',  [ProfilController::class, 'direktur'])->name('direktur');
});

// === PENDAFTARAN ONLINE — Publik ===
Route::get('/daftar-online',               [PendaftaranController::class, 'index']) ->name('pendaftaran.form');
Route::post('/daftar-online',              [PendaftaranController::class, 'store']) ->name('pendaftaran.store');
Route::get('/daftar-online/sukses/{kode}', [PendaftaranController::class, 'sukses'])->name('pendaftaran.sukses');

// === AUTH ===
Route::get('/admin/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/admin/login', [AuthController::class, 'login']);
Route::post('/admin/logout', [AuthController::class, 'logout'])->name('logout');

// === ADMIN ===
Route::prefix('admin')->middleware('auth')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    Route::resource('hero', HeroController::class);
    Route::post('hero/{hero}/toggle', [HeroController::class, 'toggle'])->name('hero.toggle');
    Route::post('hero/reorder', [HeroController::class, 'reorder'])->name('hero.reorder');

    Route::resource('layanan', LayananController::class);
    Route::post('layanan/{layanan}/toggle', [LayananController::class, 'toggle'])->name('layanan.toggle');

    Route::resource('dokter', DokterController::class);
    Route::post('dokter/{dokter}/toggle', [DokterController::class, 'toggle'])->name('dokter.toggle');

    Route::resource('berita', AdminBeritaController::class);
    Route::post('berita/{berita}/toggle', [AdminBeritaController::class, 'toggle'])->name('berita.toggle');

    Route::resource('kamar', KamarController::class);
    Route::post('kamar/{kamar}/toggle', [KamarController::class, 'toggle'])->name('kamar.toggle');

    Route::get('setting', [SiteSettingController::class, 'index'])->name('setting.index');
    Route::post('setting', [SiteSettingController::class, 'update'])->name('setting.update');

    Route::get('profil',  [AdminProfilController::class, 'index'])->name('profil.index');
    Route::post('profil', [AdminProfilController::class, 'update'])->name('profil.update');

    // Poliklinik
    Route::prefix('poliklinik')->name('poliklinik.')->group(function () {
        Route::get('/',                  [AdminPoliklinikController::class, 'index'])        ->name('index');
        Route::get('/create',            [AdminPoliklinikController::class, 'create'])       ->name('create');
        Route::post('/',                 [AdminPoliklinikController::class, 'store'])         ->name('store');
        Route::get('/{id}/edit',         [AdminPoliklinikController::class, 'edit'])          ->name('edit');
        Route::put('/{id}',              [AdminPoliklinikController::class, 'update'])        ->name('update');
        Route::delete('/{id}',           [AdminPoliklinikController::class, 'destroy'])       ->name('destroy');
        Route::post('/{id}/toggle',      [AdminPoliklinikController::class, 'toggle'])        ->name('toggle');
        Route::get('/{id}/dokter',       [AdminPoliklinikController::class, 'manageDokter'])  ->name('dokter');
        Route::post('/{id}/dokter/sync', [AdminPoliklinikController::class, 'syncDokter'])    ->name('dokter.sync');
    });

    // Pelayanan 24 Jam
    Route::prefix('pelayanan24jam')->name('pelayanan24jam.')->group(function () {
        Route::get('/',            [AdminPelayanan24JamController::class, 'index'])  ->name('index');
        Route::get('/create',      [AdminPelayanan24JamController::class, 'create']) ->name('create');
        Route::post('/',           [AdminPelayanan24JamController::class, 'store'])  ->name('store');
        Route::get('/{id}/edit',   [AdminPelayanan24JamController::class, 'edit'])   ->name('edit');
        Route::put('/{id}',        [AdminPelayanan24JamController::class, 'update']) ->name('update');
        Route::delete('/{id}',     [AdminPelayanan24JamController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle',[AdminPelayanan24JamController::class, 'toggle']) ->name('toggle');
    });

    // Download
    Route::prefix('download')->name('download.')->group(function () {
        Route::get('/',                      [AdminDownloadController::class, 'index'])          ->name('index');
        Route::get('/kategori/create',       [AdminDownloadController::class, 'createKategori']) ->name('kategori.create');
        Route::post('/kategori',             [AdminDownloadController::class, 'storeKategori'])  ->name('kategori.store');
        Route::get('/kategori/{id}/edit',    [AdminDownloadController::class, 'editKategori'])   ->name('kategori.edit');
        Route::put('/kategori/{id}',         [AdminDownloadController::class, 'updateKategori']) ->name('kategori.update');
        Route::delete('/kategori/{id}',      [AdminDownloadController::class, 'destroyKategori'])->name('kategori.destroy');
        Route::post('/kategori/{id}/toggle', [AdminDownloadController::class, 'toggleKategori']) ->name('kategori.toggle');
        Route::get('/file/create',           [AdminDownloadController::class, 'createFile'])     ->name('file.create');
        Route::post('/file',                 [AdminDownloadController::class, 'storeFile'])      ->name('file.store');
        Route::get('/file/{id}/edit',        [AdminDownloadController::class, 'editFile'])       ->name('file.edit');
        Route::put('/file/{id}',             [AdminDownloadController::class, 'updateFile'])     ->name('file.update');
        Route::delete('/file/{id}',          [AdminDownloadController::class, 'destroyFile'])    ->name('file.destroy');
        Route::post('/file/{id}/toggle',     [AdminDownloadController::class, 'toggleFile'])     ->name('file.toggle');
    });

    // Pendaftaran Online — Admin
    Route::prefix('pendaftaran')->name('pendaftaran.')->group(function () {
        Route::get('/',            [AdminPendaftaranController::class, 'index'])       ->name('index');
        Route::get('/{id}',        [AdminPendaftaranController::class, 'show'])        ->name('show');
        Route::post('/{id}/status',[AdminPendaftaranController::class, 'updateStatus'])->name('status');
        Route::delete('/{id}',     [AdminPendaftaranController::class, 'destroy'])     ->name('destroy');
    });

    // Alur Pelayanan
    Route::prefix('alur-pelayanan')->name('alur-pelayanan.')->group(function () {
        Route::get('/',            [AdminAlurPelayananController::class, 'index'])  ->name('index');
        Route::get('/create',      [AdminAlurPelayananController::class, 'create']) ->name('create');
        Route::post('/',           [AdminAlurPelayananController::class, 'store'])  ->name('store');
        Route::get('/{id}/edit',   [AdminAlurPelayananController::class, 'edit'])   ->name('edit');
        Route::put('/{id}',        [AdminAlurPelayananController::class, 'update']) ->name('update');
        Route::delete('/{id}',     [AdminAlurPelayananController::class, 'destroy'])->name('destroy');
        Route::post('/{id}/toggle',[AdminAlurPelayananController::class, 'toggle']) ->name('toggle');
    });

    // Leaflet & Poster
    Route::prefix('leaflet')->name('leaflet.')->group(function () {
        Route::get('/',                       [AdminLeafletController::class, 'index'])          ->name('index');
        Route::get('/kategori/create',        [AdminLeafletController::class, 'createKategori'])->name('kategori.create');
        Route::post('/kategori',              [AdminLeafletController::class, 'storeKategori']) ->name('kategori.store');
        Route::get('/kategori/{id}/edit',     [AdminLeafletController::class, 'editKategori'])  ->name('kategori.edit');
        Route::put('/kategori/{id}',          [AdminLeafletController::class, 'updateKategori'])->name('kategori.update');
        Route::delete('/kategori/{id}',       [AdminLeafletController::class, 'destroyKategori'])->name('kategori.destroy');
        Route::post('/kategori/{id}/toggle',  [AdminLeafletController::class, 'toggleKategori'])->name('kategori.toggle');
        Route::get('/item/create',            [AdminLeafletController::class, 'createItem'])    ->name('item.create');
        Route::post('/item',                  [AdminLeafletController::class, 'storeItem'])     ->name('item.store');
        Route::get('/item/{id}/edit',         [AdminLeafletController::class, 'editItem'])      ->name('item.edit');
        Route::put('/item/{id}',              [AdminLeafletController::class, 'updateItem'])    ->name('item.update');
        Route::delete('/item/{id}',           [AdminLeafletController::class, 'destroyItem'])   ->name('item.destroy');
        Route::post('/item/{id}/toggle',      [AdminLeafletController::class, 'toggleItem'])    ->name('item.toggle');
    });
});
