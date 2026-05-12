<?php

namespace App\Http\Controllers;

use App\Models\Tugas;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;

class SiswaTugasController extends Controller
{
    public function index()
    {
        $siswa = auth()->user();
        $tugas = Tugas::where('kelas_id', $siswa->kelas_id)
            ->with(['mata_pelajaran', 'guru', 'pengumpulan' => function ($q) use ($siswa) {
                $q->where('user_id', $siswa->id);
            }])
            ->latest()
            ->get();

        return view('siswa.tugas.index', compact('tugas'));
    }

    public function kumpulkan(Request $request, Tugas $tugas)
    {
        $request->validate([
            'file_jawaban' => 'required|file|max:20480', // max 20MB
        ]);

        $siswa = auth()->user();

        // Cegah pengumpulan ganda
        $existing = PengumpulanTugas::where('tugas_id', $tugas->id)
            ->where('user_id', $siswa->id)
            ->first();
        if ($existing) {
            return redirect()->route('siswa.tugas.index')->with('error', 'Anda sudah mengumpulkan tugas ini.');
        }

        $filePath = $request->file('file_jawaban')->store('tugas-jawaban', 'public');

        PengumpulanTugas::create([
            'tugas_id' => $tugas->id,
            'user_id' => $siswa->id,
            'file_jawaban' => $filePath,
        ]);

        return redirect()->route('siswa.tugas.index')->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
