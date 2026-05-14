<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AchievementController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        
        // 1. Dapatkan Leaderboard Data dari LeaderboardController
        $leaderboardController = new LeaderboardController();
        $response = $leaderboardController->getLeaderboard($user->kelas_id);
        $leaderboardData = json_decode($response->getContent(), true) ?? [];

        $leaderboard = collect($leaderboardData);
        
        // 2. Ambil data gamifikasi pengguna saat ini dari leaderboard data
        $myStats = $leaderboard->firstWhere('user_id', $user->id);
        $poin = $myStats ? $myStats['points'] : 0;
        $myRank = $myStats ? $myStats['rank'] : '10+';
        
        // 3. Hitung Streak secara dinamis
        $streak = $this->hitungStreak($user->id);

        // Ambil Top 10 untuk ditampilkan
        $top10 = $leaderboard->take(10);

        // Daftar Badges Statis (Aturan Gamifikasi)
        $badges = [
            [
                'id' => 'badge_1',
                'nama' => 'Penjelajah Pemula',
                'deskripsi' => 'Diberikan saat kamu berhasil mengumpulkan 50 poin pertama.',
                'ikon' => 'explore',
                'syarat_poin' => 50,
                'syarat_streak' => 0,
                'terbuka' => $poin >= 50
            ],
            [
                'id' => 'badge_2',
                'nama' => 'Pejuang Ilmu',
                'deskripsi' => 'Diberikan saat kamu mencapai 200 poin. Terus semangat!',
                'ikon' => 'local_fire_department',
                'syarat_poin' => 200,
                'syarat_streak' => 0,
                'terbuka' => $poin >= 200
            ],
            [
                'id' => 'badge_3',
                'nama' => 'Master Raudlatul',
                'deskripsi' => 'Pencapaian luar biasa! Kamu berhasil mendapatkan 500 poin.',
                'ikon' => 'workspace_premium',
                'syarat_poin' => 500,
                'syarat_streak' => 0,
                'terbuka' => $poin >= 500
            ],
            [
                'id' => 'badge_4',
                'nama' => 'Konsistensi Dasar',
                'deskripsi' => 'Login atau beraktivitas selama 3 hari berturut-turut.',
                'ikon' => 'event_available',
                'syarat_poin' => 0,
                'syarat_streak' => 3,
                'terbuka' => $streak >= 3
            ],
            [
                'id' => 'badge_5',
                'nama' => 'Sang Disiplin',
                'deskripsi' => 'Login atau beraktivitas selama 7 hari berturut-turut tanpa putus.',
                'ikon' => 'verified',
                'syarat_poin' => 0,
                'syarat_streak' => 7,
                'terbuka' => $streak >= 7
            ],
            [
                'id' => 'badge_6',
                'nama' => 'Pionir Akademik',
                'deskripsi' => 'Mengumpulkan 1000 poin dan mempertahankan 10 hari streak.',
                'ikon' => 'military_tech',
                'syarat_poin' => 1000,
                'syarat_streak' => 10,
                'terbuka' => ($poin >= 1000 && $streak >= 10)
            ],
        ];

        return view('siswa.achievements', [
            'user' => $user,
            'poin' => $poin,
            'streak' => $streak,
            'badges' => $badges,
            'leaderboard' => $top10,
            'myRank' => $myRank
        ]);
    }

    private function hitungStreak(int $userId): int
    {
        $tanggalAktif = \App\Models\LogAktivitas::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as tanggal')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->pluck('tanggal')
            ->map(fn($t) => \Illuminate\Support\Carbon::parse($t)->startOfDay());

        if ($tanggalAktif->isEmpty()) return 0;

        $today = \Illuminate\Support\Carbon::today();
        if (!$tanggalAktif->contains($today) && !$tanggalAktif->contains($today->copy()->subDay())) {
            return 0;
        }

        $streak    = 0;
        $checkDate = $tanggalAktif->first();

        foreach ($tanggalAktif as $tanggal) {
            if ($checkDate->isSameDay($tanggal)) {
                $streak++;
                $checkDate = $checkDate->copy()->subDay();
            } else {
                break;
            }
        }

        return $streak;
    }
}
