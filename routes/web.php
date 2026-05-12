<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SiswaDashboardController;
use App\Http\Controllers\GuruDashboardController;
use App\Http\Controllers\ForumController;
use App\Http\Controllers\LeaderboardController;
use App\Http\Controllers\GuruTugasController;
use App\Http\Controllers\SiswaTugasController;
use App\Http\Controllers\GuruMateriController;
use App\Http\Controllers\GuruKuisController;
use App\Http\Controllers\GuruLaporanController;
use App\Http\Controllers\SiswaMateriController;
use App\Http\Controllers\SiswaKuisController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TestingController;
use App\Http\Controllers\AdminUserController;
use App\Http\Controllers\AdminConfigController;

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
        Route::get('/siswa/materi', [SiswaMateriController::class, 'index'])->name('siswa.materi.index');
        Route::get('/siswa/materi/{id}', [SiswaMateriController::class, 'show'])->name('siswa.materi.show');
        Route::post('/siswa/materi/{id}/log', [SiswaMateriController::class, 'log'])->name('siswa.materi.log');
        
        // Kuis
        Route::get('/siswa/kuis', [SiswaKuisController::class, 'index'])->name('siswa.kuis.index');
        Route::get('/siswa/kuis/{id}', [SiswaKuisController::class, 'show'])->name('siswa.kuis.show');
        Route::post('/siswa/kuis/{id}/submit', [SiswaKuisController::class, 'submit'])->name('siswa.kuis.submit');

        // Tugas (Pengumpulan)
        Route::get('/siswa/tugas', [SiswaTugasController::class, 'index'])->name('siswa.tugas.index');
        Route::post('/siswa/tugas/{tugas}/kumpulkan', [SiswaTugasController::class, 'kumpulkan'])->name('siswa.tugas.kumpulkan');
    });

    // Rute Guru
    Route::middleware('can:guru')->group(function () {
        Route::get('/guru/dashboard', [GuruDashboardController::class, 'index'])->name('guru.dashboard');
        
        // Kelola Materi
        Route::get('/guru/materi', [GuruMateriController::class, 'index'])->name('guru.materi.index');
        Route::get('/guru/materi/create', [GuruMateriController::class, 'create'])->name('guru.materi.create');
        Route::post('/guru/materi', [GuruMateriController::class, 'store'])->name('guru.materi.store');
        Route::get('/guru/materi/template', [GuruMateriController::class, 'downloadTemplate'])->name('guru.materi.template');
        Route::post('/guru/materi/import', [GuruMateriController::class, 'import'])->name('guru.materi.import');
        Route::get('/guru/materi/{id}', [GuruMateriController::class, 'show'])->name('guru.materi.show');
        Route::get('/guru/materi/{id}/edit', [GuruMateriController::class, 'edit'])->name('guru.materi.edit');
        Route::put('/guru/materi/{id}', [GuruMateriController::class, 'update'])->name('guru.materi.update');
        Route::delete('/guru/materi/{id}', [GuruMateriController::class, 'destroy'])->name('guru.materi.destroy');
        
        // Tetap simpan rute AI generate materi dari dashboard
        Route::post('/guru/materi/generate', [GuruDashboardController::class, 'generateMateri'])->name('guru.materi.generate');

        // Kelola Kuis
        Route::get('/guru/kuis', [GuruKuisController::class, 'index'])->name('guru.kuis.index');
        Route::get('/guru/kuis/create', [GuruKuisController::class, 'create'])->name('guru.kuis.create');
        Route::post('/guru/kuis', [GuruKuisController::class, 'store'])->name('guru.kuis.store');
        Route::get('/guru/kuis/template', [GuruKuisController::class, 'downloadTemplate'])->name('guru.kuis.template');
        Route::get('/guru/kuis/{id}', [GuruKuisController::class, 'show'])->name('guru.kuis.show');
        Route::get('/guru/kuis/{id}/edit', [GuruKuisController::class, 'edit'])->name('guru.kuis.edit');
        Route::put('/guru/kuis/{id}', [GuruKuisController::class, 'update'])->name('guru.kuis.update');
        Route::post('/guru/kuis/{id}/soal', [GuruKuisController::class, 'storeSoal'])->name('guru.kuis.soal.store');
        Route::post('/guru/kuis/generate', [GuruKuisController::class, 'generate'])->name('guru.kuis.generate');
        Route::post('/guru/kuis/{id}/import', [GuruKuisController::class, 'import'])->name('guru.kuis.import');
        Route::delete('/guru/kuis/{id}', [GuruKuisController::class, 'destroy'])->name('guru.kuis.destroy');

        // Kelola Tugas
        Route::get('/guru/tugas', [GuruTugasController::class, 'index'])->name('guru.tugas.index');
        Route::get('/guru/tugas/create', [GuruTugasController::class, 'create'])->name('guru.tugas.create');
        Route::post('/guru/tugas', [GuruTugasController::class, 'store'])->name('guru.tugas.store');
        Route::get('/guru/tugas/{tugas}', [GuruTugasController::class, 'show'])->name('guru.tugas.show');
        Route::get('/guru/tugas/{tugas}/edit', [GuruTugasController::class, 'edit'])->name('guru.tugas.edit');
        Route::put('/guru/tugas/{tugas}', [GuruTugasController::class, 'update'])->name('guru.tugas.update');
        Route::delete('/guru/tugas/{tugas}', [GuruTugasController::class, 'destroy'])->name('guru.tugas.destroy');
        Route::post('/guru/tugas/nilai/{id}', [GuruTugasController::class, 'nilai'])->name('guru.tugas.nilai');

        // Laporan
        Route::get('/guru/laporan', [GuruLaporanController::class, 'index'])->name('guru.laporan.index');
    });

    // Forum (Bisa diakses guru & siswa)
    Route::get('/forum', [ForumController::class, 'index'])->name('forum.index');
    Route::post('/forum', [ForumController::class, 'store'])->name('forum.store');
    Route::post('/forum/{id}/like', [ForumController::class, 'like'])->name('forum.like');
    Route::post('/forum/{id}/reply', [ForumController::class, 'reply'])->name('forum.reply');

    // Leaderboard (API)
    Route::get('/api/leaderboard/{kelasId}', [LeaderboardController::class, 'getLeaderboard']);

    // Settings / Profile
    Route::get('/settings', [ProfileController::class, 'index'])->name('profile.settings');
    Route::post('/settings', [ProfileController::class, 'update'])->name('profile.update');

    // Route Admin Testing & Management
    Route::middleware('can:admin')->group(function () {
        Route::get('/admin/dashboard', function () {
            return view('admin.dashboard');
        })->name('admin.dashboard');

        Route::get('/admin/testing/mae', [TestingController::class, 'mae'])->name('admin.testing.mae');
        
        // Kelola Pengguna
        Route::get('/admin/user', [AdminUserController::class, 'index'])->name('admin.user.index');
        Route::get('/admin/user/create', [AdminUserController::class, 'create'])->name('admin.user.create');
        Route::post('/admin/user', [AdminUserController::class, 'store'])->name('admin.user.store');
        Route::delete('/admin/user/{id}', [AdminUserController::class, 'destroy'])->name('admin.user.destroy');

        // Konfigurasi
        Route::get('/admin/config', [AdminConfigController::class, 'index'])->name('admin.config.index');
        Route::post('/admin/config', [AdminConfigController::class, 'save'])->name('admin.config.save');
    });
});
