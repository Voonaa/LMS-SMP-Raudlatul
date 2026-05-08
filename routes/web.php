<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LeaderboardController;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    // Rute Siswa
    Route::middleware('can:siswa')->group(function () {
        Route::get('/siswa/dashboard', [SiswaDashboardController::class, 'index'])->name('siswa.dashboard');
        
        // Materi
        Route::get('/siswa/materi', [\App\Http\Controllers\SiswaMateriController::class, 'index'])->name('siswa.materi.index');
        Route::get('/siswa/materi/{id}', [\App\Http\Controllers\SiswaMateriController::class, 'show'])->name('siswa.materi.show');
        Route::post('/siswa/materi/{id}/log', [\App\Http\Controllers\SiswaMateriController::class, 'log'])->name('siswa.materi.log');
        
        // Kuis
        Route::get('/siswa/kuis', [\App\Http\Controllers\SiswaKuisController::class, 'index'])->name('siswa.kuis.index');
        Route::get('/siswa/kuis/{id}', [\App\Http\Controllers\SiswaKuisController::class, 'show'])->name('siswa.kuis.show');
        Route::post('/siswa/kuis/{id}/submit', [\App\Http\Controllers\SiswaKuisController::class, 'submit'])->name('siswa.kuis.submit');
    });

    // Rute Guru
    Route::middleware('can:guru')->group(function () {
        Route::get('/guru/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
        
        // Kelola Materi
        Route::get('/guru/materi', [\App\Http\Controllers\GuruMateriController::class, 'index'])->name('guru.materi.index');
        Route::get('/guru/materi/create', [\App\Http\Controllers\GuruMateriController::class, 'create'])->name('guru.materi.create');
        Route::post('/guru/materi', [\App\Http\Controllers\GuruMateriController::class, 'store'])->name('guru.materi.store');
        Route::delete('/guru/materi/{id}', [\App\Http\Controllers\GuruMateriController::class, 'destroy'])->name('guru.materi.destroy');

        // Kelola Kuis
        Route::get('/guru/kuis', [\App\Http\Controllers\GuruKuisController::class, 'index'])->name('guru.kuis.index');
        Route::post('/guru/kuis/generate', [\App\Http\Controllers\GuruKuisController::class, 'generate'])->name('guru.kuis.generate');
        Route::delete('/guru/kuis/{id}', [\App\Http\Controllers\GuruKuisController::class, 'destroy'])->name('guru.kuis.destroy');

        // Laporan
        Route::get('/guru/laporan', [\App\Http\Controllers\GuruLaporanController::class, 'index'])->name('guru.laporan.index');
        
        // Tetap simpan rute AI generate materi dari dashboard
        Route::post('/guru/materi/generate', [GuruDashboardController::class, 'generateMateri'])->name('guru.materi.generate-dashboard');
    });

    // Forum (Bisa diakses guru & siswa)
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::post('/forum/{id}/like', [ForumController::class, 'like'])->name('forum.like');
    Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');

    // Leaderboard (API)
    Route::get('/api/leaderboard/{kelasId}', [LeaderboardController::class, 'getLeaderboard']);

    // Route Admin Testing & Management
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/testing/mae', [\App\Http\Controllers\TestingController::class, 'mae'])->name('admin.testing.mae');
        
        // Kelola Pengguna
        Route::get('/admin/user', [\App\Http\Controllers\AdminUserController::class, 'index'])->name('admin.user.index');
        Route::get('/admin/user/create', [\App\Http\Controllers\AdminUserController::class, 'create'])->name('admin.user.create');
        Route::post('/admin/user', [\App\Http\Controllers\AdminUserController::class, 'store'])->name('admin.user.store');
        Route::delete('/admin/user/{id}', [\App\Http\Controllers\AdminUserController::class, 'destroy'])->name('admin.user.destroy');

        // Konfigurasi
        Route::get('/admin/config', [\App\Http\Controllers\AdminConfigController::class, 'index'])->name('admin.config.index');
        Route::post('/admin/config', [\App\Http\Controllers\AdminConfigController::class, 'save'])->name('admin.config.save');
    });
});
