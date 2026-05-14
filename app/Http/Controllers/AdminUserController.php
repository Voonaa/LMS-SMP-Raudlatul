<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('kelas');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('kelas_id')) {
            $query->where('kelas_id', $request->kelas_id);
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(20)->withQueryString();
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        $totalSiswa = User::where('role', 'siswa')->count();
        $totalGuru  = User::where('role', 'guru')->count();
        $totalAdmin = User::where('role', 'admin')->count();

        return view('admin.user.index', compact('users', 'kelasList', 'totalSiswa', 'totalGuru', 'totalAdmin'));
    }

    public function show($id)
    {
        $user = User::with('kelas')->findOrFail($id);
        return view('admin.user.show', compact('user'));
    }

    public function create()
    {
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.user.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'role'     => 'required|in:admin,guru,siswa',
            'password' => 'required|string|min:6',
        ]);

        $user           = new User();
        $user->name     = $request->name;
        $user->username = trim($request->username);
        $user->role     = $request->role;
        $user->password = bcrypt($request->password);
        if (in_array($request->role, ['siswa', 'guru'])) {
            $user->kelas_id = $request->kelas_id;
        }
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function edit($id)
    {
        $user      = User::findOrFail($id);
        $kelasList = Kelas::orderBy('tingkat')->orderBy('nama_kelas')->get();
        return view('admin.user.edit', compact('user', 'kelasList'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username,' . $id,
            'role'     => 'required|in:admin,guru,siswa',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name     = $request->name;
        $user->username = trim($request->username);
        $user->role     = $request->role;
        if ($request->filled('password')) {
            $user->password = bcrypt($request->password);
        }
        $user->kelas_id = in_array($request->role, ['siswa', 'guru']) ? $request->kelas_id : null;
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'Data user berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        
        if ($user->role === 'admin') {
            return redirect()->route('admin.user.index')->with('error', 'Akun Administrator tidak dapat dihapus demi keamanan sistem.');
        }

        $user->delete();
        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }

    public function import(Request $request)
    {
        $request->validate([
            'file_excel' => 'required|mimes:xlsx,csv,xls',
        ]);

        $import = new \App\Imports\SiswaImport();
        \Maatwebsite\Excel\Facades\Excel::import($import, $request->file('file_excel'));

        $failures = $import->failures();
        if ($failures->isNotEmpty()) {
            return back()->with('warning', 'Import selesai. Beberapa data dilewati (duplikat/tidak valid).');
        }

        return back()->with('success', 'Berhasil mengimpor data siswa.');
    }

    public function downloadTemplate()
    {
        $headers = [
            'Content-Type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename="template_siswa.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['no', 'username', 'email', 'password', 'nama', 'id_kelas']);
            fputcsv($file, ['1', '1234567890', 'siswa@example.com', 'password123', 'Budi Santoso', '1']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}
