<?php

namespace App\Http\Controllers\Assistant;

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

    public function index()
    {
        $tasks = $this->taskService->getSelfTasksToday(Auth::id());
        return view('assistant.tasks.index', compact('tasks'));
    }
    public function update(Request $request, Task $task)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);
    
        try {
            $this->taskService->updateSelfTask($task, $validated);
            return redirect()->route('assistant.tasks.index')
                ->with('success', 'Tugas mandiri berhasil diperbarui.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal memperbarui tugas.');
        }
    }
    
    public function destroy(Task $task)
    {
        try {
            $this->taskService->deleteSelfTask($task);
            return redirect()->route('assistant.tasks.index')
                ->with('success', 'Tugas mandiri berhasil dihapus.');
        } catch (ValidationException $e) {
            return back()->with('error', $e->errors()['task'][0] ?? 'Gagal menghapus tugas.');
        }
    }
    public function store(Request $request)
    {
        $validated = $request->validate([
            'title'       => ['required', 'string', 'max:200'],
            'description' => ['nullable', 'string'],
        ]);

        try {
            $this->taskService->createSelfTask($validated);
            return redirect()->route('assistant.tasks.index')
                ->with('success', 'Tugas mandiri berhasil ditambahkan.');
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors())->withInput();
        }
    }

    public function history()
    {
        return 'Coming soon...';
    }

    public function complete(Request $request, Task $task)
    {
        return 'Coming soon...';
    }
}