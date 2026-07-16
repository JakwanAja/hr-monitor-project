<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // Belum login
        if (! Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // Akun nonaktif — paksa logout
        if (! $user->is_active) {
            Auth::logout();
            return redirect()->route('login')
                ->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        // Role tidak sesuai
        if ($user->role !== $role) {
            abort(403, 'Anda tidak memiliki akses ke halaman ini.');
        }

        return $next($request);
    }
}