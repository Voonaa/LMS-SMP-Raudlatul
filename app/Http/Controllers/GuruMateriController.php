<?php

namespace App\Http\Controllers;

use App\Models\Materi;
use App\Models\Kelas;
use App\Models\MataPelajaran;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuruMateriController extends Controller
{
    public function index(Request $request)
    {
        $guru = Auth::user();
        $query = Materi::where('guru_id', $guru->id)->with('kelas', 'mata_pelajaran');

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        if ($request->filled('mapel_id')) {
            $query->where('mata_pelajaran_id', $request->mapel_id);
        }

        $materis = $query->latest()->get();
        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::all();

        return view('guru.materi.index', compact('materis', 'kelasList', 'mapelList'));
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

        $materi = Materi::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
            'guru_id' => Auth::id(),
        ]);

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Materi berhasil ditambahkan.', 'data' => $materi]);
        }

        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil ditambahkan.');
    }

    public function show($id)
    {
        $materi = Materi::where('guru_id', Auth::id())->with('kelas', 'mata_pelajaran')->findOrFail($id);
        return view('guru.materi.show', compact('materi'));
    }

    public function edit($id)
    {
        $materi = Materi::where('guru_id', Auth::id())->findOrFail($id);
        $kelasList = Kelas::all();
        $mapelList = MataPelajaran::all();
        return view('guru.materi.edit', compact('materi', 'kelasList', 'mapelList'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'judul' => 'required',
            'konten' => 'required',
            'kelas_id' => 'required|exists:kelas,id',
            'mata_pelajaran_id' => 'required|exists:mata_pelajaran,id',
        ]);

        $materi = Materi::where('guru_id', Auth::id())->findOrFail($id);
        $materi->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
            'kelas_id' => $request->kelas_id,
            'mata_pelajaran_id' => $request->mata_pelajaran_id,
        ]);

        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $materi = Materi::where('guru_id', Auth::id())->findOrFail($id);
        $materi->delete();
        return redirect()->route('guru.materi.index')->with('success', 'Materi berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_csv' => 'required|file|mimes:csv,txt',
        ]);

        $file = $request->file('file_csv');
        $handle = fopen($file->getRealPath(), 'r');
        
        // Skip header
        fgetcsv($handle);

        $imported = 0;
        while (($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
            // Format CSV: Judul, Konten, Nama Kelas, Nama Mapel
            if (count($data) >= 4) {
                $judul = $data[0];
                $konten = $data[1];
                $namaKelas = $data[2];
                $namaMapel = $data[3];

                $kelas = \App\Models\Kelas::where('nama_kelas', $namaKelas)->first();
                $mapel = \App\Models\MataPelajaran::where('nama_mapel', $namaMapel)->first();

                if ($kelas && $mapel) {
                    Materi::create([
                        'judul' => $judul,
                        'konten' => $konten,
                        'kelas_id' => $kelas->id,
                        'mata_pelajaran_id' => $mapel->id,
                        'guru_id' => Auth::id(),
                    ]);
                    $imported++;
                }
            }
        }

        fclose($handle);

        return back()->with('success', "Berhasil mengimpor {$imported} materi.");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_materi.csv"',
        ];

        $callback = function() {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Judul', 'Konten', 'Nama Kelas', 'Nama Mapel']);
            fputcsv($file, ['Contoh Judul Materi', 'Isi konten materi di sini...', '7A', 'IPA']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
