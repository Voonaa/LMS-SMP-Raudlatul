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
    });

    // Rute Guru
    Route::middleware('can:guru')->group(function () {
        Route::get('/guru/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
        Route::post('/guru/materi/generate', [GuruDashboardController::class, 'generateMateri'])->name('guru.materi.generate');
        Route::post('/guru/kuis/generate', [GuruDashboardController::class, 'generateKuis'])->name('guru.kuis.generate');
    });

    // Forum (Bisa diakses guru & siswa)
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::post('/forum/{id}/like', [ForumController::class, 'like'])->name('forum.like');
    Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');

    // Leaderboard (API)
    Route::get('/api/leaderboard/{kelasId}', [LeaderboardController::class, 'getLeaderboard']);

    // Route Admin Testing
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/testing/mae', [\App\Http\Controllers\TestingController::class, 'mae'])->name('admin.testing.mae');
    });
});
