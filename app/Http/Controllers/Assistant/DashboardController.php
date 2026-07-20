<?php

namespace App\Http\Controllers\Assistant;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index()
    {
        $userId = Auth::id();
        $tasks  = $this->taskService->getAllTasksForUserToday($userId);

        $stats = [
            'total'     => $tasks->count(),
            'completed' => $tasks->filter(fn($t) => $t->assignments->first()?->is_completed)->count(),
            'pending'   => $tasks->filter(fn($t) => !$t->assignments->first()?->is_completed)->count(),
        ];

        $scoreWeek  = $this->taskService->getUserScore($userId, 'week');
        $scoreMonth = $this->taskService->getUserScore($userId, 'month');

        return view('assistant.dashboard', compact(
            'tasks', 'stats', 'scoreWeek', 'scoreMonth'
        ));
    }
}