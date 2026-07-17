<?php

namespace App\Services;

use App\Models\DefaultTask;
use App\Repositories\DefaultTaskRepository;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;

class DefaultTaskService
{
    public function __construct(
        protected DefaultTaskRepository $defaultTaskRepository
    ) {}

    public function getAll(): Collection
    {
        return $this->defaultTaskRepository->getAll();
    }

    public function create(array $data): DefaultTask
    {
        return $this->defaultTaskRepository->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'target_role' => $data['target_role'],
            'is_active'   => $data['is_active'] ?? 1,
            'created_by'  => Auth::id(),
        ]);
    }

    public function update(DefaultTask $defaultTask, array $data): bool
    {
        return $this->defaultTaskRepository->update($defaultTask, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'target_role' => $data['target_role'],
            'is_active'   => $data['is_active'] ?? 1,
        ]);
    }

    public function delete(DefaultTask $defaultTask): bool
    {
        return $this->defaultTaskRepository->delete($defaultTask);
    }
}