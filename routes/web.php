<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboard;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DefaultTaskController;
use App\Http\Controllers\Admin\TaskController as AdminTaskController;
use App\Http\Controllers\Admin\ReportController;
use App\Http\Controllers\Staff\DashboardController as StaffDashboard;
use App\Http\Controllers\Staff\TaskController as StaffTaskController;
use App\Http\Controllers\Assistant\DashboardController as AssistantDashboard;
use App\Http\Controllers\Assistant\TaskController as AssistantTaskController;
use App\Http\Controllers\NotificationController;

// ── Auth (All Roles) ────────────────────────────────────────────────────────
Route::get('/', fn() => redirect()->route('login'));
Route::get('/login', [LoginController::class, 'showForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.post');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

// ── ADMIN ────────────────────────────────────────────────────────────────
Route::prefix('admin')->name('admin.')->middleware(['auth', 'role:admin'])->group(function () {
    Route::get('/dashboard', [AdminDashboard::class, 'index'])->name('dashboard');
    Route::resource('users', UserController::class) ->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/users/{user}/password', [UserController::class, 'updatePassword'])->name('users.password');
    Route::resource('default-tasks', DefaultTaskController::class) ->only(['index', 'store', 'update', 'destroy']);
    Route::resource('tasks', AdminTaskController::class) ->only(['index', 'store', 'update', 'destroy']);
    Route::prefix('reports')->name('reports.')->group(function () {
        Route::get('/productivity', [ReportController::class, 'productivity'])->name('productivity');
        Route::get('/history', [ReportController::class, 'history'])->name('history');
        Route::get('/ranking', [ReportController::class, 'ranking'])->name('ranking');
    });
});

// ── HR Staff ────────────────────────────────────────────────────────────────
Route::prefix('staff')->name('staff.')->middleware(['auth', 'role:hr_staff'])->group(function () {
    Route::get('/dashboard', [StaffDashboard::class, 'index'])->name('dashboard');
    Route::resource('tasks', StaffTaskController::class) ->only(['index', 'store', 'update', 'destroy']);
    Route::patch('/tasks/{task}/complete', [StaffTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('/history', [StaffTaskController::class, 'history'])->name('tasks.history');
    Route::get('/assign-tasks', [StaffTaskController::class, 'assignIndex'])->name('assign.index');    
    Route::post('/assign-tasks', [StaffTaskController::class, 'assignStore'])->name('assign.store');
    Route::patch('/assign-tasks/{task}', [StaffTaskController::class, 'assignUpdate'])->name('assign.update');
    Route::delete('/assign-tasks/{task}', [StaffTaskController::class, 'assignDestroy'])->name('assign.destroy');
    Route::get('/assistant-progress', [StaffTaskController::class, 'assistantProgress'])->name('assistant-progress');
});

// ── HR Assistant ─────────────────────────────────────────────────────────────
Route::prefix('assistant')->name('assistant.')->middleware(['auth', 'role:hr_assistant'])->group(function () {
    Route::get('/dashboard', [AssistantDashboard::class, 'index'])->name('dashboard');
    Route::resource('tasks', AssistantTaskController::class) ->only(['index', 'store']);
    Route::patch('/tasks/{task}/complete', [AssistantTaskController::class, 'complete'])->name('tasks.complete');
    Route::get('/history', [AssistantTaskController::class, 'history'])->name('tasks.history');
});

// ── Notifications (All Roles) ────────────────────────────────────────────────
Route::middleware('auth')->prefix('notifications')->name('notifications.')->group(function () {
    Route::get('/', [NotificationController::class, 'index'])->name('index');
    Route::patch('/{id}/read', [NotificationController::class, 'markAsRead'])->name('read');
    Route::patch('/read-all', [NotificationController::class, 'markAllAsRead'])->name('read.all');
});