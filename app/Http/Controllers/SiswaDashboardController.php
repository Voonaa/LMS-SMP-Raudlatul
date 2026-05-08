<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
        $rekomendasi = $this->cfService->getRecommendations($user->id, $user->kelas_id, 2);

        return view('siswa.dashboard', compact('user', 'rekomendasi'));
    }
}
