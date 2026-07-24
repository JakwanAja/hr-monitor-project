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
            'is_completed' => 'pending', 
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
            ->where('is_completed', 'completed')
            ->exists();
    }

    public function markAllPendingAsNotDone(string $today): int
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
    
        return $query
            ->whereHas('task', function ($q) use ($today) {
                // Semua task sebelum hari ini yang masih pending
                $q->whereDate('task_date', '<', $today);
            })
            ->where('is_completed', 'pending')
            ->update(['is_completed' => 'not_done']);
    }

    public function getSelfTasksToday(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->where('created_by', $userId)
            ->where('type', 'self')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function findAssignment(int $taskId, int $userId): ?TaskAssignment
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();

        return $query
            ->where('task_id', $taskId)
            ->where('user_id', $userId)
            ->first();
    }

    public function completeAssignment(TaskAssignment $assignment, ?string $note): bool
    {
        return (bool) $assignment->update([
            'is_completed' => 'completed',
            'completed_at' => now(),
            'note'         => $note,
        ]);
    }

    public function getAllTasksForUserToday(int $userId): Collection
    {
    /** @var Builder $query */
    $query = $this->model->newQuery();

    return $query
        ->with(['assignments' => function ($q) use ($userId) {
            $q->where('user_id', $userId);
        }, 'creator:id,name'])
        ->whereHas('assignments', function ($q) use ($userId) {
            $q->where('user_id', $userId);
        })
        ->whereDate('task_date', Carbon::today())
        ->orderByDesc('created_at')
        ->get();
    }

    public function getHistoryForUser(int $userId, ?string $date = null): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('task_date', $date);
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }
    public function getHistoryForAdmin(?int $userId = null, ?string $date = null): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with([
                'assignments' => function ($q) use ($userId) {
                    if ($userId) {
                        $q->where('user_id', $userId);
                    }
                },
                'assignedUsers:id,name,role',
                'creator:id,name',
            ])
            ->when($userId, function ($q) use ($userId) {
                $q->whereHas('assignments', function ($q) use ($userId) {
                    $q->where('user_id', $userId);
                });
            })
            ->when($date, function ($q) use ($date) {
                $q->whereDate('task_date', $date);
            })
            ->orderByDesc('task_date')
            ->orderByDesc('created_at')
            ->get();
    }

    public function getTasksCreatedByAdmin(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('creator', function ($q) {
                $q->where('role', 'admin');
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }
    public function getTasksByStaff(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('creator', function ($q) {
                $q->where('role', 'hr_staff');
            })
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getAllTasksForAssistant(): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->with(['creator:id,name', 'assignments', 'assignedUsers:id,name,role'])
            ->whereHas('assignments', function ($q) {
                $q->whereHas('user', function ($q) {
                    $q->where('role', 'hr_assistant');
                });
            })
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function isDefaultTaskAlreadyGenerated(int $defaultTaskId, string $date): bool
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();

        return $query
            ->where('default_task_id', $defaultTaskId)
            ->whereDate('task_date', $date)
            ->exists();
    }
    public function getUsersByRole(string $role): Collection
    {
        return \App\Models\User::query()
            ->where('role', $role)
            ->where('is_active', 1)
            ->get();
    }

    public function getDailyStats(): array
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();
    
        $total = $query->whereHas('task', function ($q) {
            $q->whereDate('task_date', Carbon::today());
        })->count();
    
        $completed = TaskAssignment::query()
            ->whereHas('task', function ($q) {
                $q->whereDate('task_date', Carbon::today());
            })
            ->where('is_completed', 'completed')
            ->count();
    
        $notDone = TaskAssignment::query()
            ->whereHas('task', function ($q) {
                $q->whereDate('task_date', Carbon::today());
            })
            ->where('is_completed', 'not_done')
            ->count();
    
        return [
            'total'     => $total,
            'completed' => $completed,
            'pending'   => $total - $completed - $notDone,
            'not_done'  => $notDone,
        ];
    }

    public function getDailyStatsPerUser(): Collection
    {
        return \App\Models\User::query()
            ->whereIn('role', ['hr_staff', 'hr_assistant'])
            ->where('is_active', 1)
            ->withCount([
                'taskAssignments as total_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    });
                },
                'taskAssignments as completed_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'completed');
                },
                'taskAssignments as not_done_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'not_done');
                },
            ])
            ->orderBy('role')
            ->orderBy('name')
            ->get();
    }

    public function getUserScore(int $userId, string $period): int
    {
        /** @var Builder $query */
        $query = TaskAssignment::query();

        $query->where('user_id', $userId)
            ->where('is_completed', 'completed');

        if ($period === 'week') {
            $query->whereHas('task', function ($q) {
                $q->whereBetween('task_date', [
                    Carbon::now()->startOfWeek(),
                    Carbon::now()->endOfWeek(),
                ]);
            });
        } elseif ($period === 'month') {
            $query->whereHas('task', function ($q) {
                $q->whereMonth('task_date', Carbon::now()->month)
                ->whereYear('task_date', Carbon::now()->year);
            });
        }
        return $query->count();
    }
    
    public function getDefaultTasksForUser(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();
    
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'default')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }
    
    public function getAssignedTasksFromAdmin(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();
    
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->whereHas('creator', function ($q) {
                $q->where('role', 'admin');
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getDefaultTasksForAssistant(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();
    
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'default')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }
    
    public function getAllAssignedTasksForAssistant(int $userId): Collection
    {
        /** @var Builder $query */
        $query = $this->model->newQuery();
    
        return $query
            ->with(['assignments' => function ($q) use ($userId) {
                $q->where('user_id', $userId);
            }, 'creator:id,name,role'])
            ->whereHas('assignments', function ($q) use ($userId) {
                $q->where('user_id', $userId);
            })
            ->where('type', 'assigned')
            ->whereDate('task_date', Carbon::today())
            ->orderByDesc('created_at')
            ->get();
    }

    public function getDailyStatsForAssistants(): Collection
    {
        return \App\Models\User::query()
            ->where('role', 'hr_assistant')
            ->where('is_active', 1)
            ->withCount([
                'taskAssignments as total_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    });
                },
                'taskAssignments as completed_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'completed');
                },
                'taskAssignments as not_done_tasks' => function ($q) {
                    $q->whereHas('task', function ($q) {
                        $q->whereDate('task_date', Carbon::today());
                    })->where('is_completed', 'not_done');
                },
            ])
            ->orderBy('name')
            ->get();
    }
}