<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\LogAktivitas;
use App\Services\CollaborativeFilteringService;

class SiswaDashboardController extends Controller
{
    protected $cfService;

    public function __construct(CollaborativeFilteringService $cfService)
    {
        $this->cfService = $cfService;
    }

    public function index()
    {
        $user = Auth::user();
        
        // Dapatkan rekomendasi materi
        $rekomendasi = $this->cfService->getRecommendations($user->id, $user->kelas_id, 6);

        // Hitung Daily Streak (berapa hari berturut-turut user aktif)
        $streak = $this->hitungStreak($user->id);

        return view('siswa.dashboard', compact('user', 'rekomendasi', 'streak'));
    }

    /**
     * Menghitung streak belajar harian berturut-turut dari log_aktivitas.
     */
    private function hitungStreak(int $userId): int
    {
        // Ambil tanggal-tanggal unik aktivitas, diurutkan descending
        $tanggalAktif = LogAktivitas::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as tanggal')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->startOfDay());

        if ($tanggalAktif->isEmpty()) {
            return 0;
        }

        // Harus aktif hari ini atau kemarin agar streak tidak putus
        $today = Carbon::today();
        if (!$tanggalAktif->contains($today) && !$tanggalAktif->contains($today->copy()->subDay())) {
            return 0;
        }

        $streak = 0;
        $checkDate = $tanggalAktif->first(); // Mulai dari tanggal paling baru

        foreach ($tanggalAktif as $tanggal) {
            if ($checkDate->isSameDay($tanggal)) {
                $streak++;
                $checkDate = $checkDate->copy()->subDay();
            } else {
                break; // Streak putus
            }
        }

        return $streak;
    }
}
