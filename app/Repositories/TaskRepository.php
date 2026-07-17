<?php

namespace App\Repositories;

use App\Models\Task;
use App\Models\TaskAssignment;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Carbon;

class TaskRepository
{
    public function __construct(protected Task $model) {}

    public function getAllForAdmin(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignedUsers:id,name,role'])
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAssignedTasksForStaff(int $staffId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['assignments', 'assignedUsers:id,name,role'])
            ->where('created_by', $staffId)
            ->where('type', 'assigned')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function findById(int $id): ?Task
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query->with(['assignments', 'assignedUsers'])->find($id);
    }

    public function create(array $data): Task
    {
        return $this->model->create($data);
    }

    public function update(Task $task, array $data): bool
    {
        return $task->update($data);
    }

    public function delete(Task $task): bool
    {
        /** @var Task $task */
        return $task->delete();
    }

    public function createAssignment(int $taskId, int $userId): TaskAssignment
    {
        return TaskAssignment::create([
            'task_id'      => $taskId,
            'user_id'      => $userId,
            'is_completed' => 0,
        ]);
    }

    public function deleteAssignments(int $taskId): void
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
        $query->where('task_id', $taskId)->delete();
    }

    public function hasAnyCompleted(int $taskId): bool
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();

        return $query
            ->where('task_id', $taskId)
            ->where('is_completed', 1)
            ->exists();
    }
}