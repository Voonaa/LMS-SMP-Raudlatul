<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Kelas;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        $users = User::with('kelas')->get();
        return view('admin.user.index', compact('users'));
    }

    public function create()
    {
        $kelasList = Kelas::all();
        return view('admin.user.create', compact('kelasList'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users',
            'role' => 'required|in:admin,guru,siswa',
            'password' => 'required|string|min:6',
        ]);

        $user = new User();
        $user->name = $request->name;
        $user->username = trim($request->username);
        $user->role = $request->role;
        $user->password = bcrypt($request->password);
        if ($request->role == 'siswa' || $request->role == 'guru') {
            $user->kelas_id = $request->kelas_id;
        }
        $user->save();

        return redirect()->route('admin.user.index')->with('success', 'User berhasil ditambahkan.');
    }

    public function destroy($id)
    {
        User::findOrFail($id)->delete();
        return redirect()->route('admin.user.index')->with('success', 'User berhasil dihapus.');
    }
}
