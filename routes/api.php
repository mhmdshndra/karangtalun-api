<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\Api\ProfileController;
use App\Http\Controllers\Api\WargaController;
use App\Http\Controllers\Api\StafController;
use App\Http\Controllers\Api\AdminController;
use App\Http\Controllers\Api\NotifikasiController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\CmsPublicController;
use App\Http\Controllers\Api\CmsAdminController;

/*
|--------------------------------------------------------------------------
| Endpoint Publik (tanpa auth)
|--------------------------------------------------------------------------
*/

// Auth — dengan Turnstile + rate limit
Route::prefix('auth')->group(function () {
    Route::post('/login', [AuthController::class, 'login'])
        ->middleware(['turnstile', 'throttle:login']);
    Route::post('/register', [AuthController::class, 'register'])
        ->middleware(['turnstile', 'throttle:register']);
    Route::post('/lupa-sandi/identify', [AuthController::class, 'lupaSandiIdentify'])
        ->middleware(['turnstile', 'throttle:login']);
    Route::post('/lupa-sandi/verify-hp', [AuthController::class, 'lupaSandiVerifyHp'])
        ->middleware('throttle:login');
    Route::post('/lupa-sandi/verify-otp', [AuthController::class, 'lupaSandiVerifyOtp'])
        ->middleware('throttle:login');
    Route::post('/lupa-sandi/reset', [AuthController::class, 'lupaSandiReset'])
        ->middleware('throttle:register');
});

// Laporan publik — Turnstile + rate limit
Route::post('/laporan', [PublicController::class, 'storeLaporan'])
    ->middleware(['turnstile', 'throttle:laporan']);

// Permohonan PPID publik — Turnstile + rate limit
Route::post('/ppid/permohonan', [PublicController::class, 'storePermohonan'])
    ->middleware(['turnstile', 'throttle:permohonan']);

// Like UMKM — rate limit only
Route::post('/cms/umkm/{id}/like', [PublicController::class, 'likeUmkm'])
    ->middleware('throttle:like_umkm');

// Views berita — rate limit only
Route::post('/cms/berita/{slug}/view', [PublicController::class, 'viewBerita'])
    ->middleware('throttle:view_berita');

// CMS Public read endpoints
Route::prefix('cms')->group(function () {
    Route::get('/identitas-desa', [CmsPublicController::class, 'identitasDesa']);
    Route::get('/profil-desa', [CmsPublicController::class, 'profilDesa']);
    Route::get('/berita', [CmsPublicController::class, 'beritaIndex']);
    Route::get('/berita/{slug}', [CmsPublicController::class, 'beritaShow']);
    Route::get('/galeri', [CmsPublicController::class, 'galeri']);
    Route::get('/umkm', [CmsPublicController::class, 'umkmIndex']);
    Route::get('/umkm/{slug}', [CmsPublicController::class, 'umkmShow']);
    Route::get('/aparatur', [CmsPublicController::class, 'aparatur']);
    Route::get('/potensi-desa', [CmsPublicController::class, 'potensiDesa']);
    Route::get('/fasilitas', [CmsPublicController::class, 'fasilitas']);
    Route::get('/layanan-publik', [CmsPublicController::class, 'layananPublik']);
    Route::get('/ppid-dokumen', [CmsPublicController::class, 'ppidDokumen']);
    Route::get('/peta-desa', [CmsPublicController::class, 'petaDesa']);
    Route::get('/infografis', [CmsPublicController::class, 'infografis']);
    Route::get('/header-footer', [CmsPublicController::class, 'headerFooter']);
});

// Statistik kependudukan — computed
Route::get('/statistik/kependudukan', [CmsPublicController::class, 'statistikKependudukan']);

/*
|--------------------------------------------------------------------------
| Endpoint Auth — Semua Role (auth:sanctum)
|--------------------------------------------------------------------------
*/
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::get('/auth/me', [AuthController::class, 'me']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);

    // Profil
    Route::get('/profile', [ProfileController::class, 'show']);
    Route::put('/profile', [ProfileController::class, 'update']);
    Route::post('/profile/foto', [ProfileController::class, 'uploadFoto']);
    Route::put('/password', [ProfileController::class, 'updatePassword']);

    // Notifikasi
    Route::get('/notifikasi', [NotifikasiController::class, 'index']);
    Route::put('/notifikasi/{id}/read', [NotifikasiController::class, 'markRead']);
    Route::put('/notifikasi/read-all', [NotifikasiController::class, 'markAllRead']);

    /*
    |----------------------------------------------------------------------
    | Endpoint Warga (role=warga)
    |----------------------------------------------------------------------
    */
    Route::prefix('warga')->middleware('role:warga,admin_desa')->group(function () {
        Route::get('/kk', [WargaController::class, 'kk']);
        Route::get('/kk/anggota-eligible', [WargaController::class, 'anggotaEligible']);

        Route::post('/surat', [WargaController::class, 'storeSurat']);
        Route::get('/surat', [WargaController::class, 'indexSurat']);
        Route::get('/surat/{id}', [WargaController::class, 'showSurat']);
        Route::post('/surat/{id}/berkas', [WargaController::class, 'uploadBerkas']);

        Route::post('/laporan', [WargaController::class, 'storeLaporan']);
        Route::get('/laporan', [WargaController::class, 'indexLaporan']);

        Route::post('/permohonan', [WargaController::class, 'storePermohonan']);
        Route::get('/permohonan', [WargaController::class, 'indexPermohonan']);
    });

    /*
    |----------------------------------------------------------------------
    | Endpoint Staf (role=staf_layanan)
    |----------------------------------------------------------------------
    */
    Route::prefix('staf')->middleware('role:staf_layanan,admin_desa')->group(function () {
        Route::get('/surat', [StafController::class, 'indexSurat']);
        Route::put('/surat/{id}/status', [StafController::class, 'updateStatusSurat']);

        Route::get('/laporan', [StafController::class, 'indexLaporan']);
        Route::put('/laporan/{id}/status', [StafController::class, 'updateStatusLaporan']);

        Route::get('/permohonan', [StafController::class, 'indexPermohonan']);
        Route::put('/permohonan/{id}/status', [StafController::class, 'updateStatusPermohonan']);
        Route::post('/permohonan/{id}/status', [StafController::class, 'updateStatusPermohonan']); // multipart with file_balasan
    });

    /*
    |----------------------------------------------------------------------
    | Endpoint Admin (role=admin_desa)
    |----------------------------------------------------------------------
    */
    Route::prefix('admin')->middleware('role:admin_desa')->group(function () {
        // KK CRUD
        Route::get('/kk', [AdminController::class, 'indexKK']);
        Route::post('/kk', [AdminController::class, 'storeKK']);
        Route::put('/kk/{no_kk}', [AdminController::class, 'updateKK']);
        Route::delete('/kk/{no_kk}', [AdminController::class, 'destroyKK']);
        Route::post('/kk/{no_kk}/anggota', [AdminController::class, 'storeAnggota']);
        Route::put('/kk/{no_kk}/anggota/{nik}', [AdminController::class, 'updateAnggota']);
        Route::delete('/kk/{no_kk}/anggota/{nik}', [AdminController::class, 'destroyAnggota']);

        // Users
        Route::get('/users', [AdminController::class, 'indexUsers']);
        Route::post('/users/manual', [AdminController::class, 'createManual']);
        Route::post('/users/sync', [AdminController::class, 'syncWarga']);
        Route::put('/users/{id}/staff-account', [AdminController::class, 'updateStaffAccount']);
        Route::put('/users/{id}/status', [AdminController::class, 'updateUserStatus']);

        // CMS Admin
        Route::prefix('cms')->group(function () {
            Route::put('/identitas-desa', [CmsAdminController::class, 'updateIdentitasDesa']);
            Route::put('/profil-desa', [CmsAdminController::class, 'updateProfilDesa']);
            Route::put('/infografis', [CmsAdminController::class, 'updateInfografis']);
            Route::put('/header-footer', [CmsAdminController::class, 'updateHeaderFooter']);

            Route::post('/berita', [CmsAdminController::class, 'storeBerita']);
            Route::put('/berita/{id}', [CmsAdminController::class, 'updateBerita']);
            Route::delete('/berita/{id}', [CmsAdminController::class, 'destroyBerita']);

            Route::post('/galeri', [CmsAdminController::class, 'storeGaleri']);
            Route::put('/galeri/{id}', [CmsAdminController::class, 'updateGaleri']);
            Route::delete('/galeri/{id}', [CmsAdminController::class, 'destroyGaleri']);

            Route::post('/umkm', [CmsAdminController::class, 'storeUmkm']);
            Route::put('/umkm/{id}', [CmsAdminController::class, 'updateUmkm']);
            Route::delete('/umkm/{id}', [CmsAdminController::class, 'destroyUmkm']);

            Route::post('/aparatur', [CmsAdminController::class, 'storeAparatur']);
            Route::put('/aparatur/{id}', [CmsAdminController::class, 'updateAparatur']);
            Route::delete('/aparatur/{id}', [CmsAdminController::class, 'destroyAparatur']);

            Route::post('/potensi-desa', [CmsAdminController::class, 'storePotensiDesa']);
            Route::put('/potensi-desa/{id}', [CmsAdminController::class, 'updatePotensiDesa']);
            Route::delete('/potensi-desa/{id}', [CmsAdminController::class, 'destroyPotensiDesa']);

            Route::post('/fasilitas', [CmsAdminController::class, 'storeFasilitas']);
            Route::put('/fasilitas/{id}', [CmsAdminController::class, 'updateFasilitas']);
            Route::delete('/fasilitas/{id}', [CmsAdminController::class, 'destroyFasilitas']);

            Route::post('/layanan-publik', [CmsAdminController::class, 'storeLayananPublik']);
            Route::put('/layanan-publik/{id}', [CmsAdminController::class, 'updateLayananPublik']);
            Route::delete('/layanan-publik/{id}', [CmsAdminController::class, 'destroyLayananPublik']);

            Route::post('/ppid-dokumen', [CmsAdminController::class, 'storePpidDokumen']);
            Route::put('/ppid-dokumen/{id}', [CmsAdminController::class, 'updatePpidDokumen']);
            Route::delete('/ppid-dokumen/{id}', [CmsAdminController::class, 'destroyPpidDokumen']);

            Route::post('/peta-desa', [CmsAdminController::class, 'storePetaDesa']);
            Route::put('/peta-desa/{id}', [CmsAdminController::class, 'updatePetaDesa']);
            Route::delete('/peta-desa/{id}', [CmsAdminController::class, 'destroyPetaDesa']);
        });
    });
});
