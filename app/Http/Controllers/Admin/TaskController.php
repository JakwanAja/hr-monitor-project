<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    // ── Halaman 1: Tugas dari Admin ──────────────────────

    public function index()
    {
        $tasks           = $this->taskService->getTasksCreatedByAdmin();
        $assignableUsers = $this->taskService->getAssignableUsers();
        return view('admin.tasks.index', compact('tasks', 'assignableUsers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'user_ids'    => ['required', 'array', 'min:1'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->createAssignedTask($validated);
            return redirect()->route('admin.tasks.index')
                ->with('success', 'Tugas berhasil dibuat dan dikirim ke penerima.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'user_ids'    => ['required', 'array', 'min:1'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->updateTask($task, $validated);
            return redirect()->route('admin.tasks.index')
                ->with('success', 'Tugas berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }

    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteTask($task);
            return redirect()->route('admin.tasks.index')
                ->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }

    // ── Halaman 2: Tugas HR Staff ────────────────────────

    public function staffTasks()
    {
        $tasks = $this->taskService->getTasksByStaff();
        return view('admin.tasks.staff', compact('tasks'));
    }

    // ── Halaman 3: Tugas HR Assistant ────────────────────

    public function assistantTasks()
    {
        $tasks = $this->taskService->getAllTasksForAssistant();
        return view('admin.tasks.assistant', compact('tasks'));
    }

    // ── Force Destroy (Admin hapus task siapapun) ────────

    public function forceDestroy(Task $task)
    {
        $redirect = url()->previous();

        try {
            $this->taskService->forceDeleteTask($task);
            return redirect($redirect)->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return redirect($redirect)->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }
}