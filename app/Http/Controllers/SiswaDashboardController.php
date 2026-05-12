<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Models\LogAktivitas;
use App\Models\Kuis;
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

        // ── Cek apakah kuis diagnostik sudah selesai ──
        $diagnostikDone = (bool) $user->diagnostic_done;
        $kuisDiagnostik = Kuis::where('is_diagnostik', true)->first();

        $rekomendasi = collect([]);
        $streak      = 0;

        if ($diagnostikDone) {
            // Mode normal: CF recommendations
            $rekomendasi = $this->cfService->getRecommendations($user->id, $user->kelas_id, 6);
            $streak      = $this->hitungStreak($user->id);
        }

        return view('siswa.dashboard', compact(
            'user', 'rekomendasi', 'streak', 'diagnostikDone', 'kuisDiagnostik'
        ));
    }

    private function hitungStreak(int $userId): int
    {
        $tanggalAktif = LogAktivitas::where('user_id', $userId)
            ->selectRaw('DATE(created_at) as tanggal')
            ->groupBy('tanggal')
            ->orderByDesc('tanggal')
            ->pluck('tanggal')
            ->map(fn($t) => Carbon::parse($t)->startOfDay());

        if ($tanggalAktif->isEmpty()) return 0;

        $today = Carbon::today();
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
