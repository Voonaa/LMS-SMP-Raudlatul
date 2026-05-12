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

    public function create()
    {
        $guru = Auth::user();
        $materis = Materi::where('guru_id', $guru->id)->get();
        return view('guru.kuis.create', compact('materis'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'materi_id' => 'nullable|exists:materi,id',
        ]);

        $materi = null;
        if ($request->materi_id) {
            $materi = Materi::findOrFail($request->materi_id);
        }

        Kuis::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi ?? 'Kuis dibuat manual',
            'materi_id' => $request->materi_id,
            'mata_pelajaran_id' => $materi ? $materi->mata_pelajaran_id : null,
            'kelas_id' => $materi ? $materi->kelas_id : null,
            'guru_id' => Auth::id(),
        ]);

        return redirect()->route('guru.kuis.index')->with('success', 'Kuis berhasil dibuat.');
    }

    public function show($id)
    {
        $kuis = Kuis::where('guru_id', Auth::id())->with('soal', 'materi')->findOrFail($id);
        return view('guru.kuis.show', compact('kuis'));
    }

    public function edit($id)
    {
        $kuis = Kuis::where('guru_id', Auth::id())->findOrFail($id);
        $materis = Materi::where('guru_id', Auth::id())->get();
        return view('guru.kuis.edit', compact('kuis', 'materis'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required',
            'materi_id' => 'nullable|exists:materi,id',
        ]);

        $kuis = Kuis::where('guru_id', Auth::id())->findOrFail($id);
        
        $materi = null;
        if ($request->materi_id) {
            $materi = Materi::findOrFail($request->materi_id);
        }

        $kuis->update([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi ?? $kuis->deskripsi,
            'materi_id' => $request->materi_id,
            'mata_pelajaran_id' => $materi ? $materi->mata_pelajaran_id : $kuis->mata_pelajaran_id,
            'kelas_id' => $materi ? $materi->kelas_id : $kuis->kelas_id,
        ]);

        return redirect()->route('guru.kuis.index')->with('success', 'Kuis berhasil diperbarui.');
    }

    public function storeSoal(Request $request, $id)
    {
        $request->validate([
            'pertanyaan' => 'required',
            'opsi_a' => 'required',
            'opsi_b' => 'required',
            'opsi_c' => 'required',
            'opsi_d' => 'required',
            'jawaban_benar' => 'required|in:A,B,C,D',
        ]);

        $kuis = Kuis::where('guru_id', Auth::id())->findOrFail($id);

        SoalKuis::create([
            'kuis_id' => $kuis->id,
            'pertanyaan' => $request->pertanyaan,
            'opsi_a' => $request->opsi_a,
            'opsi_b' => $request->opsi_b,
            'opsi_c' => $request->opsi_c,
            'opsi_d' => $request->opsi_d,
            'jawaban_benar' => $request->jawaban_benar,
            'bobot' => 10,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan.');
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

    public function import(Request $request, $id)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt',
        ]);

        $kuis = Kuis::where('guru_id', Auth::id())->findOrFail($id);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // Format CSV: Pertanyaan, Opsi A, Opsi B, Opsi C, Opsi D, Jawaban Benar
            if (count($data) >= 6) {
                SoalKuis::create([
                    'kuis_id' => $kuis->id,
                    'pertanyaan' => $data[0],
                    'opsi_a' => $data[1],
                    'opsi_b' => $data[2],
                    'opsi_c' => $data[3],
                    'opsi_d' => $data[4],
                    'jawaban_benar' => $data[5],
                    'bobot' => 10,
                ]);
                $imported++;
            }
        }

        fclose($handle);

        return back()->with('success', "Berhasil mengimpor {$imported} soal ke kuis {$kuis->judul}.");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_soal.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Pertanyaan', 'Opsi A', 'Opsi B', 'Opsi C', 'Opsi D', 'Jawaban Benar (A/B/C/D)']);
            fputcsv($file, ['Apa ibu kota Indonesia?', 'Jakarta', 'Bandung', 'Surabaya', 'Medan', 'A']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
