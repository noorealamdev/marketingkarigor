@extends('layouts.app')
@section('title', 'Invoices')
@section('breadcrumb', 'Invoices')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Invoices</h2>
        <p>{{ $invoices->total() }} total invoices</p>
    </div>
    <a href="{{ route('invoices.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Invoice
    </a>
</div>

<form method="GET" class="filter-row mb4">
    <input type="text" name="search" class="form-control" placeholder="Search invoice #…" value="{{ request('search') }}">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <option value="draft" {{ request('status')=='draft'?'selected':'' }}>Draft</option>
        <option value="sent" {{ request('status')=='sent'?'selected':'' }}>Sent</option>
        <option value="paid" {{ request('status')=='paid'?'selected':'' }}>Paid</option>
        <option value="overdue" {{ request('status')=='overdue'?'selected':'' }}>Overdue</option>
        <option value="cancelled" {{ request('status')=='cancelled'?'selected':'' }}>Cancelled</option>
    </select>
    <x-client-picker :clients="$clients" :selected="request('client_id')" empty-option="All Clients" placeholder="All Clients" />
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request()->hasAny(['search','status','client_id']))
        <a href="{{ route('invoices.index') }}" class="btn btn-secondary btn-sm">Clear</a>
    @endif
</form>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Invoice #</th>
                <th>Client</th>
                <th>Project</th>
                <th>Amount</th>
                <th>Status</th>
                <th>Issued</th>
                <th>Due</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invoices as $invoice)
            <tr>
                <td class="td-header"><a href="{{ route('invoices.show', $invoice) }}" class="fw600 link">{{ $invoice->invoice_number }}</a></td>
                <td class="text-muted" data-label="Client">{{ $invoice->client?->name ?? '—' }}</td>
                <td class="text-muted" data-label="Project">{{ $invoice->project?->name ?? '—' }}</td>
                <td class="fw600" data-label="Amount">{!! format_currency($invoice->amount) !!}</td>
                <td data-label="Status"><span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span></td>
                <td class="text-muted" data-label="Issued">{{ $invoice->issued_date?->format('M d, Y') ?? '—' }}</td>
                <td class="text-muted" data-label="Due" style="{{ $invoice->due_date?->isPast() && $invoice->status=='sent' ? 'color:#f87171' : '' }}">
                    {{ $invoice->due_date?->format('M d, Y') ?? '—' }}
                </td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary btn-xs">View</a>
                        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-secondary btn-xs">Edit</a>
                        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete invoice?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Del</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="8" class="td-empty">
                <div class="empty-state">
                    <p>No invoices yet. Create your first invoice to get started.</p>
                    <a href="{{ route('invoices.create') }}" class="btn btn-primary btn-sm">New Invoice</a>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($invoices->hasPages())
<div class="pager">{{ $invoices->links() }}</div>
@endif
@endsection
