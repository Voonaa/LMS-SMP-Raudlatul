<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Materi;
use App\Models\Kuis;
use App\Models\Kelas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruLaporanController extends Controller
{
    public function index()
    {
        $guru = Auth::user();
        
        // Cari kelas-kelas yang diajar guru ini melalui relasi materi/kuis
        $kelasIds = array_unique(array_merge(
            Materi::where('guru_id', $guru->id)->pluck('kelas_id')->toArray(),
            Kuis::where('guru_id', $guru->id)->pluck('kelas_id')->toArray()
        ));

        $kelasList = Kelas::whereIn('id', $kelasIds)->get();
        $siswa = User::where('role', 'siswa')->whereIn('kelas_id', $kelasIds)->with('kelas', 'log_aktivitas', 'hasil_kuis')->get();

        return view('guru.laporan.index', compact('siswa', 'kelasList'));
    }
}
