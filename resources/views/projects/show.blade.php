@extends('layouts.app')
@section('title', $project->name)
@section('breadcrumb', 'Projects / ' . $project->name)

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $project->name }}</h2>
        <div class="row center" style="gap:8px;margin-top:4px;">
            <span class="badge badge-{{ $project->status }}">{{ str_replace('_',' ',$project->status) }}</span>
            <span class="badge badge-{{ $project->priority }}">{{ $project->priority }} priority</span>
            @if($project->client)
                <span class="text-sm text-muted">for <a href="{{ route('clients.show', $project->client) }}" class="link">{{ $project->client->company ?? $project->client->name }}</a></span>
            @endif
        </div>
    </div>
    <div class="row" style="gap:6px;">
        <a href="{{ route('projects.media', $project) }}" class="btn btn-secondary btn-sm">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
            Media
        </a>
        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-secondary btn-sm">Add Task</a>
        <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary">Edit</a>
        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="g2 mb6">
    <div class="card">
        <div class="card-hd"><h3>Project Details</h3></div>
        <div class="card-bd">
            @if($project->description)
                <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;margin-bottom:16px;" class="desc-field">{!! format_description($project->description) !!}</p>
            @endif
            <div class="detail-row">
                <span class="detail-label">Start Date</span>
                <span class="detail-value">{{ $project->start_date?->format('M d, Y') ?? '—' }}</span>
                <span class="detail-label">Deadline</span>
                <span class="detail-value">{{ $project->deadline?->format('M d, Y') ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-hd"><h3>Progress</h3></div>
        <div class="card-bd">
            @php
                $total = $project->tasks->count();
                $done = $project->tasks->where('status', \App\Enums\TaskStatus::Done->value)->count();
                $inProgress = $project->tasks->whereNotIn('status', [\App\Enums\TaskStatus::Todo->value, \App\Enums\TaskStatus::Done->value])->count();
                $progress = $total > 0 ? round($done/$total*100) : 0;
            @endphp
            <div style="text-align:center;padding:10px 0;">
                <div style="font-size:2.5rem;font-weight:800;color:#6c63ff;letter-spacing:-0.04em;">{{ $progress }}%</div>
                <div class="text-sm text-muted" style="margin-top:4px;">Complete</div>
            </div>
            <div class="progress-bar" style="margin:12px 0;">
                <div class="progress-fill" style="width:{{ $progress }}%"></div>
            </div>
            <div class="row" style="justify-content:space-around;margin-top:10px;">
                <div style="text-align:center;">
                    <div class="fw700" style="font-size:1.1rem;">{{ $total }}</div>
                    <div class="text-xs text-muted">Total</div>
                </div>
                <div style="text-align:center;">
                    <div class="fw700" style="font-size:1.1rem;color:#4ade80;">{{ $done }}</div>
                    <div class="text-xs text-muted">Done</div>
                </div>
                <div style="text-align:center;">
                    <div class="fw700" style="font-size:1.1rem;color:#60a5fa;">{{ $inProgress }}</div>
                    <div class="text-xs text-muted">In Progress</div>
                </div>
                <div style="text-align:center;">
                    <div class="fw700" style="font-size:1.1rem;color:#9ca3af;">{{ $project->tasks->where('status', \App\Enums\TaskStatus::Todo->value)->count() }}</div>
                    <div class="text-xs text-muted">Todo</div>
                </div>
            </div>

            {{-- Media Library count --}}
            <div style="margin-top:16px;padding-top:14px;border-top:1px solid #252936;display:flex;align-items:center;justify-content:space-between;">
                <span class="text-xs text-muted">Media Library</span>
                <a href="{{ route('projects.media', $project) }}" class="btn btn-secondary btn-xs">
                    {{ $project->getMedia('*')->count() }} files →
                </a>
            </div>
        </div>
    </div>
</div>

{{-- Tasks --}}
<div class="card mb6">
    <div class="card-hd">
        <h3>Tasks ({{ $project->tasks->count() }})</h3>
        <a href="{{ route('tasks.create') }}?project_id={{ $project->id }}" class="btn btn-primary btn-xs">Add Task</a>
    </div>
    @if($project->tasks->isEmpty())
    <div class="empty-state"><p>No tasks yet. Add tasks to track work for this project.</p></div>
    @else
    <table class="table">
        <thead><tr><th>Task</th><th>Assignee</th><th>Status</th><th>Priority</th><th>Due Date</th><th>Actions</th></tr></thead>
        <tbody>
            @foreach($project->tasks->sortBy('status') as $task)
            <tr>
                <td class="td-header">
                    <a href="{{ route('tasks.show', $task) }}" class="fw600 link">{{ $task->name }}</a>
                    @if($task->description)
                    <div class="text-xs text-muted" style="margin-top:2px;">{{ Str::limit($task->description,60) }}</div>
                    @endif
                </td>
                <td data-label="Assignee">
                    @if($task->assignees->isNotEmpty())
                        <div style="display:flex;flex-direction:column;gap:4px;">
                            @foreach($task->assignees as $assignee)
                            <div class="row center" style="gap:6px;">
                                {!! user_avatar($assignee, 22) !!}
                                <span class="text-xs">{{ $assignee->name }}</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <span class="text-faint text-xs">—</span>
                    @endif
                </td>
                <td data-label="Status"><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span></td>
                <td data-label="Priority"><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></td>
                <td class="text-muted" data-label="Due Date" style="{{ $task->due_date?->isPast() && $task->status!=\App\Enums\TaskStatus::Done->value ? 'color:#f87171' : '' }}">
                    {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                </td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary btn-xs">Edit</a>
                        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete task?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Invoices — Super Admin + Project Manager --}}
@if(auth()->user()->hasAnyRole(['super-admin', 'project-manager']))
<div class="card">
    <div class="card-hd">
        <h3>Invoices ({{ $project->invoices->count() }})</h3>
        <a href="{{ route('invoices.create') }}?project_id={{ $project->id }}" class="btn btn-primary btn-xs">Add Invoice</a>
    </div>
    @if($project->invoices->isEmpty())
    <div class="empty-state"><p>No invoices linked to this project.</p></div>
    @else
    <table class="table">
        <thead><tr><th>Number</th><th>Client</th><th>Amount</th><th>Status</th><th>Due</th><th></th></tr></thead>
        <tbody>
            @foreach($project->invoices as $invoice)
            <tr>
                <td class="td-header"><a href="{{ route('invoices.show', $invoice) }}" class="fw600 link">{{ $invoice->invoice_number }}</a></td>
                <td class="text-muted" data-label="Client">{{ $invoice->client?->name ?? '—' }}</td>
                <td class="fw600" data-label="Amount">{!! format_currency($invoice->amount) !!}</td>
                <td data-label="Status"><span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span></td>
                <td class="text-muted" data-label="Due">{{ $invoice->due_date?->format('M d, Y') ?? '—' }}</td>
                <td class="td-actions"><a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary btn-xs">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endif
@endsection
