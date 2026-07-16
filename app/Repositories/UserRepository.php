<?php

namespace App\Repositories;

use App\Models\User;

class UserRepository
{
    public function __construct(protected User $model) {}

    public function findActiveByEmail(string $email): ?User
    {
        return $this->model
            ->where('email', $email)
            ->where('is_active', 1)
            ->first();
    }
}