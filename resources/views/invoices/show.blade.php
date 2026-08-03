@extends('layouts.app')
@section('title', $invoice->invoice_number)
@section('breadcrumb', 'Invoices / ' . $invoice->invoice_number)

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $invoice->invoice_number }}</h2>
        <div class="row center" style="gap:8px;margin-top:4px;">
            <span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span>
            <span class="fw600" style="color:#6c63ff;">{!! format_currency($invoice->amount) !!}</span>
        </div>
    </div>
    <div class="row" style="gap:6px;">
        <a href="{{ route('invoices.pdf', $invoice) }}" class="btn btn-secondary">
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
            Download PDF
        </a>
        <a href="{{ route('invoices.edit', $invoice) }}" class="btn btn-secondary">Edit</a>
        <form method="POST" action="{{ route('invoices.destroy', $invoice) }}" onsubmit="return confirm('Delete this invoice?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="g2">
    <div class="card">
        <div class="card-hd"><h3>Invoice Details</h3></div>
        <div class="card-bd">
            <div class="detail-row">
                <span class="detail-label">Invoice #</span>
                <span class="detail-value fw600">{{ $invoice->invoice_number }}</span>
                <span class="detail-label">Amount</span>
                <span class="detail-value fw600" style="color:#6c63ff;">{!! format_currency($invoice->amount) !!}</span>
                <span class="detail-label">Status</span>
                <span class="detail-value"><span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span></span>
                <span class="detail-label">Client</span>
                <span class="detail-value">
                    @if($invoice->client)
                        <a href="{{ route('clients.show', $invoice->client) }}" class="link">{{ $invoice->client->name }}</a>
                    @else — @endif
                </span>
                <span class="detail-label">Project</span>
                <span class="detail-value">
                    @if($invoice->project)
                        <a href="{{ route('projects.show', $invoice->project) }}" class="link">{{ $invoice->project->name }}</a>
                    @else — @endif
                </span>
                <span class="detail-label">Issued</span>
                <span class="detail-value">{{ $invoice->issued_date?->format('M d, Y') ?? '—' }}</span>
                <span class="detail-label">Due</span>
                <span class="detail-value" style="{{ $invoice->due_date?->isPast() && $invoice->status=='sent' ? 'color:#f87171' : '' }}">
                    {{ $invoice->due_date?->format('M d, Y') ?? '—' }}
                    @if($invoice->due_date?->isPast() && $invoice->status=='sent')
                        <span style="font-size:0.72rem;"> (Overdue)</span>
                    @endif
                </span>
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-hd"><h3>Quick Status Update</h3></div>
        <div class="card-bd">
            <p class="text-sm text-muted" style="margin-bottom:14px;">Change the invoice status.</p>
            @foreach(['draft'=>'Draft','sent'=>'Sent','paid'=>'Paid','overdue'=>'Overdue','cancelled'=>'Cancelled'] as $s => $label)
            <form method="POST" action="{{ route('invoices.update', $invoice) }}" style="margin-bottom:6px;">
                @csrf @method('PATCH')
                <input type="hidden" name="invoice_number" value="{{ $invoice->invoice_number }}">
                <input type="hidden" name="amount" value="{{ $invoice->amount }}">
                <input type="hidden" name="status" value="{{ $s }}">
                <input type="hidden" name="client_id" value="{{ $invoice->client_id }}">
                <input type="hidden" name="project_id" value="{{ $invoice->project_id }}">
                <input type="hidden" name="issued_date" value="{{ $invoice->issued_date?->format('Y-m-d') }}">
                <input type="hidden" name="due_date" value="{{ $invoice->due_date?->format('Y-m-d') }}">
                <input type="hidden" name="notes" value="{{ $invoice->notes }}">
                <button type="submit" class="btn {{ $invoice->status==$s ? 'btn-primary' : 'btn-secondary' }}" style="width:100%;justify-content:center;">
                    <span class="badge badge-{{ $s }}" style="margin-right:6px;">{{ $label }}</span>
                    {{ $invoice->status==$s ? '← Current' : 'Mark as '.$label }}
                </button>
            </form>
            @endforeach
        </div>
    </div>
</div>

@if($invoice->notes)
<div class="card" style="margin-top:14px;">
    <div class="card-hd"><h3>Notes</h3></div>
    <div class="card-bd">
        <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;white-space:pre-line;">{{ $invoice->notes }}</p>
    </div>
</div>
@endif
@endsection
