@extends('layouts.app')
@section('title', 'Finance')
@section('breadcrumb', 'Finance')

@section('content')
<div class="page-hd">
    <h2>Finance</h2>
    <p>Track investor payments received and money spent.</p>
</div>

{{-- Summary --}}
<div class="g3 mb6">
    <div class="stat-card">
        <div class="stat-label">Total Received</div>
        <div class="stat-value" style="color:#4ade80;">{!! format_currency($totalReceived) !!}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Total Spent</div>
        <div class="stat-value" style="color:#f87171;">{!! format_currency($totalSpent) !!}</div>
    </div>
    <div class="stat-card">
        <div class="stat-label">Net Balance</div>
        <div class="stat-value" style="color:{{ $netBalance >= 0 ? '#60a5fa' : '#f87171' }};">{!! format_currency($netBalance) !!}</div>
    </div>
</div>

<div class="g2 mb6">
    {{-- Record Investor Payment --}}
    <div class="card" style="padding:22px 24px;">
        <div class="card-hd" style="padding:0 0 14px;margin-bottom:14px;"><h3>Record Investor Payment</h3></div>
        <form method="POST" action="{{ route('finance.payments.store') }}">
            @csrf
            <div style="display:grid;gap:14px;">
                <div>
                    <label class="form-label">Amount (৳)</label>
                    <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                           step="0.01" min="0" placeholder="0.00" value="{{ old('amount') }}" required>
                    @error('amount')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label class="form-label">Date Received</label>
                        <input type="date" name="received_at" class="form-control @error('received_at') is-invalid @enderror"
                               value="{{ old('received_at', now()->format('Y-m-d')) }}" required>
                        @error('received_at')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Method <span class="text-muted" style="font-weight:400;">(optional)</span></label>
                        <input type="text" name="method" class="form-control" placeholder="Bank transfer, bKash…" value="{{ old('method') }}">
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes <span class="text-muted" style="font-weight:400;">(optional)</span></label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="e.g. Q3 funding installment" style="resize:vertical;">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Record Payment
                </button>
            </div>
        </form>
    </div>

    {{-- Record Expense --}}
    <div class="card" style="padding:22px 24px;">
        <div class="card-hd" style="padding:0 0 14px;margin-bottom:14px;"><h3>Record Expense</h3></div>
        <form method="POST" action="{{ route('finance.expenses.store') }}">
            @csrf
            <div style="display:grid;gap:14px;">
                <div>
                    <label class="form-label">Title</label>
                    <input type="text" name="title" class="form-control @error('title') is-invalid @enderror"
                           placeholder="e.g. Office rent, software subscription" value="{{ old('title') }}" required>
                    @error('title')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                </div>
                <div style="display:grid;grid-template-columns:1fr 1fr;gap:10px;">
                    <div>
                        <label class="form-label">Amount (৳)</label>
                        <input type="number" name="amount" class="form-control @error('amount') is-invalid @enderror"
                               step="0.01" min="0" placeholder="0.00" value="{{ old('amount') }}" required>
                        @error('amount')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                    <div>
                        <label class="form-label">Date Spent</label>
                        <input type="date" name="spent_at" class="form-control @error('spent_at') is-invalid @enderror"
                               value="{{ old('spent_at', now()->format('Y-m-d')) }}" required>
                        @error('spent_at')<div class="text-xs" style="color:#f87171;margin-top:4px;">{{ $message }}</div>@enderror
                    </div>
                </div>
                <div>
                    <label class="form-label">Notes <span class="text-muted" style="font-weight:400;">(optional)</span></label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="Optional details" style="resize:vertical;">{{ old('notes') }}</textarea>
                </div>
                <button type="submit" class="btn btn-primary btn-sm" style="justify-content:center;">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Record Expense
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Investor Payments History --}}
<div class="card mb6">
    <div class="card-hd"><h3>Investor Payments ({{ $payments->total() }})</h3></div>
    <table class="table">
        <thead>
            <tr>
                <th>Amount</th>
                <th>Date Received</th>
                <th>Method</th>
                <th>Notes</th>
                <th>Recorded By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($payments as $payment)
            <tr>
                <td class="fw700 td-header" style="color:#4ade80;">{!! $payment->formatted_amount !!}</td>
                <td class="text-muted" data-label="Date Received">{{ $payment->received_at->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Method">{{ $payment->method ?: '—' }}</td>
                <td class="text-muted" data-label="Notes" style="max-width:200px;white-space:normal;font-size:0.8rem;">{{ $payment->notes ?: '—' }}</td>
                <td class="text-muted" data-label="Recorded By">{{ $payment->recordedBy?->name ?? '—' }}</td>
                <td class="td-actions">
                    <form method="POST" action="{{ route('finance.payments.destroy', $payment) }}"
                          onsubmit="return confirm('Delete this investor payment record?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="td-empty">
                <div class="empty-state"><p>No investor payments recorded yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pager">{{ $payments->links() }}</div>
</div>

{{-- Expenses History --}}
<div class="card">
    <div class="card-hd"><h3>Expenses ({{ $expenses->total() }})</h3></div>
    <table class="table">
        <thead>
            <tr>
                <th>Title</th>
                <th>Amount</th>
                <th>Date Spent</th>
                <th>Notes</th>
                <th>Recorded By</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @forelse($expenses as $expense)
            <tr>
                <td class="td-header fw600">{{ $expense->title }}</td>
                <td class="fw700" data-label="Amount" style="color:#f87171;">{!! $expense->formatted_amount !!}</td>
                <td class="text-muted" data-label="Date Spent">{{ $expense->spent_at->format('M d, Y') }}</td>
                <td class="text-muted" data-label="Notes" style="max-width:200px;white-space:normal;font-size:0.8rem;">{{ $expense->notes ?: '—' }}</td>
                <td class="text-muted" data-label="Recorded By">{{ $expense->recordedBy?->name ?? '—' }}</td>
                <td class="td-actions">
                    <form method="POST" action="{{ route('finance.expenses.destroy', $expense) }}"
                          onsubmit="return confirm('Delete this expense record?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-danger btn-xs">Del</button>
                    </form>
                </td>
            </tr>
            @empty
            <tr><td colspan="6" class="td-empty">
                <div class="empty-state"><p>No expenses recorded yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
    <div class="pager">{{ $expenses->links() }}</div>
</div>
@endsection
