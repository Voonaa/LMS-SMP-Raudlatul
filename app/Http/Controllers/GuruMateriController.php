<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruMateriController extends Controller
{
    public function index()
    {
        $guru = Auth::user();
        $materis = Materi::where('guru_id', $guru->id)->with('kelas', 'mata_pelajaran')->latest()->get();
        return view('guru.materi.index', compact('materis'));
    }

    public function create()
    {
        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::all();
        return view('guru.materi.create', compact('kelasList', 'mapelList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        ]);

        Materi::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => Auth::id(),
        ]);

        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        $materi = Materi::where('guru_id', Auth::id())->findOrFail($id);
        $materi->delete();
        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil dihapus.');
    }
}
