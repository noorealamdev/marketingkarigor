@extends('layouts.portal')
@section('title', ($report->title ?: $report->periodEnum()->label() . ' Report'))

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $report->title ?: $report->periodEnum()->label() . ' Report' }}</h2>
        <p>
            @if($report->report_type){{ $report->report_type }} &middot; @endif
            {{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}
        </p>
    </div>
    <a href="{{ route('client.reports.index') }}" class="btn btn-secondary">← All Reports</a>
</div>

@if(!empty($report->metrics))
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(150px,1fr));gap:14px;margin-bottom:24px;">
    @foreach($report->metrics as $m)
    <div class="stat-card">
        <div class="stat-label">{{ $m['label'] }}</div>
        <div class="stat-value" style="font-size:1.5rem;">{{ ($m['value'] ?? '') !== '' ? $m['value'] : '—' }}</div>
    </div>
    @endforeach
</div>
@endif

@if($report->summary)
<div class="card" style="margin-bottom:16px;">
    <div class="card-hd"><h3>Summary</h3></div>
    <div class="card-bd">
        <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;white-space:pre-line;">{{ $report->summary }}</p>
    </div>
</div>
@endif

<div class="card">
    <div class="card-hd"><h3>Next Steps / Plan</h3></div>
    <div class="card-bd">
        @if($report->next_plan)
            <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;white-space:pre-line;">{{ $report->next_plan }}</p>
        @else
            <p class="text-muted text-sm">No plan added for the next period yet.</p>
        @endif
    </div>
</div>
@endsection
