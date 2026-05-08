<?php

namespace App\Http\Controllers;

use App\Models\Kuis;
use App\Models\SoalKuis;
use App\Models\Materi;
use App\Services\GeminiService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GuruKuisController extends Controller
{
    public function index()
    {
        $guru = Auth::user();
        $kuis = Kuis::where('guru_id', $guru->id)->with('kelas', 'mata_pelajaran', 'soal')->latest()->get();
        $materis = Materi::where('guru_id', $guru->id)->get();
        return view('guru.kuis.index', compact('kuis', 'materis'));
    }

    public function generate(Request $request, GeminiService $gemini)
    {
        $request->validate([
            'materi_id' => 'required|exists:materi,id',
            'jumlah_soal' => 'required|integer|min:1|max:20'
        ]);

        $materi = Materi::findOrFail($request->materi_id);

        try {
            DB::beginTransaction();

            // Panggil AI
            $hasilJSON = $gemini->generateKuis(strip_tags($materi->konten), $request->jumlah_soal);

            if (!$hasilJSON || !is_array($hasilJSON)) {
                return response()->json(['success' => false, 'message' => 'Gagal generate soal dari AI. Pastikan API Key valid atau coba lagi.'], 500);
            }

            // Buat Kuis
            $kuis = Kuis::create([
                'judul' => 'Kuis AI: ' . $materi->judul,
                'deskripsi' => 'Kuis digenerate otomatis menggunakan AI Gemini untuk materi ' . $materi->judul,
                'materi_id' => $materi->id,
                'mata_pelajaran_id' => $materi->mata_pelajaran_id,
                'kelas_id' => $materi->kelas_id,
                'guru_id' => Auth::id(),
            ]);

            // Insert Soal
            foreach ($hasilJSON as $soalData) {
                SoalKuis::create([
                    'kuis_id' => $kuis->id,
                    'pertanyaan' => $soalData['pertanyaan'] ?? 'Soal Kosong',
                    'opsi_a' => $soalData['opsi_a'] ?? '-',
                    'opsi_b' => $soalData['opsi_b'] ?? '-',
                    'opsi_c' => $soalData['opsi_c'] ?? '-',
                    'opsi_d' => $soalData['opsi_d'] ?? '-',
                    'jawaban_benar' => $soalData['jawaban_benar'] ?? 'A',
                    'bobot' => 10,
                ]);
            }

            DB::commit();
            return response()->json(['success' => true, 'message' => 'Kuis berhasil digenerate!']);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Kuis Generate Error: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan sistem.'], 500);
        }
    }

    public function destroy($id)
    {
        $kuis = Kuis::where('guru_id', Auth::id())->findOrFail($id);
        $kuis->delete();
        return redirect()->route('guru.kuis.index')->with('success', 'Kuis berhasil dihapus.');
    }
}
