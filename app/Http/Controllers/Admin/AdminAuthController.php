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
        if (Auth::check()) {
            $user = Auth::user();
            if ($user->hasRole([Role::ADMIN, Role::SUPERADMIN])) {
                return redirect()->route('admin.dashboard');
            }
            if ($user->hasRole(Role::TEACHER)) {
                return redirect()->route('guru.jadwal-ujian.index');
            }
            if ($user->hasRole(Role::STUDENT)) {
                return redirect()->route('murid.ujian.index');
            }
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

        // Cek role — admin/superadmin masuk dashboard, teacher masuk portal guru
        if ($user->hasRole([Role::ADMIN, Role::SUPERADMIN])) {
            $request->session()->regenerate();

            ActivityLog::record(
                action: 'login',
                model:  $user,
                label:  $user->name,
            );

            return redirect()->intended(route('admin.dashboard'));
        }

        if ($user->hasRole(Role::TEACHER)) {
            $request->session()->regenerate();

            return redirect()->intended(route('guru.jadwal-ujian.index'));
        }

        if ($user->hasRole(Role::STUDENT)) {
            $request->session()->regenerate();

            return redirect()->intended(route('murid.ujian.index'));
        }

        // Role lain (candidate, dll) tidak punya akses
        Auth::logout();

        return back()
            ->withInput($request->only('email'))
            ->withErrors(['email' => 'Akun Anda tidak memiliki akses ke sistem ini.']);
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
