<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckPasswordChanged
{
    /**
     * Paksa siswa mengganti password default sebelum bisa mengakses halaman lain.
     * Guru dan Admin dikecualikan dari aturan ini.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->role === 'siswa' && !$user->is_password_changed) {
            // Izinkan akses ke halaman ganti password & logout
            $allowedRoutes = ['profile.settings', 'profile.update', 'profile.password', 'profile.password.update', 'logout'];

            if (!in_array($request->route()?->getName(), $allowedRoutes)) {
                return redirect()->route('profile.settings')
                    ->with('force_password_change', true);
            }
        }

        return $next($request);
    }
}
