<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Kuis;
use App\Models\HasilKuis;
use App\Models\LogAktivitas;
use App\Models\Materi;

class SiswaDiagnostikController extends Controller
{
    public function show()
    {
        $user = Auth::user();

        if ($user->diagnostic_done) {
            return redirect()->route('siswa.dashboard')
                ->with('info', 'Kuis diagnostik sudah diselesaikan.');
        }

        $kuis = Kuis::where('is_diagnostik', true)
            ->with(['soal' => fn($q) => $q->orderBy('mata_pelajaran_id')->orderBy('id')])
            ->first();

        if (!$kuis) {
            return redirect()->route('siswa.dashboard')
                ->with('error', 'Kuis diagnostik belum tersedia. Hubungi administrator.');
        }

        return view('siswa.diagnostik.show', compact('kuis'));
    }

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

        $jawaban    = $request->input('jawaban', []);
        $totalSoal  = $kuis->soal->count();
        $totalBenar = 0;

        // ── 1. Hitung skor per mata pelajaran ──────────────────────────────────
        // Struktur: [mapel_id => ['benar' => N, 'total' => N]]
        $perMapel = [];

        foreach ($kuis->soal as $soal) {
            $mapelId = $soal->mata_pelajaran_id ?? 1;

            if (!isset($perMapel[$mapelId])) {
                $perMapel[$mapelId] = ['benar' => 0, 'total' => 0];
            }

            $perMapel[$mapelId]['total']++;

            $jawabanSiswa = strtoupper($jawaban[$soal->id] ?? '');
            if ($jawabanSiswa === $soal->jawaban_benar) {
                $perMapel[$mapelId]['benar']++;
                $totalBenar++;
            }
        }

        $nilaiTotal = $totalSoal > 0 ? round(($totalBenar / $totalSoal) * 100) : 0;

        // ── 2. Simpan HasilKuis ────────────────────────────────────────────────
        HasilKuis::create([
            'user_id' => $user->id,
            'kuis_id' => $kuis->id,
            'nilai'   => $nilaiTotal,
        ]);

        // ── 3. Buat sinyal CF ke log_aktivitas ─────────────────────────────────
        // Untuk SETIAP mata pelajaran di diagnostik, cari materi yang ada
        // di kelas siswa, lalu buat log dengan durasi sebanding kelemahan.
        // Format log IDENTIK dengan log membaca materi biasa (jenis_aktivitas='baca_materi').
        //
        // Bobot durasi:
        //   nilai 0   → durasi 300s (sangat lemah → prioritas tinggi di CF)
        //   nilai 100 → durasi  60s (sudah kuat   → sinyal kecil)
        //
        $nilaiPerMapel = [];
        $mapelNames    = [
            1 => 'Matematika',
            2 => 'IPA',
            3 => 'IPS',
            4 => 'Bahasa Indonesia',
            5 => 'Bahasa Inggris',
        ];

        foreach ($perMapel as $mapelId => $skor) {
            $nilaiMapel = $skor['total'] > 0
                ? round(($skor['benar'] / $skor['total']) * 100)
                : 0;

            $nilaiPerMapel[$mapelId] = $nilaiMapel;

            // Durasi sinyal: semakin rendah nilai → durasi semakin tinggi
            // Range: 60s (nilai 100) hingga 300s (nilai 0)
            $durasiSignal = (int) round(60 + (100 - $nilaiMapel) * 2.4);

            // ── PERBAIKAN KRITIS: Cari SEMUA materi mapel ini di kelas siswa ──
            // Gunakan kelas_id dari $user (bukan hard-coded), ambil semua materi
            // mapel tersebut agar CF punya variasi sinyal
            $materisMapel = Materi::where('kelas_id', $user->kelas_id)
                ->where('mata_pelajaran_id', $mapelId)
                ->get(['id']);

            if ($materisMapel->isEmpty()) {
                // Tidak ada materi mapel ini di kelas → catat tapi skip
                // (tidak bisa buat sinyal CF tanpa item_id yang valid)
                continue;
            }

            // Buat satu log per materi yang tersedia di mapel ini
            // Identik dengan format log 'baca_materi' biasa:
            //   user_id, jenis_aktivitas, item_id, durasi
            foreach ($materisMapel as $materi) {
                LogAktivitas::create([
                    'user_id'         => $user->id,
                    'jenis_aktivitas' => 'baca_materi',   // <-- sama persis dengan log baca materi
                    'item_id'         => $materi->id,      // <-- materi yang valid di kelas siswa
                    'durasi'          => $durasiSignal,    // <-- bobot sinyal berdasarkan nilai
                    // created_at default = now() → CF akan membacanya sebagai aktivitas baru
                ]);
            }
        }

        // ── 4. Set diagnostic_done = true ─────────────────────────────────────
        $user->diagnostic_done = true;
        $user->save();

        // ── 5. Identifikasi kelemahan terbesar ────────────────────────────────
        asort($nilaiPerMapel);  // sort ascending → index 0 = nilai terendah
        $weakestMapelId   = array_key_first($nilaiPerMapel);
        $weakestMapelNama = $mapelNames[$weakestMapelId] ?? 'Mata Pelajaran';
        $nilaiTerendah    = $nilaiPerMapel[$weakestMapelId];

        return response()->json([
            'success'          => true,
            'nilai_total'      => $nilaiTotal,
            'nilai_per_mapel'  => $nilaiPerMapel,
            'weakest_mapel'    => $weakestMapelNama,
            'nilai_terendah'   => $nilaiTerendah,
            'message'          => "Kuis selesai! Nilai kamu: {$nilaiTotal}/100. Rekomendasi akan difokuskan pada: {$weakestMapelNama}.",
            'redirect'         => route('siswa.dashboard'),
        ]);
    }
}
