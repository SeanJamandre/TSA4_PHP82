<?php
namespace App\Http\Controllers;
use App\Http\Requests\SaveTaskRequest;
use App\Mail\TaskCompleted;
use App\Models\Task;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
class TaskController extends Controller {
    public function index(): View {
        return view('tasks.index', ['tasks' => Task::active()->orderBy('due_date')->orderBy('id')->paginate(10)]);
    }
    public function create(): View { return view('tasks.create', ['task' => new Task(['status'=>'pending','priority'=>'medium'])]); }
    public function store(SaveTaskRequest $request): RedirectResponse {
        Task::create($request->validated());
        return to_route('tasks.index')->with('success', 'Task created.');
    }
    public function show(Task $task): View { abort_if($task->is_archived, 404); return view('tasks.show', compact('task')); }
    public function edit(Task $task): View { abort_if($task->is_archived, 404); return view('tasks.edit', compact('task')); }
    public function update(SaveTaskRequest $request, Task $task): RedirectResponse {
        DB::transaction(function () use ($request, $task) {
            $current = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();
            abort_if($current->is_archived, 404);
            $before = $current->status;
            $current->fill($request->validated());
            $current->save();
            if ($before !== 'done' && $current->status === 'done') {
                Mail::to(config('tasks.notification_recipient'))->send(new TaskCompleted($current));
            }
        });
        return to_route('tasks.index')->with('success', 'Task updated.');
    }
    public function destroy(Task $task): RedirectResponse {
        DB::transaction(function () use ($task) {
            $current = Task::whereKey($task->id)->lockForUpdate()->firstOrFail();
            abort_if($current->is_archived, 404);
            $current->is_archived = true; $current->save();
        });
        return to_route('tasks.index')->with('success', 'Task archived.');
    }
}
