<?php

namespace App\Http\Controllers;

use App\Models\Kelas;
use App\Models\Tugas;
use App\Models\MataPelajaran;
use App\Models\PengumpulanTugas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class GuruTugasController extends Controller
{
    public function index()
    {
        $guru = auth()->user();
        $tugas = Tugas::where('guru_id', $guru->id)
            ->with(['kelas', 'mata_pelajaran', 'pengumpulan'])
            ->latest()
            ->get();

        return view('guru.tugas.index', compact('tugas'));
    }

    public function create()
    {
        $guru = auth()->user();
        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::all();

        return view('guru.tugas.create', compact('kelasList', 'mapelList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'tenggat_waktu' => 'nullable|date',
            'file_lampiran' => 'nullable|file|max:10240',
        ]);

        $filePath = null;
        if ($request->hasFile('file_lampiran')) {
            $filePath = $request->file('file_lampiran')->store('tugas-lampiran', 'public');
        }

        Tugas::create([
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => auth()->id(),
            'tenggat_waktu' => $request->tenggat_waktu,
            'file_lampiran' => $filePath,
        ]);

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil ditambahkan!');
    }

    public function show(Tugas $tugas)
    {
        if ($tugas->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $pengumpulan = $tugas->pengumpulan()->with('user')->get();
        return view('guru.tugas.show', compact('tugas', 'pengumpulan'));
    }

    public function edit(Tugas $tugas)
    {
        if ($tugas->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::all();
        return view('guru.tugas.edit', compact('tugas', 'kelasList', 'mapelList'));
    }

    public function update(Request $request, Tugas $tugas)
    {
        if ($tugas->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'judul' => 'required|string|max:255',
            'deskripsi' => 'required|string',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
            'tenggat_waktu' => 'nullable|date',
            'file_lampiran' => 'nullable|file|max:10240',
        ]);

        $data = [
            'judul' => $request->judul,
            'deskripsi' => $request->deskripsi,
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'tenggat_waktu' => $request->tenggat_waktu,
        ];

        if ($request->hasFile('file_lampiran')) {
            if ($tugas->file_lampiran) {
                Storage::disk('public')->delete($tugas->file_lampiran);
            }
            $data['file_lampiran'] = $request->file('file_lampiran')->store('tugas-lampiran', 'public');
        }

        $tugas->update($data);

        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil diperbarui!');
    }

    public function destroy(Tugas $tugas)
    {
        if ($tugas->guru_id !== auth()->id()) {
            abort(403, 'Unauthorized action.');
        }
        
        if ($tugas->file_lampiran) {
            Storage::disk('public')->delete($tugas->file_lampiran);
        }
        
        $tugas->delete();
        return redirect()->route('guru.tugas.index')->with('success', 'Tugas berhasil dihapus.');
    }

    public function nilai(Request $request, $id)
    {
        $request->validate([
            'nilai' => 'required|numeric|min:0|max:100',
            'komentar' => 'nullable|string',
        ]);

        $pengumpulan = PengumpulanTugas::findOrFail($id);
        
        // Verifikasi bahwa tugas ini milik guru yang login
        if ($pengumpulan->tugas->guru_id !== auth()->id()) {
            abort(403);
        }

        $pengumpulan->update([
            'nilai' => $request->nilai,
            'komentar' => $request->komentar,
        ]);

        return back()->with('success', 'Nilai berhasil disimpan!');
    }
}
