<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\LogAktivitas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SiswaMateriController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $materis = Materi::where('kelas_id', $user->kelas_id)->with('mata_pelajaran', 'guru')->get();
        return view('siswa.materi.index', compact('materis'));
    }

    public function show($id)
    {
        $user = Auth::user();
        $materi = Materi::where('kelas_id', $user->kelas_id)->findOrFail($id);

        return view('siswa.materi.show', compact('materi'));
    }

    public function log(Request $request, $id)
    {
        $user = Auth::user();
        $materi = Materi::where('kelas_id', $user->kelas_id)->findOrFail($id);

        $durasi = $request->input('durasi', 60); // default 60s jika tidak ada

        LogAktivitas::create([
            'user_id' => $user->id,
            'jenis_aktivitas' => 'baca_materi',
            'item_id' => $materi->id,
            'durasi' => $durasi,
        ]);

        return response()->json(['success' => true]);
    }
}
