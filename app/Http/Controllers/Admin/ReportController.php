<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class ReportController extends Controller
{
    public function __construct(
        protected TaskService $taskService,
        protected UserService $userService,
    ) {}

    public function history(Request $request)
    {
        $date   = $request->query('date');
        $userId = $request->query('user_id');
        $users  = $this->userService->getAllUsers();
        $tasks  = $this->taskService->getHistoryForAdmin(
            $userId ? (int) $userId : null,
            $date
        );
        return view('admin.reports.history', compact('tasks', 'users', 'date', 'userId'));
    }

    public function productivity(Request $request)
    {
        $date  = $request->query('date', Carbon::today()->toDateString());
        $users = $this->userService->getAllUsers();
    
        $report = $users->map(function ($user) use ($date) {
            $assignments = \App\Models\TaskAssignment::query()
                ->where('user_id', $user->id)
                ->whereHas('task', function ($q) use ($date) {
                    $q->whereDate('task_date', $date);
                })
                ->with('task:id,title,type')
                ->get();
    
            return [
                'user'      => $user,
                'total'     => $assignments->count(),
                'completed' => $assignments->where('is_completed', 1)->count(),
                'pending'   => $assignments->where('is_completed', 0)->count(),
                'tasks'     => $assignments,
            ];
        });
    
        return view('admin.reports.productivity', compact('report', 'users', 'date'));
    }

    public function ranking(Request $request)
    {
        $period = $request->query('period', 'week');
        $users  = $this->userService->getAllUsers();
    
        $rankings = $users->map(function ($user) use ($period) {
            return [
                'user'  => $user,
                'score' => $this->taskService->getUserScore($user->id, $period),
            ];
        })
        ->sortByDesc('score')
        ->values();
    
        return view('admin.reports.ranking', compact('rankings', 'period'));
    }
}