<?php

use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\Admin\ActivityLogController;
use App\Http\Controllers\Admin\AbsensiController;
use App\Http\Controllers\Admin\EkstrakurikulerController;
use App\Http\Controllers\Admin\FasilitasController;
use App\Http\Controllers\Admin\GuruController;
use App\Http\Controllers\Admin\InformasiController;
use App\Http\Controllers\Admin\JadwalController;
use App\Http\Controllers\Admin\JadwalUjianController;
use App\Http\Controllers\Admin\KegiatanSekolahController;
use App\Http\Controllers\Admin\KelasController;
use App\Http\Controllers\Admin\MataPelajaranController;
use App\Http\Controllers\Admin\MuridController;
use App\Http\Controllers\Admin\NilaiController;
use App\Http\Controllers\Admin\KandidatController;
use App\Http\Controllers\Admin\PrestasiController;
use App\Http\Controllers\Admin\SoalUjianController;
use App\Http\Controllers\Admin\TickerController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\JawabanUjianController;
use App\Http\Controllers\Guru\JadwalUjianGuruController;
use App\Http\Controllers\Guru\JawabanUjianGuruController;
use App\Http\Controllers\Murid\UjianController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PublicController;
use Illuminate\Support\Facades\Route;

Route::get('/', [HomeController::class, 'index'])->name('beranda');

/*
|--------------------------------------------------------------------------
| Halaman Publik — Profil Sekolah
|--------------------------------------------------------------------------
*/
Route::get('/kegiatan',        [PublicController::class, 'kegiatan'])->name('kegiatan');
Route::get('/prestasi',        [PublicController::class, 'prestasi'])->name('prestasi');
Route::get('/ekstrakurikuler', [PublicController::class, 'ekstrakurikuler'])->name('ekstrakurikuler');
Route::get('/fasilitas',       [PublicController::class, 'fasilitas'])->name('fasilitas');
Route::get('/pengumuman',      [PublicController::class, 'pengumuman'])->name('pengumuman');
Route::get('/informasi',       [PublicController::class, 'informasi'])->name('informasi');
Route::get('/kontak',          [PublicController::class, 'kontak'])->name('kontak');

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

        // Jadwal Ujian
        Route::prefix('jadwal-ujian')->name('jadwal-ujian.')->group(function () {
            Route::get('/',                                [JadwalUjianController::class, 'index'])->name('index');
            Route::post('/',                               [JadwalUjianController::class, 'store'])->name('store');
            Route::put('/{jadwalUjian}',                   [JadwalUjianController::class, 'update'])->name('update');
            Route::delete('/{jadwalUjian}',                [JadwalUjianController::class, 'destroy'])->name('destroy');
            Route::patch('/{jadwalUjian}/toggle-aktif',    [JadwalUjianController::class, 'toggleAktif'])->name('toggle-aktif');
            Route::post('/{jadwalUjian}/upload-soal',      [JadwalUjianController::class, 'uploadSoal'])->name('upload-soal');
            Route::delete('/{jadwalUjian}/hapus-soal',     [JadwalUjianController::class, 'hapusSoal'])->name('hapus-soal');
            Route::post('/{jadwalUjian}/upload-kunci',     [JadwalUjianController::class, 'uploadKunci'])->name('upload-kunci');
        });

        // Soal Ujian (manajemen soal per jadwal ujian)
        Route::prefix('jadwal-ujian/{jadwalUjian}/soal')->name('soal-ujian.')->group(function () {
            Route::get('/',              [SoalUjianController::class, 'index'])->name('index');
            Route::post('/',             [SoalUjianController::class, 'store'])->name('store');
            Route::put('/{soal}',        [SoalUjianController::class, 'update'])->name('update');
            Route::delete('/{soal}',     [SoalUjianController::class, 'destroy'])->name('destroy');
            Route::get('/rekap',         [SoalUjianController::class, 'rekap'])->name('rekap');
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

        // Ticker (Info berjalan di home)
        Route::prefix('ticker')->name('ticker.')->group(function () {
            Route::get('/',                          [TickerController::class, 'index'])->name('index');
            Route::post('/',                         [TickerController::class, 'store'])->name('store');
            Route::put('/{ticker}',                  [TickerController::class, 'update'])->name('update');
            Route::delete('/{ticker}',               [TickerController::class, 'destroy'])->name('destroy');
            Route::patch('/{ticker}/toggle-aktif',   [TickerController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Prestasi
        Route::prefix('prestasi')->name('prestasi.')->group(function () {
            Route::get('/',                              [PrestasiController::class, 'index'])->name('index');
            Route::post('/',                             [PrestasiController::class, 'store'])->name('store');
            Route::put('/{prestasi}',                    [PrestasiController::class, 'update'])->name('update');
            Route::delete('/{prestasi}',                 [PrestasiController::class, 'destroy'])->name('destroy');
            Route::patch('/{prestasi}/toggle-aktif',     [PrestasiController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Kegiatan Sekolah
        Route::prefix('kegiatan-sekolah')->name('kegiatan-sekolah.')->group(function () {
            Route::get('/',                                        [KegiatanSekolahController::class, 'index'])->name('index');
            Route::post('/',                                       [KegiatanSekolahController::class, 'store'])->name('store');
            Route::put('/{kegiatanSekolah}',                       [KegiatanSekolahController::class, 'update'])->name('update');
            Route::delete('/{kegiatanSekolah}',                    [KegiatanSekolahController::class, 'destroy'])->name('destroy');
            Route::patch('/{kegiatanSekolah}/toggle-aktif',        [KegiatanSekolahController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Ekstrakurikuler
        Route::prefix('ekstrakurikuler')->name('ekstrakurikuler.')->group(function () {
            Route::get('/',                                            [EkstrakurikulerController::class, 'index'])->name('index');
            Route::post('/',                                           [EkstrakurikulerController::class, 'store'])->name('store');
            Route::put('/{ekstrakurikuler}',                           [EkstrakurikulerController::class, 'update'])->name('update');
            Route::delete('/{ekstrakurikuler}',                        [EkstrakurikulerController::class, 'destroy'])->name('destroy');
            Route::patch('/{ekstrakurikuler}/toggle-aktif',            [EkstrakurikulerController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Fasilitas
        Route::prefix('fasilitas')->name('fasilitas.')->group(function () {
            Route::get('/',                              [FasilitasController::class, 'index'])->name('index');
            Route::post('/',                             [FasilitasController::class, 'store'])->name('store');
            Route::put('/{fasilitas}',                   [FasilitasController::class, 'update'])->name('update');
            Route::delete('/{fasilitas}',                [FasilitasController::class, 'destroy'])->name('destroy');
            Route::patch('/{fasilitas}/toggle-aktif',    [FasilitasController::class, 'toggleAktif'])->name('toggle-aktif');
        });

        // Pengguna
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',              [UserController::class, 'index'])->name('index');
            Route::get('/{user}/edit',   [UserController::class, 'edit'])->name('edit');
            Route::put('/{user}',        [UserController::class, 'update'])->name('update');
            Route::delete('/{user}',     [UserController::class, 'destroy'])->name('destroy');
        });

        // Jawaban Ujian
        Route::prefix('jawaban-ujian')->name('jawaban-ujian.')->group(function () {
            Route::get('/',                                        [JawabanUjianController::class, 'index'])->name('index');
            Route::get('/{jadwalUjian}',                           [JawabanUjianController::class, 'show'])->name('show');
            Route::post('/{jadwalUjian}/update-nilai',             [JawabanUjianController::class, 'updateNilai'])->name('update-nilai');
            Route::post('/{jadwalUjian}/soal/{soal}/update-kunci', [JawabanUjianController::class, 'updateKunci'])->name('update-kunci');
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

/*
|--------------------------------------------------------------------------
| Guru Routes — Area khusus guru (akses dengan role teacher)
|--------------------------------------------------------------------------
*/
Route::prefix('guru')->name('guru.')->middleware(['auth', \App\Http\Middleware\EnsureTeacher::class])->group(function () {

    // Dashboard jadwal ujian milik guru
    Route::prefix('jadwal-ujian')->name('jadwal-ujian.')->group(function () {
        Route::get('/',                                    [JadwalUjianGuruController::class, 'index'])->name('index');
        Route::post('/{jadwalUjian}/upload-soal',          [JadwalUjianGuruController::class, 'uploadSoal'])->name('upload-soal');
        Route::delete('/{jadwalUjian}/hapus-soal',         [JadwalUjianGuruController::class, 'hapusSoal'])->name('hapus-soal');
    });

    // Jawaban ujian siswa (hanya mapel yang diampu)
    Route::prefix('jawaban-ujian')->name('jawaban-ujian.')->group(function () {
        Route::get('/',                                          [JawabanUjianGuruController::class, 'index'])->name('index');
        Route::get('/{jadwalUjian}',                             [JawabanUjianGuruController::class, 'show'])->name('show');
        Route::post('/{jadwalUjian}/update-nilai',               [JawabanUjianGuruController::class, 'updateNilai'])->name('update-nilai');
        Route::post('/{jadwalUjian}/soal/{soal}/update-kunci',   [JawabanUjianGuruController::class, 'updateKunci'])->name('update-kunci');
    });
});

/*
|--------------------------------------------------------------------------
| Murid Routes — Area khusus murid (akses dengan role student)
|--------------------------------------------------------------------------
*/
Route::prefix('murid')->name('murid.')->middleware(['auth', \App\Http\Middleware\EnsureStudent::class])->group(function () {

    // Dashboard murid
    Route::get('dashboard', function () {
        return redirect()->route('murid.ujian.index');
    })->name('dashboard');

    // Ujian
    Route::prefix('ujian')->name('ujian.')->group(function () {
        Route::get('/',                                      [UjianController::class, 'index'])->name('index');
        Route::get('/{jadwalUjian}',                         [UjianController::class, 'show'])->name('show');
        Route::get('/{jadwalUjian}/kerjakan',                [UjianController::class, 'kerjakan'])->name('kerjakan');
        Route::post('/{jadwalUjian}/jawaban',                [UjianController::class, 'simpanJawaban'])->name('jawaban');
        Route::post('/{jadwalUjian}/submit',                 [UjianController::class, 'submit'])->name('submit');
        Route::get('/{jadwalUjian}/hasil',                   [UjianController::class, 'hasil'])->name('hasil');
    });
});
