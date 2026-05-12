<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
use App\Models\HasilKuis;
use App\Models\LogAktivitas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaKuisController extends Controller
{
    public function index(Request $request)
    {
        $user  = Auth::user();
        $query = Kuis::where('kelas_id', $user->kelas_id)->with('mata_pelajaran');

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }

        $kuis      = $query->get();
        $mapelList = MataPelajaran::all();

        return view('siswa.kuis.index', compact('kuis', 'mapelList'));
    }

    public function show($id)
    {
        $user  = Auth::user();
        $kuis  = Kuis::with('soal')->findOrFail($id);

        // ─── CONTEXT-AWARE PRIVACY: Proteksi akses lintas kelas ───
        if ((int) $kuis->kelas_id !== (int) $user->kelas_id) {
            abort(403, 'Anda tidak memiliki akses ke kuis kelas ini.');
        }
        // ──────────────────────────────────────────────────────────

        // Cek apakah sudah pernah mengerjakan
        $hasil = HasilKuis::where('kuis_id', $id)->where('user_id', $user->id)->first();
        if ($hasil) {
            return redirect()->route('siswa.kuis.index')
                ->with('error', 'Anda sudah mengerjakan kuis ini dengan nilai: ' . $hasil->nilai);
        }

        return view('siswa.kuis.show', compact('kuis'));
    }

    public function submit(Request $request, $id)
    {
        $user = Auth::user();
        $kuis = Kuis::with('soal')->findOrFail($id);

        // ─── CONTEXT-AWARE PRIVACY: Proteksi submit lintas kelas ───
        if ((int) $kuis->kelas_id !== (int) $user->kelas_id) {
            abort(403, 'Submit kuis ditolak: bukan kuis kelas Anda.');
        }
        // ───────────────────────────────────────────────────────────

        $hasil = HasilKuis::where('kuis_id', $id)->where('user_id', $user->id)->first();
        if ($hasil) {
            return redirect()->route('siswa.kuis.index')
                ->with('error', 'Anda sudah mengerjakan kuis ini.');
        }

        $jawaban   = $request->input('jawaban', []); // ['soal_id' => 'A/B/C/D']
        $totalSoal = $kuis->soal->count();
        $benar     = 0;

        foreach ($kuis->soal as $soal) {
            if (isset($jawaban[$soal->id]) && $jawaban[$soal->id] == $soal->jawaban_benar) {
                $benar++;
            }
        }

        $nilai = $totalSoal > 0 ? round(($benar / $totalSoal) * 100) : 0;

        HasilKuis::create([
            'user_id' => $user->id,
            'kuis_id' => $kuis->id,
            'nilai'   => $nilai,
        ]);

        LogAktivitas::create([
            'user_id'         => $user->id,
            'jenis_aktivitas' => 'kerjakan_kuis',
            'item_id'         => $kuis->id,
            'durasi'          => $request->input('durasi', 60),
        ]);

        return redirect()->route('siswa.kuis.index')
            ->with('success', 'Kuis selesai! Nilai Anda: ' . $nilai);
    }
}
