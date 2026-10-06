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
     * @param  string|array  ...$roles
     */
    public function handle(Request $request, Closure $next, ...$roles): Response
    {
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Flatten roles array if passed as multiple arguments
        $allowedRoles = is_array($roles[0] ?? null) ? $roles[0] : $roles;

        if (! in_array($user->role, $allowedRoles)) {
            abort(403, 'Akses ditolak. Anda tidak memiliki hak akses ke halaman ini.');
        }

        if ($user->status !== 'aktif') {
            Auth::logout();
            return redirect()->route('login')->withErrors([
                'username' => 'Akun Anda tidak aktif. Silakan hubungi administrator.',
            ]);
        }

        return $next($request);
    }
}