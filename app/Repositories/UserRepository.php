<?php

namespace App\Repositories;

use App\Models\User;
use Illuminate\Database\Eloquent\Builder;

class UserRepository
{
    public function __construct(protected User $model) {}

    public function findActiveByEmail(string $email): ?User
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->where('email', $email)
            ->where('is_active', 1)
            ->first();
    }
}