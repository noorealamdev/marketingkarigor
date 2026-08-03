@extends('layouts.app')
@section('title', 'Reports')
@section('breadcrumb', 'Reports')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Performance Reports</h2>
        <p>{{ $reports->total() }} total reports</p>
    </div>
    <a href="{{ route('reports.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Report
    </a>
</div>

<form method="GET" class="filter-row mb4">
    <x-client-picker :clients="$clients" :selected="request('client_id')" empty-option="All Clients" placeholder="All Clients" />
    <select name="period_type" class="form-control">
        <option value="">All Periods</option>
        @foreach(\App\Enums\ReportPeriod::cases() as $period)
        <option value="{{ $period->value }}" {{ request('period_type')==$period->value?'selected':'' }}>{{ $period->label() }}</option>
        @endforeach
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request()->hasAny(['client_id','period_type']))
        <a href="{{ route('reports.index') }}" class="btn btn-secondary btn-sm">Clear</a>
    @endif
</form>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Client</th>
                <th>Period</th>
                <th>Range</th>
                <th>Reach</th>
                <th>Engagement</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reports as $report)
            <tr>
                <td class="td-header">
                    <a href="{{ route('reports.show', $report) }}" class="fw600 link">{{ $report->client->company ?: $report->client->name }}</a>
                    @if($report->project)
                    <div class="text-xs text-muted" style="margin-top:2px;">{{ $report->project->name }}</div>
                    @endif
                </td>
                <td data-label="Period"><span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $report->periodEnum()->label() }}</span></td>
                <td class="text-muted" data-label="Range">{{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Reach">{{ $report->reach !== null ? number_format($report->reach) : '—' }}</td>
                <td class="text-muted" data-label="Engagement">{{ $report->engagement !== null ? number_format($report->engagement) : '—' }}</td>
                <td data-label="Status">
                    @if($report->isSent())
                        <span class="badge badge-done">Sent</span>
                    @else
                        <span class="badge badge-todo">Draft</span>
                    @endif
                </td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('reports.show', $report) }}" class="btn btn-secondary btn-xs">View</a>
                        <a href="{{ route('reports.edit', $report) }}" class="btn btn-secondary btn-xs">Edit</a>
                        <form method="POST" action="{{ route('reports.destroy', $report) }}" onsubmit="return confirm('Delete this report?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="td-empty">
                <div class="empty-state">
                    <p>No reports yet. Create your first performance report.</p>
                    <a href="{{ route('reports.create') }}" class="btn btn-primary btn-sm">New Report</a>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($reports->hasPages())
<div class="pager">{{ $reports->links() }}</div>
@endif
@endsection
