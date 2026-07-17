<?php

namespace App\Services;

use App\Models\Task;
use App\Models\User;
use App\Notifications\TaskAssigned;
use App\Repositories\TaskRepository;
use App\Repositories\UserRepository;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use Illuminate\Validation\ValidationException;

class TaskService
{
    public function __construct(
        protected TaskRepository $taskRepository,
        protected UserRepository $userRepository,
    ) {}

    // ── Getter ───────────────────────────────────────────

    public function getAllForAdmin(): Collection
    {
        return $this->taskRepository->getAllForAdmin();
    }

    public function getAssignedTasksForStaff(int $staffId): Collection
    {
        return $this->taskRepository->getAssignedTasksForStaff($staffId);
    }

    public function getAssignableUsers(): Collection
    {
        $role = Auth::user()->role;

        /** @var Builder $query */
        $query = User::query();

        if ($role === 'admin') {
            return $query
                ->whereIn('role', ['hr_staff', 'hr_assistant'])
                ->where('is_active', 1)
                ->orderBy('role')
                ->orderBy('name')
                ->get();
        }

        return $query
            ->where('role', 'hr_assistant')
            ->where('is_active', 1)
            ->orderBy('name')
            ->get();
    }

    // ── Create ───────────────────────────────────────────

    public function createAssignedTask(array $data): Task
    {
        $this->validateAssignees($data['user_ids']);

        $task = $this->taskRepository->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'task_date'   => Carbon::today(),
            'type'        => 'assigned',
            'created_by'  => Auth::id(),
        ]);

        $this->attachAssigneesAndNotify($task, $data['user_ids']);

        return $task;
    }

    // ── Update ───────────────────────────────────────────

    public function updateTask(Task $task, array $data): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat diedit karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        $this->validateAssignees($data['user_ids']);

        $updated = $this->taskRepository->update($task, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
        ]);

        $this->taskRepository->deleteAssignments($task->id);
        $this->attachAssigneesAndNotify($task, $data['user_ids']);

        return $updated;
    }

    // ── Delete ───────────────────────────────────────────

    public function deleteTask(Task $task): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        return $this->taskRepository->delete($task);
    }

    // ── Private Helpers ──────────────────────────────────

    private function validateAssignees(array $userIds): void
    {
        if (empty($userIds)) {
            throw ValidationException::withMessages([
                'user_ids' => 'Pilih minimal satu penerima tugas.',
            ]);
        }

        $role = Auth::user()->role;

        $allowedRoles = $role === 'admin'
            ? ['hr_staff', 'hr_assistant']
            : ['hr_assistant'];

        /** @var Builder $query */
        $query = User::query();

        $invalid = $query
            ->whereIn('id', $userIds)
            ->whereNotIn('role', $allowedRoles)
            ->exists();

        if ($invalid) {
            throw ValidationException::withMessages([
                'user_ids' => 'Terdapat penerima yang tidak sesuai dengan hierarki role.',
            ]);
        }
    }

    private function attachAssigneesAndNotify(Task $task, array $userIds): void
    {
        $assignerName = Auth::user()->name;

        foreach ($userIds as $userId) {
            $this->taskRepository->createAssignment($task->id, (int) $userId);

            $user = $this->userRepository->findById((int) $userId);
            if ($user) {
                $user->notify(new TaskAssigned($task, $assignerName));
            }
        }
    }
}