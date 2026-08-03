@extends('layouts.app')
@section('title', 'Projects')
@section('breadcrumb', 'Projects')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Projects</h2>
        <p>{{ $projects->total() }} total projects</p>
    </div>
    <a href="{{ route('projects.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Project
    </a>
</div>

<form method="GET" class="filter-row mb4">
    <input type="text" name="search" class="form-control" placeholder="Search projects…" value="{{ request('search') }}">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
        <option value="on_hold" {{ request('status')=='on_hold'?'selected':'' }}>On Hold</option>
        <option value="completed" {{ request('status')=='completed'?'selected':'' }}>Completed</option>
        <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
    </select>
    <select name="priority" class="form-control">
        <option value="">All Priorities</option>
        <option value="low" {{ request('priority')=='low'?'selected':'' }}>Low</option>
        <option value="medium" {{ request('priority')=='medium'?'selected':'' }}>Medium</option>
        <option value="high" {{ request('priority')=='high'?'selected':'' }}>High</option>
        <option value="urgent" {{ request('priority')=='urgent'?'selected':'' }}>Urgent</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request('search') || request('status') || request('priority'))
        <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-sm">Clear</a>
    @endif
</form>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Project</th>
                <th>Client</th>
                <th>Status</th>
                <th>Priority</th>
                <th>Tasks</th>
                <th>Deadline</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($projects as $project)
            <tr>
                <td class="td-header">
                    <a href="{{ route('projects.show', $project) }}" class="fw600 link">{{ $project->name }}</a>
                    @if($project->description)
                        <div class="text-xs text-muted" style="margin-top:2px;">{{ Str::limit($project->description, 60) }}</div>
                    @endif
                </td>
                <td class="text-muted" data-label="Client">{{ $project->client?->company ?? $project->client?->name ?? '—' }}</td>
                <td data-label="Status"><span class="badge badge-{{ $project->status }}">{{ str_replace('_',' ',$project->status) }}</span></td>
                <td data-label="Priority"><span class="badge badge-{{ $project->priority }}">{{ $project->priority }}</span></td>
                <td class="text-muted" data-label="Tasks">{{ $project->tasks_count }}</td>
                <td class="text-muted" data-label="Deadline">{{ $project->deadline ? $project->deadline->format('M d, Y') : '—' }}</td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-xs">View</a>
                        <a href="{{ route('projects.edit', $project) }}" class="btn btn-secondary btn-xs">Edit</a>
                        <form method="POST" action="{{ route('projects.destroy', $project) }}" onsubmit="return confirm('Delete this project?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="td-empty">
                <div class="empty-state">
                    <p>No projects found. Create your first project to get started.</p>
                    <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">New Project</a>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($projects->hasPages())
<div class="pager">{{ $projects->links() }}</div>
@endif
@endsection
