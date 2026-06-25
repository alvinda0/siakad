<?php

namespace App\Http\Middleware;

use App\Models\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class EnsureTeacher
{
    public function handle(Request $request, Closure $next): Response
    {
        if (! Auth::check()) {
            abort(403, 'Halaman ini hanya dapat diakses oleh guru.');
        }

        $user = Auth::user();

        // Superadmin boleh akses semua area termasuk portal guru
        if ($user->hasRole([Role::TEACHER, Role::SUPERADMIN])) {
            return $next($request);
        }

        abort(403, 'Halaman ini hanya dapat diakses oleh guru atau superadmin.');
    }
}
