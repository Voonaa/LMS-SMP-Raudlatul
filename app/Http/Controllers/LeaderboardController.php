<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\HasilKuis;
use Illuminate\Support\Facades\Auth;

class LeaderboardController extends Controller
{
    /**
     * Endpoint leaderboard per kelas.
     * 
     * CONTEXT-AWARE PRIVACY: Siswa hanya boleh melihat leaderboard kelasnya sendiri.
     * Jika request datang dari siswa yang meminta kelas lain → 403.
     */
    public function getLeaderboard($kelasId)
    {
        $requestingUser = Auth::user();

        // ─── CONTEXT-AWARE PRIVACY: Validasi kelas ───
        // Jika yang request adalah siswa, pastikan hanya bisa melihat kelas sendiri
        if ($requestingUser && $requestingUser->role === 'siswa') {
            if ((int) $requestingUser->kelas_id !== (int) $kelasId) {
                return response()->json([
                    'error' => 'Akses ditolak. Anda hanya dapat melihat leaderboard kelas Anda sendiri.'
                ], 403);
            }
        }
        // Guru & Admin bebas mengakses leaderboard kelas manapun
        // ─────────────────────────────────────────────────────────

        // Ambil semua siswa di kelas tersebut
        $siswas      = User::where('kelas_id', $kelasId)->where('role', 'siswa')->get();
        $leaderboard = [];

        foreach ($siswas as $siswa) {
            $totalPoints = 0;

            // 1. Poin dari Kuis (nilai × bobot mapel)
            $hasilKuis = HasilKuis::with('kuis.mata_pelajaran')
                ->where('user_id', $siswa->id)
                ->get();
            foreach ($hasilKuis as $hasil) {
                $poin = $hasil->nilai;
                // Matematika mendapat bobot 1.5×
                if ($hasil->kuis && $hasil->kuis->mata_pelajaran
                    && $hasil->kuis->mata_pelajaran->nama_mapel === 'Matematika') {
                    $poin *= 1.5;
                }
                $totalPoints += $poin;
            }

            // 2. Poin dari Aktivitas Belajar (Log Aktivitas)
            $logs = LogAktivitas::where('user_id', $siswa->id)->get();
            foreach ($logs as $log) {
                $poin = 0;
                if ($log->jenis_aktivitas === 'baca_materi') {
                    $poin = 10 + min(10, (int) ceil($log->durasi / 60));
                } elseif ($log->jenis_aktivitas === 'like_forum') {
                    $poin = 2;
                } elseif ($log->jenis_aktivitas === 'reply_forum') {
                    $poin = 5;
                } elseif ($log->jenis_aktivitas === 'kerjakan_kuis') {
                    $poin = 5; // bonus poin karena berpartisipasi
                }
                $totalPoints += $poin;
            }

            $leaderboard[] = [
                'user_id' => $siswa->id,
                'name'    => $siswa->name,
                'points'  => (int) $totalPoints,
            ];
        }

        // Sort descending by points
        usort($leaderboard, fn($a, $b) => $b['points'] <=> $a['points']);

        // Tambahkan rank
        foreach ($leaderboard as $index => &$entry) {
            $entry['rank']   = $index + 1;
            // Tandai user yang sedang login (untuk highlight di UI)
            $entry['is_me']  = $requestingUser && $entry['user_id'] === $requestingUser->id;
        }

        return response()->json($leaderboard);
    }
}
