@extends('layouts.app')
@section('title', 'Salaries')
@section('breadcrumb', 'Salaries')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Salary Management</h2>
        <p>{{ $members->count() }} team members &mdash; est. monthly payroll: <strong style="color:#4ade80;">{!! format_currency($totalMonthly) !!}</strong></p>
    </div>
</div>

{{-- Summary Cards --}}
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:14px;margin-bottom:24px;">
    @foreach($members as $member)
    @php $s = $member->currentSalary; @endphp
    <div class="card" style="padding:18px 20px;">
        <div class="row between" style="margin-bottom:14px;">
            <div class="row center" style="gap:10px;">
                {!! user_avatar($member, 38) !!}
                <div>
                    <div class="fw700" style="font-size:0.88rem;">{{ $member->name }}</div>
                    <div class="text-xs text-muted">{{ $member->roles->pluck('name')->join(', ') ?: 'No role' }}</div>
                </div>
            </div>
            <a href="{{ route('salaries.show', $member) }}" class="btn btn-secondary btn-xs">Manage</a>
        </div>

        @if($s)
        <div style="display:flex;align-items:baseline;gap:6px;margin-bottom:4px;">
            <span style="font-size:1.55rem;font-weight:800;color:#c8cce0;letter-spacing:-0.03em;">
                {!! $s->formatted_amount !!}
            </span>
            <span class="text-xs text-muted">/ {{ $s->period }}</span>
        </div>
        <div style="display:flex;align-items:center;justify-content:space-between;">
            <div class="text-xs text-muted">Effective {{ $s->effective_date->format('M d, Y') }}</div>
            @if(in_array($member->id, $paidUserIds))
                <span class="badge" style="background:rgba(74,222,128,0.12);color:#4ade80;">Paid this month</span>
            @else
                <span class="badge" style="background:rgba(248,113,113,0.12);color:#f87171;">Unpaid this month</span>
            @endif
        </div>
        @else
        <div style="color:#3a4060;font-size:0.82rem;padding:8px 0;">No salary set yet</div>
        @endif
    </div>
    @endforeach
</div>

{{-- Full Table --}}
<div class="card">
    <div class="card-hd"><h3>All Members</h3></div>
    <table class="table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Role</th>
                <th>Current Salary</th>
                <th>Period</th>
                <th>Effective Date</th>
                <th>Monthly Equivalent</th>
                <th>Paid</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            @php
                $s = $member->currentSalary;
                $monthly = $s ? match($s->period) {
                    'yearly'  => $s->amount / 12,
                    'weekly'  => $s->amount * 4.33,
                    default   => $s->amount,
                } : null;
            @endphp
            <tr>
                <td class="td-header">
                    <div class="fw600">{{ $member->name }}</div>
                    <div class="text-xs text-muted">{{ $member->email }}</div>
                </td>
                <td data-label="Role">
                    <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:flex-end;">
                        @forelse($member->roles as $role)
                            <span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $role->name }}</span>
                        @empty
                            <span class="text-xs text-faint">—</span>
                        @endforelse
                    </div>
                </td>
                <td class="fw600" data-label="Current Salary">{!! $s ? $s->formatted_amount : '—' !!}</td>
                <td class="text-muted" data-label="Period" style="text-transform:capitalize;">{{ $s?->period ?? '—' }}</td>
                <td class="text-muted" data-label="Effective Date">{{ $s?->effective_date?->format('M d, Y') ?? '—' }}</td>
                <td class="fw600" data-label="Monthly Equivalent" style="color:#4ade80;">{!! $monthly ? format_currency($monthly) : '—' !!}</td>
                <td data-label="Paid">
                    @if($s)
                        @if(in_array($member->id, $paidUserIds))
                            <span class="badge" style="background:rgba(74,222,128,0.12);color:#4ade80;">Paid this month</span>
                        @else
                            <span class="badge" style="background:rgba(248,113,113,0.12);color:#f87171;">Unpaid this month</span>
                        @endif
                    @else
                        <span class="text-xs text-faint">—</span>
                    @endif
                </td>
                <td class="td-actions"><a href="{{ route('salaries.show', $member) }}" class="btn btn-secondary btn-xs">History →</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
