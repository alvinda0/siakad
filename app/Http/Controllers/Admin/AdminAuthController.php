<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ActivityLog;
use App\Models\Role;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AdminAuthController extends Controller
{
    /**
     * Tampilkan halaman login admin.
     */
    public function showLogin(): View|RedirectResponse
    {
        // Jika sudah login dan punya role admin/superadmin, langsung ke dashboard
        if (Auth::check() && Auth::user()->hasRole([Role::ADMIN, Role::SUPERADMIN])) {
            return redirect()->route('admin.dashboard');
        }

        return view('admin.login');
    }

    /**
     * Proses login admin.
     */
    public function login(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required', 'string'],
        ]);

        $remember = $request->boolean('remember');

        if (! Auth::attempt($credentials, $remember)) {
            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Email atau password salah.']);
        }

        $user = Auth::user();

        // Pastikan user memiliki role admin atau superadmin
        if (! $user->hasRole([Role::ADMIN, Role::SUPERADMIN])) {
            Auth::logout();

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['email' => 'Akun Anda tidak memiliki akses admin.']);
        }

        $request->session()->regenerate();

        // Log aktivitas login
        ActivityLog::record(
            action: 'login',
            model:  $user,
            label:  $user->name,
        );

        return redirect()->intended(route('admin.dashboard'));
    }

    /**
     * Logout admin.
     */
    public function logout(Request $request): RedirectResponse
    {
        // Log sebelum logout (masih ada auth)
        if (Auth::check()) {
            ActivityLog::record(
                action: 'logout',
                model:  Auth::user(),
                label:  Auth::user()->name,
            );
        }

        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }
}
