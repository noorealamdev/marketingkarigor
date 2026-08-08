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
                <th>Report</th>
                <th>Service</th>
                <th>Period</th>
                <th>Range</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr>
                <td class="td-header">
                    <a href="{{ route('client.reports.show', $report) }}" class="fw600 link">{{ $report->title ?: ($report->report_type ?: 'Report') }}</a>
                </td>
                <td class="text-muted" data-label="Service">{{ $report->report_type ?: '—' }}</td>
                <td data-label="Period"><span class="badge" style="background:rgba(242,183,5,0.12);color:#f8d878;">{{ $report->periodEnum()->label() }}</span></td>
                <td class="text-muted" data-label="Range">{{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}</td>
                <td class="td-actions"><a href="{{ route('client.reports.show', $report) }}" class="btn btn-secondary btn-sm">View →</a></td>
            </tr>
            @empty
            <tr><td colspan="5" class="td-empty">
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
