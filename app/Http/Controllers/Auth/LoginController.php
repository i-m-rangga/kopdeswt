<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class LoginController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('auth.login');
    }

    /**
     * Handle an authentication attempt.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        $request->authenticate();

        $request->session()->regenerate();

        return $this->redirectToDashboard();
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        Auth::guard('web')->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }

    /**
     * Redirect user to dashboard based on role.
     */
    protected function redirectToDashboard(): RedirectResponse
    {
        $role = Auth::user()->role;

        return match ($role) {
            'kasir' => redirect()->route('kasir.dashboard'),
            'petugas_persediaan' => redirect()->route('petugas.dashboard'),
            'pengurus' => redirect()->route('pengurus.dashboard'),
            default => redirect()->route('login')->withErrors(['role' => 'Role tidak dikenali.']),
        };
    }
}