<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Services\TaskService;

class DashboardController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function index()
    {
        $stats    = $this->taskService->getDailyStats();
        $perUser  = $this->taskService->getDailyStatsPerUser();
        $rankings = $this->taskService->getTopRankings('week');

        return view('admin.dashboard', compact('stats', 'perUser', 'rankings'));
    }
}