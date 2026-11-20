<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        // 1. Jika belum login (Guest), alihkan ke halaman login
        if (!Auth::check()) {
            return redirect()->route('login')->with('warning', 'Silakan masuk (login) terlebih dahulu untuk mengakses fitur ini.');
        }

        $user = Auth::user();

        // Load relasi role bila belum ter-load
        if (!$user->relationLoaded('role')) {
            $user->load('role');
        }

        $userRole = $user->role?->name;

        // 2. Cek apakah role user termasuk dalam daftar role yang diperbolehkan
        // Parameter bisa berupa argumen terpisah atau string dipisah koma (e.g. "staf_klinik,pemilik_hewan")
        $allowedRoles = [];
        foreach ($roles as $role) {
            foreach (explode(',', $role) as $r) {
                $trimmed = trim($r);
                if ($trimmed !== '') {
                    $allowedRoles[] = $trimmed;
                }
            }
        }

        if (!in_array($userRole, $allowedRoles)) {
            abort(403, 'Akses Ditolak (HTTP 403): Peran (' . ($user->role?->display_name ?? 'Tidak Ada') . ') tidak memiliki hak akses ke halaman/aksi ini.');
        }

        return $next($request);
    }
}
