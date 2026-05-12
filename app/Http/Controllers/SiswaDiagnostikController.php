<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kuis;
use App\Models\HasilKuis;
use App\Models\LogAktivitas;
use App\Models\SoalKuis;
use App\Models\Materi;

class SiswaDiagnostikController extends Controller
{
    /**
     * Tampilkan halaman kuis diagnostik.
     */
    public function show()
    {
        $user = Auth::user();

        // Jika sudah selesai, redirect ke dashboard
        if ($user->diagnostic_done) {
            return redirect()->route('siswa.dashboard')
                ->with('info', 'Kuis diagnostik sudah diselesaikan.');
        }

        $kuis = Kuis::where('is_diagnostik', true)->with('soal')->first();

        if (!$kuis) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Kuis diagnostik belum tersedia. Hubungi administrator.');
        }

        return view('siswa.diagnostik.show', compact('kuis'));
    }

    /**
     * Proses jawaban kuis diagnostik.
     * 1. Hitung nilai total & nilai per mata pelajaran
     * 2. Simpan ke hasil_kuis
     * 3. Simpan log_aktivitas per mapel (sinyal CF cold-start)
     * 4. Set diagnostic_done = true
     */
    public function submit(Request $request)
    {
        $user = Auth::user();

        if ($user->diagnostic_done) {
            return response()->json(['success' => false, 'message' => 'Sudah selesai.'], 422);
        }

        $kuis = Kuis::where('is_diagnostik', true)->with('soal')->first();
        if (!$kuis) {
            return response()->json(['success' => false, 'message' => 'Kuis tidak ditemukan.'], 404);
        }

        $jawaban = $request->input('jawaban', []);  // [soal_id => 'A/B/C/D']

        // ── Hitung skor per mata pelajaran ──
        $perMapel       = [];  // [mapel_id => ['benar' => 0, 'total' => 0]]
        $totalBenar     = 0;
        $totalSoal      = $kuis->soal->count();

        foreach ($kuis->soal as $soal) {
            $mapelId = $soal->mata_pelajaran_id ?? 1;
            if (!isset($perMapel[$mapelId])) {
                $perMapel[$mapelId] = ['benar' => 0, 'total' => 0];
            }
            $perMapel[$mapelId]['total']++;

            if (isset($jawaban[$soal->id]) && strtoupper($jawaban[$soal->id]) === $soal->jawaban_benar) {
                $perMapel[$mapelId]['benar']++;
                $totalBenar++;
            }
        }

        // Nilai total (0–100)
        $nilaiTotal = $totalSoal > 0 ? round(($totalBenar / $totalSoal) * 100) : 0;

        // ── Simpan ke hasil_kuis ──
        HasilKuis::create([
            'user_id' => $user->id,
            'kuis_id' => $kuis->id,
            'nilai'   => $nilaiTotal,
        ]);

        // ── Simpan log_aktivitas per mapel sebagai sinyal CF cold-start ──
        // Setiap log mewakili "keterlibatan" siswa terhadap mata pelajaran tersebut.
        // Nilai rendah → prioritas rekomendasi tinggi (cold-start remedial recommendation)
        $nilaiPerMapel = [];
        foreach ($perMapel as $mapelId => $skor) {
            $nilaiMapel = $skor['total'] > 0
                ? round(($skor['benar'] / $skor['total']) * 100)
                : 0;

            $nilaiPerMapel[$mapelId] = $nilaiMapel;

            // Bobot durasi: nilai tinggi = sedikit perlu remedial, nilai rendah = banyak perlu remedial
            // CF akan membaca durasi sebagai "engagement signal"
            $durasiSignal = max(60, (100 - $nilaiMapel) * 3);  // 0 nilai = 300s signal, 100 nilai = 60s

            // Ambil materi pertama dari mapel ini di kelas siswa
            $materi = Materi::where('kelas_id', $user->kelas_id)
                ->where('mata_pelajaran_id', $mapelId)
                ->first();

            if ($materi) {
                LogAktivitas::create([
                    'user_id'         => $user->id,
                    'jenis_aktivitas' => 'baca_materi',  // CF membaca ini
                    'item_id'         => $materi->id,
                    'durasi'          => $durasiSignal,  // Sinyal kebutuhan materi
                ]);
            }
        }

        // ── Set diagnostic_done = true ──
        $user->diagnostic_done = true;
        $user->save();

        // ── Identifikasi kelemahan (mapel nilai terendah) ──
        $mapelNames = [
            1 => 'Matematika',
            2 => 'IPA',
            3 => 'IPS',
            4 => 'Bahasa Indonesia',
            5 => 'Bahasa Inggris',
        ];

        asort($nilaiPerMapel);
        $weakestMapelId   = array_key_first($nilaiPerMapel);
        $weakestMapelNama = $mapelNames[$weakestMapelId] ?? 'Mata Pelajaran';
        $nilaiTerendah    = $nilaiPerMapel[$weakestMapelId];

        return response()->json([
            'success'       => true,
            'nilai_total'   => $nilaiTotal,
            'nilai_per_mapel' => $nilaiPerMapel,
            'weakest_mapel' => $weakestMapelNama,
            'nilai_terendah' => $nilaiTerendah,
            'message'       => "Kuis selesai! Nilai kamu: {$nilaiTotal}/100. Rekomendasi akan difokuskan pada: {$weakestMapelNama}.",
            'redirect'      => route('siswa.dashboard'),
        ]);
    }
}
