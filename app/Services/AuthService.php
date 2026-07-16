<?php

namespace App\Services;

use App\Repositories\UserRepository;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AuthService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function login(array $credentials): void
    {
        // Cek apakah email terdaftar dan akun aktif
        $user = $this->userRepository->findActiveByEmail($credentials['email']);

        if (! $user) {
            throw ValidationException::withMessages([
                'email' => 'Email tidak terdaftar atau akun tidak aktif.',
            ]);
        }

        // Attempt login via Laravel Auth
        if (! Auth::attempt($credentials, $credentials['remember'] ?? false)) {
            throw ValidationException::withMessages([
                'email' => 'Email atau password salah.',
            ]);
        }

        // Regenerate session untuk keamanan
        request()->session()->regenerate();
    }

    public function logout(): void
    {
        Auth::logout();
        request()->session()->invalidate();
        request()->session()->regenerateToken();
    }

    /**
     * Redirect setelah login berdasarkan role.
     */
    public function redirectByRole(): string
    {
        return match(Auth::user()->role) {
            'admin'        => route('admin.dashboard'),
            'hr_staff'     => route('staff.dashboard'),
            'hr_assistant' => route('assistant.dashboard'),
            default        => route('login'),
        };
    }
}