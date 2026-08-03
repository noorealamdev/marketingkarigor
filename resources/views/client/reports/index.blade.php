@extends('layouts.portal')
@section('title', 'Reports')

@section('content')
<div class="page-hd">
    <h2>Performance Reports</h2>
    <p>Reports shared with you by the {{ config('app.name') }} team.</p>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Period</th>
                <th>Range</th>
                <th>Reach</th>
                <th>Engagement</th>
                <th>Video Views</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr>
                <td class="td-header"><span class="badge" style="background:rgba(242,183,5,0.12);color:#f8d878;">{{ $report->periodEnum()->label() }}</span></td>
                <td class="text-muted" data-label="Range">{{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Reach">{{ $report->reach !== null ? number_format($report->reach) : '—' }}</td>
                <td class="text-muted" data-label="Engagement">{{ $report->engagement !== null ? number_format($report->engagement) : '—' }}</td>
                <td class="text-muted" data-label="Video Views">{{ $report->video_views !== null ? number_format($report->video_views) : '—' }}</td>
                <td class="td-actions"><a href="{{ route('client.reports.show', $report) }}" class="btn btn-secondary btn-sm">View →</a></td>
            </tr>
            @empty
            <tr><td colspan="6" class="td-empty">
                <div class="empty-state"><p>No reports have been shared with you yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($reports->hasPages())
<div class="pager">{{ $reports->links() }}</div>
@endif
@endsection
