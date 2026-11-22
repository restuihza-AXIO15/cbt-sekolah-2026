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
        if (!Auth::check()) {
            return redirect()->route('login')->with('error', 'Silakan login terlebih dahulu.');
        }

        $user = Auth::user();

        if (!in_array($user->role, $roles)) {
            // If user role is not allowed, redirect to their home
            if ($user->isAdmin()) {
                return redirect()->route('admin.dashboard')->with('error', 'Akses ditolak.');
            } elseif ($user->isGuru()) {
                return redirect()->route('guru.dashboard')->with('error', 'Akses ditolak.');
            } else {
                return redirect()->route('siswa.konfirmasi')->with('error', 'Akses ditolak.');
            }
        }

        return $next($request);
    }
}
