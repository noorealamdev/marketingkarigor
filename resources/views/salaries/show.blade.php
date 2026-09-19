@extends('layouts.app')
@section('title', $user->name . ' — Salary')
@section('breadcrumb', 'Salaries')

@push('styles')
<style>
    @media (max-width: 768px) {
        .payment-form-grid { grid-template-columns: 1fr !important; }
    }
</style>
@endpush

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $user->name }} — Salary</h2>
        <p>{{ $user->roles->pluck('name')->join(', ') ?: 'No role' }} &middot; {{ $user->email }}</p>
    </div>
    <a href="{{ route('salaries.index') }}" class="btn btn-secondary btn-sm">← All Members</a>
</div>

<div class="g2 mb6">

    {{-- Current Salary Card --}}
    <div class="card" style="padding:22px 24px;">
        <div class="card-hd" style="padding:0 0 14px;margin-bottom:14px;"><h3>Current Salary</h3></div>
        @if($current)
        <div style="display:flex;align-items:baseline;gap:8px;margin-bottom:8px;">
            <span style="font-size:2.2rem;font-weight:800;color:#c8cce0;letter-spacing:-0.04em;">{!! $current->formatted_amount !!}</span>
            <span class="text-muted" style="font-size:0.9rem;">/ {{ $current->period }}</span>
        </div>
        <div class="text-xs text-muted" style="margin-bottom:4px;">Effective {{ $current->effective_date->format('F d, Y') }}</div>
        @php
            $monthly = match($current->period) {
                'yearly'  => $current->amount / 12,
                'weekly'  => $current->amount * 4.33,
                default   => $current->amount,
            };
        @endphp
        <div style="margin-top:14px;padding-top:14px;border-top:1px solid #252936;display:grid;grid-template-columns:1fr 1fr;gap:12px;text-align:center;">
            <div>
                <div class="fw700" style="color:#4ade80;font-size:1.05rem;">{!! format_currency($monthly) !!}</div>
                <div class="text-xs text-muted">Monthly</div>
            </div>
            <div>
                <div class="fw700" style="color:#60a5fa;font-size:1.05rem;">{!! format_currency($monthly*12) !!}</div>
                <div class="text-xs text-muted">Annual</div>
            </div>
        </div>
        @if($current->notes)
        <div class="desc-field" style="margin-top:14px;padding:10px 12px;background:#13161d;border-radius:6px;font-size:0.8rem;color:#6b7590;">
            {!! format_description($current->notes) !!}
        </div>
        @endif
        @else
        <div style="color:#3a4060;font-size:0.9rem;padding:12px 0;">No salary record yet. Use the form to set one.</div>
        @endif
    </div>

    {{-- Set / Update Salary Form --}}
    <div class="card" style="padding:22px 24px;">
        <div class="card-hd" style="padding:0 0 14px;margin-bottom:14px;"><h3>{{ $current ? 'Update Salary' : 'Set Salary' }}</h3></div>
        <form method="POST" action="{{ route('salaries.store', $user) }}">
            @csrf
            <div style="display:grid;gap:14px;">
                <div>
                    <label class="form-label">Amount (৳)</label>
                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                           step="0.01" min="0" placeholder="0.00"
                           value="{{ old('amount', $current?->amount) }}" required>
                    @error('amount')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label class="form-label">Pay Period</label>
                        <select name="period" class="form-control">
                            @foreach(['monthly','weekly','yearly'] as $p)
                            <option value="{{ $p }}" {{ old('period', $current?->period ?? 'monthly') == $p ? 'selected' : '' }}>{{ ucfirst($p) }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="form-label">Effective Date</label>
                        <input type="date" name="effective_date" class="form-control @error('effective_date') is-invalid @enderror"
                               value="{{ old('effective_date', now()->format('Y-m-d')) }}" required>
                        @error('effective_date')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes <span class="text-muted" style="font-weight:400;">(optional)</span></label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Annual review raise, promoted to senior…" style="resize:vertical;">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    {{ $current ? 'Update Salary' : 'Set Salary' }}
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Record Payment --}}
<div class="card mb6" style="padding:22px 24px;max-width:100%;">
    <div class="card-hd" style="padding:0 0 14px;margin-bottom:14px;"><h3>Record a Payment</h3></div>
    <form method="POST" action="{{ route('salaries.payments.store', $user) }}">
        @csrf
        <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:12px;align-items:end;" class="payment-form-grid">
            <div>
                <label class="form-label">Amount (৳)</label>
                <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                       step="0.01" min="0" placeholder="0.00" value="{{ old('amount', $current?->amount) }}" required>
                @error('amount')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Pay Period</label>
                <input type="date" name="period_month" class="form-control @error('period_month') is-invalid @enderror"
                       value="{{ old('period_month', now()->startOfMonth()->format('Y-m-d')) }}" required>
                @error('period_month')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <div>
                <label class="form-label">Date Paid</label>
                <input type="date" name="paid_at" class="form-control @error('paid_at') is-invalid @enderror"
                       value="{{ old('paid_at', now()->format('Y-m-d')) }}" required>
                @error('paid_at')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
            </div>
            <button type="submit" class="btn btn-primary btn-sm" style="justify-content:center;">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                Record Payment
            </button>
        </div>
        <div style="margin-top:12px;">
            <label class="form-label">Notes <span class="text-muted" style="font-weight:400;">(optional)</span></label>
            <input type="text" name="notes" class="form-control" placeholder="e.g. Includes ৳2,000 bonus" value="{{ old('notes') }}">
        </div>
        <p class="text-xs text-muted" style="margin-top:8px;">Recording a payment automatically logs it as an expense in Finance.</p>
    </form>
</div>

{{-- Payment History --}}
<div class="card mb6">
    <div class="card-hd"><h3>Payment History ({{ $payments->count() }})</h3></div>
    <table class="table">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Pay Period</th>
                <th>Date Paid</th>
                <th>Paid By</th>
                <th>Notes</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td class="fw700 td-header" style="color:#4ade80;">{!! $payment->formatted_amount !!}</td>
                <td class="text-muted" data-label="Pay Period">{{ $payment->period_month->format('F Y') }}</td>
                <td class="text-muted" data-label="Date Paid">{{ $payment->paid_at->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Paid By">{{ $payment->paidBy?->name ?? '—' }}</td>
                <td class="text-muted" data-label="Notes" style="max-width:200px;white-space:normal;font-size:0.8rem;">{{ $payment->notes ?: '—' }}</td>
                <td class="td-actions">
                    <form method="POST" action="{{ route('salaries.payments.destroy', $payment) }}"
                          onsubmit="return confirm('Delete this payment record? This also removes the matching Finance expense.')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="td-empty">
                <div class="empty-state"><p>No payments recorded yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Task Payments (freelance / per-task work) --}}
<div class="card mb6">
    <div class="card-hd"><h3>Task Payments ({{ $taskPayments->count() }}) &middot; Total {!! format_currency($taskPayments->sum('amount')) !!}</h3></div>
    <table class="table">
        <thead>
            <tr><th>Task</th><th>Amount</th><th>Date Paid</th><th>Paid By</th><th>Notes</th></tr>
        </thead>
        <tbody>
            @forelse($taskPayments as $tp)
            <tr>
                <td class="td-header">
                    @if($tp->task)<a href="{{ route('tasks.show', $tp->task) }}" class="link fw600">{{ $tp->task->name }}</a>@else<span class="text-muted">Deleted task</span>@endif
                </td>
                <td class="fw700" data-label="Amount" style="color:#4ade80;">{!! $tp->formatted_amount !!}</td>
                <td class="text-muted" data-label="Date Paid">{{ $tp->paid_at->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Paid By">{{ $tp->paidBy?->name ?? '—' }}</td>
                <td class="text-muted" data-label="Notes" style="font-size:0.8rem;white-space:normal;">{{ $tp->notes ?: '—' }}</td>
            </tr>
            @empty
            <tr><td colspan="5" class="td-empty"><div class="empty-state"><p>No task payments yet. Pay from a completed task's page.</p></div></td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Salary History Table --}}
<div class="card">
    <div class="card-hd"><h3>Salary Rate History</h3></div>
    <table class="table">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Period</th>
                <th>Effective Date</th>
                <th>Set By</th>
                <th>Notes</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($records as $record)
            <tr>
                <td class="fw700 td-header" style="{{ $loop->first ? 'color:#4ade80;' : '' }}">
                    {!! $record->formatted_amount !!}
                    @if($loop->first)<span class="badge" style="background:rgba(74,222,128,0.1);color:#4ade80;margin-left:6px;font-size:0.65rem;">Current</span>@endif
                </td>
                <td class="text-muted" data-label="Period" style="text-transform:capitalize;">{{ $record->period }}</td>
                <td class="text-muted" data-label="Effective Date">{{ $record->effective_date->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Set By">{{ $record->createdBy?->name ?? '—' }}</td>
                <td class="text-muted" data-label="Notes" style="max-width:160px;white-space:normal;font-size:0.8rem;">{{ $record->notes ?: '—' }}</td>
                <td class="td-actions">
                    <form method="POST" action="{{ route('salaries.destroy', $record) }}"
                          onsubmit="return confirm('Delete this salary record?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="td-empty">
                <div class="empty-state"><p>No salary history yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
