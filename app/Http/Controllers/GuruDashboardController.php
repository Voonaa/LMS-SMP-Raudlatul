<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\MataPelajaran;
use App\Models\Kelas;
use App\Models\Materi;
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
        // Sederhananya, tampilkan semua materi yang dibuat oleh guru ini
        $materiList = Materi::where('guru_id', $user->id)->with('mata_pelajaran', 'kelas')->get();
        $mapelList = MataPelajaran::all();
        $kelasList = Kelas::all();

        return view('guru.dashboard', compact('user', 'materiList', 'mapelList', 'kelasList'));
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
