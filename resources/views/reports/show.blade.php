@extends('layouts.app')
@section('title', $report->client->company ?: $report->client->name)
@section('breadcrumb', 'Reports / ' . ($report->client->company ?: $report->client->name))

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $report->client->company ?: $report->client->name }}</h2>
        <div class="row center" style="gap:8px;margin-top:4px;">
            <span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $report->periodEnum()->label() }}</span>
            <span class="text-sm text-muted">{{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}</span>
            @if($report->isSent())
                <span class="badge badge-done">Sent {{ $report->sent_at->format('M d, Y') }}</span>
            @else
                <span class="badge badge-todo">Draft</span>
            @endif
        </div>
    </div>
    <div class="row" style="gap:6px;">
        <form method="POST" action="{{ route('reports.send', $report) }}" onsubmit="return confirm('Send this report to {{ addslashes($report->client->name) }}\'s portal login?')">
            @csrf
            <button type="submit" class="btn btn-primary">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ $report->isSent() ? 'Resend to Client' : 'Send to Client' }}
            </button>
        </form>
        <a href="{{ route('reports.pdf', $report) }}" class="btn btn-secondary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            PDF
        </a>
        <a href="{{ route('reports.edit', $report) }}" class="btn btn-secondary">Edit</a>
        <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Delete this report?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="g4 mb6">
    <div class="stat-card">
        <div class="stat-label">Reach</div>
        <div class="stat-value">{{ $report->reach !== null ? number_format($report->reach) : '—' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Engagement</div>
        <div class="stat-value">{{ $report->engagement !== null ? number_format($report->engagement) : '—' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Video Views</div>
        <div class="stat-value">{{ $report->video_views !== null ? number_format($report->video_views) : '—' }}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Tasks Completed</div>
        <div class="stat-value">{{ $report->tasksCompletedCount() }}</div>
        <div class="stat-sub">Auto-tracked from this period</div>
    </div>
</div>

<div class="g2">
    <div class="card">
        <div class="card-hd"><h3>Report Details</h3></div>
        <div class="card-bd">
            <div class="detail-row">
                <span class="detail-label">Client</span>
                <span class="detail-value"><a href="{{ route('clients.show', $report->client) }}" class="link">{{ $report->client->name }}</a></span>
                <span class="detail-label">Project</span>
                <span class="detail-value">
                    @if($report->project)
                        <a href="{{ route('projects.show', $report->project) }}" class="link">{{ $report->project->name }}</a>
                    @else — @endif
                </span>
                <span class="detail-label">Period</span>
                <span class="detail-value">{{ $report->periodEnum()->label() }}</span>
                <span class="detail-label">Range</span>
                <span class="detail-value">{{ $report->period_start->format('M d, Y') }} – {{ $report->period_end->format('M d, Y') }}</span>
                <span class="detail-label">Best Post</span>
                <span class="detail-value">{{ $report->best_performing_post ?: '—' }}</span>
                <span class="detail-label">Created By</span>
                <span class="detail-value">{{ $report->createdBy?->name ?? '—' }}</span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-hd"><h3>Next Period's Plan</h3></div>
        <div class="card-bd">
            @if($report->next_plan)
                <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;white-space:pre-line;">{{ $report->next_plan }}</p>
            @else
                <p class="text-muted text-sm">No plan added yet.</p>
            @endif
        </div>
    </div>
</div>

@if($report->notes)
<div class="card" style="margin-top:14px;">
    <div class="card-hd"><h3>Internal Notes <span class="text-faint text-xs" style="font-weight:400;">(not shown to client)</span></h3></div>
    <div class="card-bd">
        <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;white-space:pre-line;">{{ $report->notes }}</p>
    </div>
</div>
@endif
@endsection
