<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\LogAktivitas;
use App\Models\HasilKuis;

class LeaderboardController extends Controller
{
    public function getLeaderboard($kelasId)
    {
        // Ambil semua siswa di kelas tersebut
        $siswas = User::where('kelas_id', $kelasId)->where('role', 'siswa')->get();

        $leaderboard = [];

        foreach ($siswas as $siswa) {
            $totalPoints = 0;

            // 1. Poin dari Kuis (menggunakan HasilKuis)
            $hasilKuis = HasilKuis::with('kuis.mata_pelajaran')->where('user_id', $siswa->id)->get();
            foreach ($hasilKuis as $hasil) {
                $poin = $hasil->nilai; 
                // Jika Matematika, bobot 1.5x
                if ($hasil->kuis && $hasil->kuis->mata_pelajaran && $hasil->kuis->mata_pelajaran->nama_mapel == 'Matematika') {
                    $poin *= 1.5;
                }
                $totalPoints += $poin;
            }

            // 2. Poin dari Baca Materi & Aktivitas Forum (menggunakan LogAktivitas)
            // Untuk aktivitas forum, asumsikan mapel_id ada di thread. Karena kompleksitas relasi di log, kita sederhanakan
            // Baca Materi: 10 poin, Like Forum: 2 poin, Reply Forum: 5 poin
            $logs = LogAktivitas::where('user_id', $siswa->id)->get();
            foreach ($logs as $log) {
                $poin = 0;
                if ($log->jenis_aktivitas == 'baca_materi') {
                    $poin = 10;
                    // Simplifikasi jika mapelnya bisa dilacak, misal durasi > 0 juga tambah poin
                    if ($log->durasi) {
                        $poin += min(10, ceil($log->durasi / 60)); 
                    }
                } elseif ($log->jenis_aktivitas == 'like_forum') {
                    $poin = 2;
                } elseif ($log->jenis_aktivitas == 'reply_forum') {
                    $poin = 5;
                }
                $totalPoints += $poin;
            }

            $leaderboard[] = [
                'user_id' => $siswa->id,
                'name' => $siswa->name,
                'points' => (int) $totalPoints
            ];
        }

        // Sort descending by points
        usort($leaderboard, function($a, $b) {
            return $b['points'] <=> $a['points'];
        });

        // Add rank
        foreach ($leaderboard as $index => &$l) {
            $l['rank'] = $index + 1;
        }

        return response()->json($leaderboard);
    }
}
