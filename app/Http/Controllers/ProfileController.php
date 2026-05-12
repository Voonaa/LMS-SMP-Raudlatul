<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        return view('profile.settings', compact('user'));
    }

    /**
     * Update profil umum (nama).
     */
    public function update(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        $user = Auth::user();
        $user->name = $request->name;
        $user->save();

        return back()->with('success', 'Profil berhasil diperbarui.');
    }

    /**
     * Update password — jika berhasil, tandai is_password_changed = true.
     * Ini adalah endpoint yang juga bisa diakses siswa yang belum ganti password.
     */
    public function updatePassword(Request $request)
    {
        $request->validate([
            'current_password'          => 'required',
            'new_password'              => 'required|min:6|confirmed',
            'new_password_confirmation' => 'required',
        ]);

        $user = Auth::user();

        // Verifikasi password lama
        if (!Hash::check($request->current_password, $user->password)) {
            return back()->withErrors(['current_password' => 'Password saat ini salah.'])->withInput();
        }

        // Simpan password baru & tandai sudah ganti password
        $user->password           = Hash::make($request->new_password);
        $user->is_password_changed = true;
        $user->save();

        // Redirect ke dashboard sesuai role setelah berhasil ganti password
        $redirect = match ($user->role) {
            'siswa' => route('siswa.dashboard'),
            'guru'  => route('guru.dashboard'),
            default => route('admin.dashboard'),
        };

        return redirect($redirect)->with('success', '✅ Password berhasil diperbarui. Selamat belajar!');
    }
}
