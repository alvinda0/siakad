<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\InformasiController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\MuridController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\KandidatController;
use App\Http\Controllers\Admin\UserController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
})->name('beranda');

/*
|--------------------------------------------------------------------------
| Pendaftaran Siswa Baru (Publik — multi-step)
|--------------------------------------------------------------------------
*/
Route::prefix('daftar')->name('kandidat.')->group(function () {
    Route::get('/',        [KandidatController::class, 'create'])->name('create');
    Route::post('/',       [KandidatController::class, 'store'])->name('store');
    Route::get('/selesai', [KandidatController::class, 'selesai'])->name('selesai');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {

    // Login (guest only)
    Route::middleware('guest')->group(function () {
        Route::get('login',  [AdminAuthController::class, 'showLogin'])->name('login');
        Route::post('login', [AdminAuthController::class, 'login'])->name('login.post');
    });

    // Logout
    Route::post('logout', [AdminAuthController::class, 'logout'])->name('logout');

    // Protected admin area
    Route::middleware('auth')->group(function () {

        // Dashboard
        Route::get('dashboard', function () {
            return view('admin.dashboard');
        })->name('dashboard');

        // Guru
        Route::prefix('guru')->name('guru.')->group(function () {
            Route::get('/',                           [GuruController::class, 'index'])->name('index');
            Route::get('create',                      [GuruController::class, 'create'])->name('create');
            Route::patch('/{user}/assign-wali-kelas', [GuruController::class, 'assignWaliKelas'])->name('assign-wali-kelas');
        });

        // Murid
        Route::prefix('murid')->name('murid.')->group(function () {
            Route::get('/',                       [MuridController::class, 'index'])->name('index');
            Route::get('create',                  [MuridController::class, 'create'])->name('create');
            Route::post('create',                 [MuridController::class, 'store'])->name('store');
            Route::get('/{user}',                 [MuridController::class, 'show'])->name('show');
            Route::patch('/{user}/assign-kelas',  [MuridController::class, 'assignKelas'])->name('assign-kelas');
        });

        // Kandidat PPDB
        Route::prefix('kandidat')->name('kandidat.')->group(function () {
            Route::get('/',                      [KandidatController::class, 'index'])->name('index');
            Route::get('/{user}',                [KandidatController::class, 'show'])->name('show');
            Route::patch('/{user}/accept',       [KandidatController::class, 'accept'])->name('accept');
            Route::patch('/{user}/reject',       [KandidatController::class, 'reject'])->name('reject');
        });

        // Kelas
        Route::prefix('kelas')->name('kelas.')->group(function () {
            Route::get('/',           [KelasController::class, 'index'])->name('index');
            Route::get('/create',     [KelasController::class, 'create'])->name('create');
            Route::post('/',          [KelasController::class, 'store'])->name('store');
            Route::get('/{kelas}',    [KelasController::class, 'show'])->name('show');
            Route::get('/{kelas}/edit', [KelasController::class, 'edit'])->name('edit');
            Route::put('/{kelas}',    [KelasController::class, 'update'])->name('update');
            Route::delete('/{kelas}', [KelasController::class, 'destroy'])->name('destroy');
        });

        // Mata Pelajaran
        Route::prefix('mapel')->name('mapel.')->group(function () {
            Route::get('/',                            [MataPelajaranController::class, 'index'])->name('index');
            Route::post('/',                           [MataPelajaranController::class, 'store'])->name('store');
            Route::put('/{mapel}',                     [MataPelajaranController::class, 'update'])->name('update');
            Route::delete('/{mapel}',                  [MataPelajaranController::class, 'destroy'])->name('destroy');
            Route::patch('/{mapel}/toggle-aktif',      [MataPelajaranController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Jadwal
        Route::prefix('jadwal')->name('jadwal.')->group(function () {
            Route::get('/',                [JadwalController::class, 'index'])->name('index');
            Route::post('/',               [JadwalController::class, 'store'])->name('store');
            Route::put('/{jadwal}',        [JadwalController::class, 'update'])->name('update');
            Route::delete('/{jadwal}',     [JadwalController::class, 'destroy'])->name('destroy');
        });

        // Absensi
        Route::prefix('absensi')->name('absensi.')->group(function () {
            Route::get('/',        [AbsensiController::class, 'index'])->name('index');
            Route::post('/simpan', [AbsensiController::class, 'simpan'])->name('simpan');
            Route::get('/rekap',   [AbsensiController::class, 'rekap'])->name('rekap');
        });

        // Nilai
        Route::prefix('nilai')->name('nilai.')->group(function () {
            Route::get('/',        [NilaiController::class, 'index'])->name('index');
            Route::post('/simpan', [NilaiController::class, 'simpan'])->name('simpan');
            Route::get('/rekap',   [NilaiController::class, 'rekap'])->name('rekap');
        });

        // Informasi (Beasiswa & Promo Program Strategis)
        Route::prefix('informasi')->name('informasi.')->group(function () {
            Route::get('/',                                [InformasiController::class, 'index'])->name('index');
            Route::post('/',                               [InformasiController::class, 'store'])->name('store');
            Route::put('/{informasi}',                     [InformasiController::class, 'update'])->name('update');
            Route::delete('/{informasi}',                  [InformasiController::class, 'destroy'])->name('destroy');
            Route::patch('/{informasi}/toggle-aktif',      [InformasiController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Pengguna
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',              [UserController::class, 'index'])->name('index');
            Route::get('/{user}/edit',   [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}',        [UserController::class, 'update'])->name('update');
            Route::delete('/{user}',     [UserController::class, 'destroy'])->name('destroy');
        });

        // Pengaturan (dialihkan ke log aktivitas)
        Route::get('settings', [ActivityLogController::class, 'index'])->name('settings');

        // Log Aktivitas
        Route::prefix('activity-logs')->name('activity-logs.')->group(function () {
            Route::get('/',        [ActivityLogController::class, 'index'])->name('index');
            Route::get('/{activityLog}', [ActivityLogController::class, 'show'])->name('show');
        });
    });
});
