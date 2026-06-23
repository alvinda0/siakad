<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\InfoController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes — SIAKAD SMK Muhammadiyah Sempor
|--------------------------------------------------------------------------
|
| Semua route di sini diawali prefix /api secara otomatis oleh Laravel.
|
| Endpoint publik  : tidak butuh token
| Endpoint auth    : butuh header  Authorization: Bearer <token>
|
*/

// ── Publik: Auth ─────────────────────────────────────────────────────────
Route::prefix('auth')->group(function () {
    Route::post('login',    [AuthController::class, 'login']);
    Route::post('register', [AuthController::class, 'register']);
});

// ── Publik: Info sekolah ─────────────────────────────────────────────────
Route::prefix('info')->group(function () {
    Route::get('sekolah',  [InfoController::class, 'sekolah']);
    Route::get('kegiatan', [InfoController::class, 'kegiatan']);
    Route::get('prestasi', [InfoController::class, 'prestasi']);
    Route::get('ekskul',   [InfoController::class, 'ekskul']);
    Route::get('berita',   [InfoController::class, 'berita']);
});

// ── Protected: butuh token Sanctum ───────────────────────────────────────
Route::middleware('auth:sanctum')->group(function () {

    // Auth
    Route::prefix('auth')->group(function () {
        Route::post('logout',          [AuthController::class, 'logout']);
        Route::get('profile',          [AuthController::class, 'profile']);
        Route::put('profile',          [AuthController::class, 'updateProfile']);
        Route::put('change-password',  [AuthController::class, 'changePassword']);
    });

});
