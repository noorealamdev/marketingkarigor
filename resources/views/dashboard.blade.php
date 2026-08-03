@extends('layouts.app')
@section('title', 'Dashboard')
@section('breadcrumb', 'Dashboard')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Good to see you, {{ Auth::user()->name }} 👋</h2>
        <p>Here's what's happening in your workspace today.</p>
    </div>
    <div class="row">
        <a href="{{ route('clients.create') }}" class="btn btn-secondary btn-sm">Add Client</a>
        <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">New Project</a>
    </div>
</div>

{{-- Stats Row --}}
<div class="g4 mb6">
    <div class="stat-card">
        <div class="stat-label">Total Clients</div>
        <div class="stat-value">{{ $stats['total_clients'] }}</div>
        <div class="stat-sub"><a href="{{ route('clients.index') }}" class="link">View all →</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Active Projects</div>
        <div class="stat-value">{{ $stats['active_projects'] }}</div>
        <div class="stat-sub"><a href="{{ route('projects.index') }}?status=active" class="link">View all →</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Pending Tasks</div>
        <div class="stat-value">{{ $stats['pending_tasks'] }}</div>
        <div class="stat-sub"><a href="{{ route('tasks.index') }}" class="link">View all →</a></div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Revenue</div>
        <div class="stat-value">{!! format_currency($stats['total_revenue']) !!}</div>
        <div class="stat-sub"><a href="{{ route('invoices.index') }}?status=paid" class="link">Paid invoices →</a></div>
    </div>
</div>

<div class="g2 mb6">
    {{-- Recent Projects --}}
    <div class="card">
        <div class="card-hd">
            <h3>Recent Projects</h3>
            <a href="{{ route('projects.index') }}" class="btn btn-secondary btn-xs">View All</a>
        </div>
        <div style="padding:0;">
            @forelse($recent_projects as $project)
            <div style="padding:14px 18px; border-bottom:1px solid #1a1e28;">
                <div class="row between mb3">
                    <a href="{{ route('projects.show', $project) }}" class="fw600" style="font-size:0.875rem;">{{ $project->name }}</a>
                    <span class="badge badge-{{ $project->status }}">{{ str_replace('_',' ',$project->status) }}</span>
                </div>
                @if($project->client)
                <div class="text-xs text-muted mb3">{{ $project->client->company ?? $project->client->name }}</div>
                @endif
                <div class="row between" style="font-size:0.76rem;color:#4a5068;">
                    @if($project->deadline)
                    <span>Due {{ $project->deadline->format('M d, Y') }}</span>
                    @else
                    <span>No deadline</span>
                    @endif
                    <span class="badge badge-{{ $project->priority }}">{{ $project->priority }}</span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No projects yet</p>
                <a href="{{ route('projects.create') }}" class="btn btn-primary btn-sm">Create Project</a>
            </div>
            @endforelse
        </div>
    </div>

    {{-- Upcoming Tasks --}}
    <div class="card">
        <div class="card-hd">
            <h3>Upcoming Tasks</h3>
            <a href="{{ route('tasks.index') }}" class="btn btn-secondary btn-xs">View All</a>
        </div>
        <div style="padding:0;">
            @forelse($upcoming_tasks as $task)
            <div style="padding:14px 18px; border-bottom:1px solid #1a1e28;">
                <div class="row between mb3">
                    <a href="{{ route('tasks.show', $task) }}" class="fw600" style="font-size:0.875rem;">{{ $task->name }}</a>
                    <span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span>
                </div>
                <div class="row between" style="font-size:0.76rem;color:#4a5068;">
                    @if($task->due_date)
                    <span style="{{ $task->due_date->isPast() ? 'color:#f87171' : '' }}">
                        {{ $task->due_date->isPast() ? 'Overdue: ' : 'Due: ' }}{{ $task->due_date->format('M d') }}
                    </span>
                    @else
                    <span>No due date</span>
                    @endif
                    <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
                </div>
            </div>
            @empty
            <div class="empty-state">
                <p>No upcoming tasks</p>
                <a href="{{ route('tasks.create') }}" class="btn btn-primary btn-sm">Add Task</a>
            </div>
            @endforelse
        </div>
    </div>
</div>

{{-- Project Status Breakdown --}}
@if($project_statuses->count())
<div class="card">
    <div class="card-hd"><h3>Project Status Breakdown</h3></div>
    <div class="card-bd">
        <div style="display:flex;gap:20px;flex-wrap:wrap;">
            @foreach(['active','on_hold','completed','cancelled'] as $s)
            @php $count = $project_statuses->get($s, 0); @endphp
            <div style="display:flex;align-items:center;gap:8px;">
                <span class="badge badge-{{ $s }}">{{ str_replace('_',' ',$s) }}</span>
                <span class="fw700" style="font-size:1rem;">{{ $count }}</span>
                <span class="text-xs text-muted">{{ $count == 1 ? 'project' : 'projects' }}</span>
            </div>
            @endforeach
        </div>
    </div>
</div>
@endif
@endsection
