<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\Auth;

class UserService
{
    public function __construct(
        protected UserRepository $userRepository
    ) {}

    public function getAllUsers(): Collection
    {
        return $this->userRepository->getAllExceptAdmin();
    }

    public function createUser(array $data): User
    {
        if ($this->userRepository->isEmailTaken($data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        return $this->userRepository->create([
            'name'      => $data['name'],
            'email'     => $data['email'],
            'password'  => Hash::make($data['password']),
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? 1,
        ]);
    }

    public function updateUser(User $user, array $data): bool
    {
        if ($this->userRepository->isEmailTaken($data['email'], $user->id)) {
            throw ValidationException::withMessages([
                'email' => 'Email sudah digunakan oleh pengguna lain.',
            ]);
        }

        return $this->userRepository->update($user, [
            'name'      => $data['name'],
            'email'     => $data['email'],
            'role'      => $data['role'],
            'is_active' => $data['is_active'] ?? $user->is_active,
        ]);
    }

    public function updatePassword(User $user, string $newPassword): bool
    {
        return $this->userRepository->updatePassword(
            $user,
            Hash::make($newPassword)
        );
    }

    public function deleteUser(User $user): bool
    {
        // Cegah hapus diri sendiri
        if ($user->id === Auth::id()) {
            throw ValidationException::withMessages([
                'user' => 'Tidak dapat menghapus akun sendiri.',
            ]);
        }

        return $this->userRepository->delete($user);
    }
}