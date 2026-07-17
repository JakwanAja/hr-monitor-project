<?php

namespace App\Http\Controllers\Staff;

use App\Http\Controllers\Controller;
use App\Models\Task;
use App\Services\TaskService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class TaskController extends Controller
{
    public function __construct(
        protected TaskService $taskService
    ) {}

    public function assignIndex()
    {
        $tasks           = $this->taskService->getAssignedTasksForStaff(Auth::id());
        $assignableUsers = $this->taskService->getAssignableUsers();
        return view('staff.assign-tasks.index', compact('tasks', 'assignableUsers'));
    }

    public function assignStore(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'user_ids'    => ['required', 'array', 'min:1'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->createAssignedTask($validated);
            return redirect()->route('staff.assign.index')
                ->with('success', 'Tugas berhasil dikirim ke HR Assistant.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function assignUpdate(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
            'user_ids'    => ['required', 'array', 'min:1'],
            'user_ids.*'  => ['integer', 'exists:users,id'],
        ]);

        try {
            $this->taskService->updateTask($task, $validated);
            return redirect()->route('staff.assign.index')
                ->with('success', 'Tugas berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }

    public function assignDestroy(Task $task)
    {
        try {
            $this->taskService->deleteTask($task);
            return redirect()->route('staff.assign.index')
                ->with('success', 'Tugas berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }

    // Stub — akan diisi di Phase 3.3 & 3.4
    public function index() { return 'Coming soon...'; }
    public function store(Request $request) { return 'Coming soon...'; }
    public function update(Request $request, Task $task) { return 'Coming soon...'; }
    public function destroy(Task $task) { return 'Coming soon...'; }
    public function complete(Request $request, Task $task) { return 'Coming soon...'; }
    public function history() { return 'Coming soon...'; }
    public function assistantProgress() { return 'Coming soon...'; }
}