@extends('layouts.portal')
@section('title', $report->periodEnum()->label() . ' Report')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $report->periodEnum()->label() }} Report</h2>
        <p>{{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}</p>
    </div>
    <a href="{{ route('client.reports.index') }}" class="btn btn-secondary">← All Reports</a>
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
        <div class="stat-label">Best Performing Post</div>
        <div class="stat-value" style="font-size:1rem;">{{ $report->best_performing_post ?: '—' }}</div>
    </div>
</div>

<div class="card">
    <div class="card-hd"><h3>Next Period's Plan</h3></div>
    <div class="card-bd">
        @if($report->next_plan)
            <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;white-space:pre-line;">{{ $report->next_plan }}</p>
        @else
            <p class="text-muted text-sm">No plan added for the next period yet.</p>
        @endif
    </div>
</div>
@endsection
