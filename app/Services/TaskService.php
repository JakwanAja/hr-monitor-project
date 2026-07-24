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
    public function deleteTask(Task $task): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        return $this->taskRepository->delete($task);
    }

    public function createSelfTask(array $data): Task
    {
        $task = $this->taskRepository->create([
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
            'task_date'   => Carbon::today(),
            'type'        => 'self',
            'created_by'  => Auth::id(),
        ]);
        $this->taskRepository->createAssignment($task->id, Auth::id());

        return $task;
    }

    public function updateSelfTask(Task $task, array $data): bool
    {
        // Pastikan task milik sendiri
        if ($task->created_by !== Auth::id()) {
            throw ValidationException::withMessages([
                'task' => 'Anda tidak memiliki akses untuk mengedit tugas ini.',
            ]);
        }

        // Cegah edit jika sudah selesai
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat diedit karena sudah diselesaikan.',
            ]);
        }

        return $this->taskRepository->update($task, [
            'title'       => $data['title'],
            'description' => $data['description'] ?? null,
        ]);
    }
    public function deleteSelfTask(Task $task): bool
    {
        // Pastikan task milik sendiri
        if ($task->created_by !== Auth::id()) {
            throw ValidationException::withMessages([
                'task' => 'Anda tidak memiliki akses untuk menghapus tugas ini.',
            ]);
        }
        // Cegah hapus jika sudah selesai
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah diselesaikan.',
            ]);
        }

        return $this->taskRepository->delete($task);
    }

    public function getSelfTasksToday(int $userId): Collection
    {
        return $this->taskRepository->getSelfTasksToday($userId);
    }

    public function completeTask(Task $task, ?string $note): bool
    {
        $assignment = $this->taskRepository->findAssignment($task->id, Auth::id());
    
        if (! $assignment) {
            throw ValidationException::withMessages([
                'task' => 'Anda tidak memiliki akses untuk menyelesaikan tugas ini.',
            ]);
        }
    
        // Tidak bisa complete jika sudah not_done
        if ($assignment->isNotDone()) {
            throw ValidationException::withMessages([
                'task' => 'Tugas ini sudah ditandai tidak dikerjakan karena melewati batas waktu.',
            ]);
        }
    
        if ($assignment->isCompleted()) {
            throw ValidationException::withMessages([
                'task' => 'Tugas ini sudah ditandai selesai.',
            ]);
        }
        return $this->taskRepository->completeAssignment($assignment, $note);
    }

    public function markAllPendingAsNotDone(): int
    {
        $today = Carbon::today()->toDateString();
        return $this->taskRepository->markAllPendingAsNotDone($today);
    }

    public function getAllTasksForUserToday(int $userId): Collection
    {
        return $this->taskRepository->getAllTasksForUserToday($userId);
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

    public function forceDeleteTask(Task $task): bool
    {
        if ($this->taskRepository->hasAnyCompleted($task->id)) {
            throw ValidationException::withMessages([
                'task' => 'Tugas tidak dapat dihapus karena sudah ada penerima yang menyelesaikannya.',
            ]);
        }

        return $this->taskRepository->delete($task);
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
    public function getHistoryForUser(int $userId, ?string $date = null): Collection
    {
        return $this->taskRepository->getHistoryForUser($userId, $date);
    }

    public function getHistoryForAdmin(?int $userId = null, ?string $date = null): Collection
    {
        return $this->taskRepository->getHistoryForAdmin($userId, $date);
    }

    public function getTasksByStaff(): Collection
    {
        return $this->taskRepository->getTasksByStaff();
    }

    public function getTasksCreatedByAdmin(): Collection
    {
        return $this->taskRepository->getTasksCreatedByAdmin();
    }

    public function getAllTasksForAssistant(): Collection
    {
        return $this->taskRepository->getAllTasksForAssistant();
    }

    public function getDailyStats(): array
    {
        return $this->taskRepository->getDailyStats();
    }

    public function getDailyStatsPerUser(): Collection
    {
        return $this->taskRepository->getDailyStatsPerUser();
    }

    public function getUserScore(int $userId, string $period): int
    {
        return $this->taskRepository->getUserScore($userId, $period);
    }

    public function getAssistantProgressForStaff(): Collection
    {
        return $this->taskRepository->getDailyStatsPerUser()
            ->where('role', 'hr_assistant');
    }

    public function getDefaultTasksForUser(int $userId): Collection
    {
        return $this->taskRepository->getDefaultTasksForUser($userId);
    }

    public function getAssignedTasksFromAdmin(int $userId): Collection
    {
        return $this->taskRepository->getAssignedTasksFromAdmin($userId);
    }

    public function getDefaultTasksForAssistant(int $userId): Collection
    {
        return $this->taskRepository->getDefaultTasksForAssistant($userId);
    }
    
    public function getAllAssignedTasksForAssistant(int $userId): Collection
    {
        return $this->taskRepository->getAllAssignedTasksForAssistant($userId);
    }
}