<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TaskService;
use App\Services\UserService;
use Illuminate\Http\Request;

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

    public function productivity()
    {
        return 'Coming soon...';
    }

    public function ranking()
    {
        return 'Coming soon...';
    }
}