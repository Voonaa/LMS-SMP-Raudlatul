<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Materi;
use App\Models\User;
use App\Models\HasilKuis;
use App\Models\LogAktivitas;
use App\Models\ForumThread;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use App\Services\GeminiService;

class GuruDashboardController extends Controller
{
    protected $gemini;

    public function __construct(GeminiService $gemini)
    {
        $this->gemini = $gemini;
    }

    public function index()
    {
        $user = Auth::user();
        $materiList = Materi::where('guru_id', $user->id)->with('mata_pelajaran', 'kelas')->get();
        $mapelList = MataPelajaran::all();
        $kelasList = Kelas::all();

        // Statistik Ringkas
        $totalSiswa = User::where('role', 'siswa')->count();
        $rataRataKuis = HasilKuis::avg('nilai') ?? 0;
        
        $totalMateri = Materi::count();
        $materiDibaca = LogAktivitas::where('jenis_aktivitas', 'baca_materi')->distinct('item_id')->count('item_id');
        $persentasePenyelesaian = $totalMateri > 0 ? round(($materiDibaca / $totalMateri) * 100, 1) : 0;

        // Student at Risk (5 Siswa nilai terendah atau aktivitas paling sedikit)
        $studentsAtRisk = User::where('role', 'siswa')
            ->withCount('log_aktivitas')
            ->withAvg('hasil_kuis as avg_skor', 'nilai')
            ->orderBy('avg_skor', 'asc')
            ->orderBy('log_aktivitas_count', 'asc')
            ->take(5)
            ->get();

        // Grafik Aktivitas (Tren 7 hari terakhir)
        $aktivitas7Hari = [];
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::now()->subDays($i)->format('Y-m-d');
            $count = LogAktivitas::whereDate('created_at', $date)->count();
            $aktivitas7Hari[$date] = $count;
        }

        // Monitoring Forum (Top 5 Threads berdasarkan jumlah balasan)
        $topForums = ForumThread::withCount('replies')
            ->orderBy('replies_count', 'desc')
            ->take(5)
            ->get();

        // Rekomendasi Terpopuler (Materi paling banyak dibaca)
        $topMateri = LogAktivitas::where('jenis_aktivitas', 'baca_materi')
            ->select('item_id', DB::raw('count(*) as total'))
            ->groupBy('item_id')
            ->orderBy('total', 'desc')
            ->take(5)
            ->get()
            ->map(function ($log) {
                $materi = Materi::find($log->item_id);
                return [
                    'judul' => $materi->judul ?? 'Materi Dihapus',
                    'total' => $log->total
                ];
            });

        return view('guru.dashboard', compact(
            'user', 'materiList', 'mapelList', 'kelasList',
            'totalSiswa', 'rataRataKuis', 'persentasePenyelesaian',
            'studentsAtRisk', 'aktivitas7Hari', 'topForums', 'topMateri'
        ));
    }

    public function generateMateri(Request $request)
    {
        $request->validate([
            'topik' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id'
        ]);

        $kelas = \App\Models\Kelas::findOrFail($request->kelas_id);
        $result = $this->gemini->generateRancanganMateri($request->topik, $kelas->tingkat);
        
        if ($result && isset($result['judul']) && isset($result['konten_html'])) {
            return response()->json([
                'success' => true,
                'judul' => $result['judul'],
                'konten_html' => $result['konten_html']
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal generate materi dari AI'], 500);
    }

    public function generateKuis(Request $request)
    {
        $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'jumlah_soal' => 'required|integer|min:1|max:10'
        ]);

        $materi = Materi::findOrFail($request->materi_id);
        $result = $this->gemini->generateKuis($materi->konten, $request->jumlah_soal);

        if ($result && is_array($result)) {
            return response()->json([
                'success' => true,
                'soal' => $result
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Gagal generate kuis dari AI'], 500);
    }
}
