<?php

namespace App\Http\Controllers;

use App\Enums\TaskStatus;
use App\Models\Task;
use App\Models\Project;
use App\Models\User;
use App\Notifications\ContentReadyForApproval;
use App\Notifications\TaskAssigned;
use App\Notifications\TaskStatusChanged;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Enum;

class TaskController extends Controller
{
    public function index(Request $request)
    {
        $user  = auth()->user();
        $query = Task::with(['project', 'assignees']);

        if (!$user->hasAnyRole(['super-admin', 'project-manager'])) {
            $query->whereHas('assignees', fn($q) => $q->whereKey($user->id));
        }

        if ($request->search) {
            $query->where('name', 'like', "%{$request->search}%");
        }
        if ($request->status) {
            $query->where('status', $request->status);
        }
        if ($request->priority) {
            $query->where('priority', $request->priority);
        }
        if ($request->project_id) {
            $query->where('project_id', $request->project_id);
        }
        if ($request->assigned_to && $user->hasAnyRole(['super-admin', 'project-manager'])) {
            $query->whereHas('assignees', fn($q) => $q->whereKey($request->assigned_to));
        }

        $tasks    = $query->latest()->paginate(20);
        $projects = Project::orderBy('name')->get();
        $members  = User::with('roles')->whereDoesntHave('roles', fn($q) => $q->where('name', 'client'))->get();
        return view('tasks.index', compact('tasks', 'projects', 'members'));
    }

    public function create(Request $request)
    {
        $projects         = Project::orderBy('name')->get();
        $members          = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'client'))->orderBy('name')->get();
        $selected_project = $request->project_id ? Project::find($request->project_id) : null;
        return view('tasks.create', compact('projects', 'members', 'selected_project'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => 'required|string|max:255',
            'description'  => 'nullable|string',
            'status'       => ['required', new Enum(TaskStatus::class)],
            'priority'     => 'required|in:low,medium,high,urgent',
            'due_date'     => 'nullable|date',
            'project_id'   => 'nullable|exists:projects,id',
            'assignees'    => 'nullable|array',
            'assignees.*'  => 'exists:users,id',
        ]);

        $assigneeIds = $data['assignees'] ?? [];
        unset($data['assignees']);

        // Only admins and project-managers may assign to someone else
        if (!auth()->user()->hasAnyRole(['super-admin', 'project-manager'])) {
            $assigneeIds = [auth()->id()];
        }

        $task = Task::create($data);
        $task->assignees()->sync($assigneeIds);

        foreach ($task->assignees as $assignee) {
            if ($assignee->id === auth()->id()) {
                continue;
            }
            try {
                $assignee->notify(new TaskAssigned($task, auth()->user()));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        if ($task->project_id) {
            return redirect()->route('projects.show', $task->project)->with('success', 'Task created successfully.');
        }
        return redirect()->route('tasks.index')->with('success', 'Task created successfully.');
    }

    public function show(Task $task)
    {
        $this->authorizeTaskAccess($task);
        $task->load(['project.client', 'assignees.roles', 'comments.author.roles', 'comments.reactions', 'comments.media']);
        return view('tasks.show', compact('task'));
    }

    public function edit(Task $task)
    {
        $this->authorizeTaskAccess($task);
        $task->load('assignees');
        $projects = Project::orderBy('name')->get();
        $members  = User::whereDoesntHave('roles', fn($q) => $q->where('name', 'client'))->orderBy('name')->get();
        return view('tasks.edit', compact('task', 'projects', 'members'));
    }

    private function authorizeTaskAccess(Task $task): void
    {
        $user = auth()->user();
        abort_unless(
            $user->hasAnyRole(['super-admin', 'project-manager']) || $task->isAssignedTo($user),
            403
        );
    }

    public function update(Request $request, Task $task)
    {
        $this->authorizeTaskAccess($task);

        $data = $request->validate([
            'name'        => 'required|string|max:255',
            'description' => 'nullable|string',
            'status'      => ['required', new Enum(TaskStatus::class)],
            'priority'    => 'required|in:low,medium,high,urgent',
            'due_date'    => 'nullable|date',
            'project_id'  => 'nullable|exists:projects,id',
            'assignees'    => 'nullable|array',
            'assignees.*'  => 'exists:users,id',
        ]);

        $newAssigneeIds = $data['assignees'] ?? [];
        unset($data['assignees']);

        $oldAssigneeIds = $task->assignees->pluck('id')->all();
        $oldStatus      = $task->status;

        $task->update($data);

        // Only admins and project-managers may reassign
        if (auth()->user()->hasAnyRole(['super-admin', 'project-manager'])) {
            $task->assignees()->sync($newAssigneeIds);
        }

        $task->refresh()->load('assignees', 'project');

        $actor = auth()->user();

        // Notify newly-added assignees
        $addedIds = array_diff($task->assignees->pluck('id')->all(), $oldAssigneeIds);
        foreach ($task->assignees->whereIn('id', $addedIds) as $assignee) {
            if ($assignee->id === $actor->id) {
                continue;
            }
            try {
                $assignee->notify(new TaskAssigned($task, $actor));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        // Notify assignees + all admins/PMs when status changes
        if ($task->status !== $oldStatus) {
            $toNotify = $task->assignees->where('id', '!=', $actor->id);

            // Every admin and project-manager (excluding actor and assignees already queued)
            $managers = User::whereHas('roles', fn($q) => $q->whereIn('name', ['super-admin', 'project-manager']))
                ->where('id', '!=', $actor->id)
                ->whereNotIn('id', $toNotify->pluck('id')->all())
                ->get();

            foreach ($toNotify->merge($managers) as $recipient) {
                try {
                    $recipient->notify(new TaskStatusChanged($task, $oldStatus, $task->status, $actor));
                } catch (\Throwable $e) {
                    report($e);
                }
            }
        }

        return redirect()->route('tasks.show', $task)->with('success', 'Task updated.');
    }

    public function destroy(Task $task)
    {
        abort_unless(auth()->user()->hasAnyRole(['super-admin', 'project-manager']), 403);
        $task->delete();
        return redirect()->route('tasks.index')->with('success', 'Task deleted.');
    }

    public function shareWithClient(Task $task)
    {
        abort_unless(auth()->user()->hasAnyRole(['super-admin', 'project-manager']), 403);

        $task->load('project.client.users');
        $client = $task->project?->client;

        if (!$client) {
            return back()->with('error', 'This task isn\'t linked to a client project.');
        }

        $recipients = $client->users;
        if ($recipients->isEmpty()) {
            return back()->with('error', 'This client has no portal login yet — invite a client contact first.');
        }

        $task->update(['shared_with_client_at' => now()]);

        $sharedBy = auth()->user();
        foreach ($recipients as $recipient) {
            try {
                $recipient->notify(new ContentReadyForApproval($task, $sharedBy));
            } catch (\Throwable $e) {
                report($e);
            }
        }

        return back()->with('success', 'Shared with ' . $recipients->pluck('name')->implode(', ') . ' for approval.');
    }
}
