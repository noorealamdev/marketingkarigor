@extends('layouts.app')
@section('title', 'Tasks')
@section('breadcrumb', 'Tasks')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ auth()->user()->hasAnyRole(['super-admin', 'project-manager']) ? 'All Tasks' : 'My Tasks' }}</h2>
        <p>{{ $tasks->total() }} {{ $tasks->total() === 1 ? 'task' : 'tasks' }}</p>
    </div>
    <a href="{{ route('tasks.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Task
    </a>
</div>

<form method="GET" class="filter-row mb-4">
    <input type="text" name="search" class="form-control" placeholder="Search tasks…" value="{{ request('search') }}">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>
        @foreach(\App\Enums\TaskStatus::cases() as $status)
        <option value="{{ $status->value }}" {{ request('status')==$status->value?'selected':'' }}>{{ $status->label() }}</option>
        @endforeach
    </select>
    <select name="priority" class="form-control">
        <option value="">All Priorities</option>
        <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
        <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
        <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
        <option value="urgent" {{ request('priority')=='urgent'?'selected':'' }}>Urgent</option>
    </select>
    <select name="project_id" class="form-control">
        <option value="">All Projects</option>
        @foreach($projects as $project)
        <option value="{{ $project->id }}" {{ request('project_id')==$project->id?'selected':'' }}>{{ $project->name }}</option>
        @endforeach
    </select>
    @if(auth()->user()->hasAnyRole(['super-admin', 'project-manager']))
    <select name="assigned_to" class="form-control">
        <option value="">All Members</option>
        @foreach($members as $m)
        <option value="{{ $m->id }}" {{ request('assigned_to')==$m->id?'selected':'' }}>{{ $m->name }}</option>
        @endforeach
    </select>
    @endif
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request()->hasAny(['search','status','priority','project_id','assigned_to']))
        <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-sm">Clear</a>
    @endif
</form>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Task</th>
                <th>Assignee</th>
                <th>Project</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Due Date</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($tasks as $task)
            <tr>
                <td class="td-header">
                    <a href="{{ route('tasks.show', $task) }}" class="fw600 link">{{ $task->name }}</a>
                    @if($task->description)
                        <div class="text-xs text-muted" style="margin-top:2px;">{{ Str::limit($task->description,55) }}</div>
                    @endif
                    @if($task->comments_count ?? $task->comments->count())
                    <div style="margin-top:3px;font-size:0.7rem;color:#4a5068;">
                        <svg width="10" height="10" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;"><path d="M21 15a2 2 0 01-2 2H7l-4 4V5a2 2 0 012-2h14a2 2 0 012 2z"/></svg>
                        {{ $task->comments->count() }}
                    </div>
                    @endif
                </td>
                <td data-label="Assignee">
                    @if($task->assignees->isNotEmpty())
                    <div style="display:flex;flex-direction:column;gap:4px;">
                        @foreach($task->assignees as $assignee)
                        <div style="display:flex;align-items:center;gap:6px;">
                            {!! user_avatar($assignee, 26) !!}
                            <span style="font-size:0.82rem;color:#c8cce0;">{{ $assignee->name }}</span>
                        </div>
                        @endforeach
                    </div>
                    @else
                    <span class="text-faint text-xs">Unassigned</span>
                    @endif
                </td>
                <td data-label="Project">
                    @if($task->project)
                        <a href="{{ route('projects.show', $task->project) }}" class="link text-sm">{{ $task->project->name }}</a>
                    @else
                        <span class="text-muted">—</span>
                    @endif
                </td>
                <td data-label="Status"><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span>
                    @if(auth()->user()->isAdmin() && ($task->payment_amount > 0 || $task->payments->isNotEmpty()))
                        @php $paid = $task->payments->sum('amount'); $fee = (float) $task->payment_amount; @endphp
                        <div class="text-xs" style="margin-top:4px;color:{{ $paid > 0 && ($fee <= 0 || $paid >= $fee) ? '#4ade80' : ($task->status === 'done' ? '#fbbf24' : '#6b7590') }};">
                            {{ $paid > 0 && ($fee <= 0 || $paid >= $fee) ? 'Paid' : ($paid > 0 ? 'Part-paid' : ($task->status === 'done' ? 'Payment due' : 'Fee')) }}
                            @if($fee > 0) &middot; {!! format_currency($fee) !!} @endif
                        </div>
                    @endif
                </td>
                <td data-label="Priority"><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                <td data-label="Due Date" style="{{ $task->due_date?->isPast() && $task->status!=\App\Enums\TaskStatus::Done->value ? 'color:#f87171' : 'color:#6b7590' }}">
                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                </td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary btn-xs">View</a>
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary btn-xs">Edit</a>
                        @if(auth()->user()->hasAnyRole(['super-admin', 'project-manager']))
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete task?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Del</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="td-empty">
                <div class="empty-state">
                    <p>No tasks found.</p>
                    <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">Add Task</a>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($tasks->hasPages())
<div class="pager">{{ $tasks->links() }}</div>
@endif
@endsection
