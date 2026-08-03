@extends('layouts.app')
@section('title', $client->name)
@section('breadcrumb', 'Clients / ' . $client->name)

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $client->name }} <span class="text-faint text-xs" style="font-weight:400;">#{{ $client->id }}</span></h2>
        <p>{{ $client->company ?? 'Individual Client' }}</p>
    </div>
    <div class="row" style="gap:6px;">
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary">Edit</a>
        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Delete this client and all their data?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
    </div>
</div>

<div class="g2 mb6">
    <div class="card">
        <div class="card-hd"><h3>Contact Information</h3></div>
        <div class="card-bd">
            <div class="detail-row">
                <span class="detail-label">Status</span>
                <span class="detail-value"><span class="badge badge-{{ $client->status }}">{{ $client->status }}</span></span>
                <span class="detail-label">Email</span>
                <span class="detail-value">{{ $client->email ?: '—' }}</span>
                <span class="detail-label">Phone</span>
                <span class="detail-value">{{ $client->phone ?: '—' }}</span>
                <span class="detail-label">Website</span>
                <span class="detail-value">{!! $client->website ? '<a href="'.e($client->website).'" class="link" target="_blank">'.e($client->website).'</a>' : '—' !!}</span>
                <span class="detail-label">Address</span>
                <span class="detail-value" style="white-space:pre-line;">{{ $client->address ?: '—' }}</span>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-hd"><h3>Notes</h3></div>
        <div class="card-bd">
            @if($client->notes)
                <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;" class="desc-field">{!! format_description($client->notes) !!}</p>
            @else
                <p class="text-muted text-sm">No notes added yet.</p>
            @endif
        </div>
    </div>
</div>

{{-- Brand Kit --}}
<div class="card mb6">
    <div class="card-hd">
        <h3>Brand Kit</h3>
        <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary btn-xs">Edit</a>
    </div>
    <div class="card-bd">
        <div class="g2">
            <div class="detail-row">
                <span class="detail-label">WhatsApp</span>
                <span class="detail-value">{{ $client->whatsapp ?: '—' }}</span>
                <span class="detail-label">Facebook Page</span>
                <span class="detail-value">{!! $client->facebook_page ? '<a href="'.e($client->facebook_page).'" class="link" target="_blank">'.e($client->facebook_page).'</a>' : '—' !!}</span>
                <span class="detail-label">Package</span>
                <span class="detail-value">{{ $client->package ?: '—' }}</span>
                <span class="detail-label">Renewal Date</span>
                <span class="detail-value">{{ $client->renewal_date ? $client->renewal_date->format('M d, Y') : '—' }}</span>
            </div>
            <div>
                <div style="margin-bottom:12px;">
                    <div class="detail-label" style="margin-bottom:6px;">Logo</div>
                    @if($client->getFirstMediaUrl('logo'))
                        <img src="{{ $client->getFirstMediaUrl('logo') }}" alt="Logo" style="max-height:60px;border-radius:6px;">
                    @else
                        <span class="text-muted text-sm">No logo uploaded.</span>
                    @endif
                </div>
                <div style="margin-bottom:12px;">
                    <div class="detail-label" style="margin-bottom:6px;">Brand Colors</div>
                    @if(!empty($client->brand_colors))
                        <div class="row" style="gap:6px;flex-wrap:wrap;">
                            @foreach($client->brand_colors as $color)
                            <span class="row" style="gap:5px;background:#1a1e28;border:1px solid #252936;border-radius:6px;padding:3px 8px;">
                                <span style="width:12px;height:12px;border-radius:3px;background:{{ $color }};display:inline-block;"></span>
                                <span class="text-xs text-muted">{{ $color }}</span>
                            </span>
                            @endforeach
                        </div>
                    @else
                        <span class="text-muted text-sm">No colors set.</span>
                    @endif
                </div>
                <div>
                    <div class="detail-label" style="margin-bottom:6px;">Fonts</div>
                    <span class="text-sm">{{ !empty($client->fonts) ? implode(', ', $client->fonts) : '—' }}</span>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Projects --}}
<div class="card mb6">
    <div class="card-hd">
        <h3>Projects ({{ $client->projects->count() }})</h3>
        <a href="{{ route('projects.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-xs">Add Project</a>
    </div>
    @if($client->projects->isEmpty())
    <div class="empty-state"><p>No projects for this client yet.</p></div>
    @else
    <table class="table">
        <thead><tr><th>Name</th><th>Status</th><th>Priority</th><th>Deadline</th><th></th></tr></thead>
        <tbody>
            @foreach($client->projects as $project)
            <tr>
                <td class="td-header"><a href="{{ route('projects.show', $project) }}" class="fw600 link">{{ $project->name }}</a></td>
                <td data-label="Status"><span class="badge badge-{{ $project->status }}">{{ str_replace('_',' ',$project->status) }}</span></td>
                <td data-label="Priority"><span class="badge badge-{{ $project->priority }}">{{ $project->priority }}</span></td>
                <td class="text-muted" data-label="Deadline">{{ $project->deadline ? $project->deadline->format('M d, Y') : '—' }}</td>
                <td class="td-actions"><a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-xs">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>

{{-- Invoices --}}
<div class="card">
    <div class="card-hd">
        <h3>Invoices ({{ $client->invoices->count() }})</h3>
        <a href="{{ route('invoices.create') }}?client_id={{ $client->id }}" class="btn btn-primary btn-xs">Add Invoice</a>
    </div>
    @if($client->invoices->isEmpty())
    <div class="empty-state"><p>No invoices for this client yet.</p></div>
    @else
    <table class="table">
        <thead><tr><th>Number</th><th>Amount</th><th>Status</th><th>Due Date</th><th></th></tr></thead>
        <tbody>
            @foreach($client->invoices as $invoice)
            <tr>
                <td class="td-header"><a href="{{ route('invoices.show', $invoice) }}" class="fw600 link">{{ $invoice->invoice_number }}</a></td>
                <td class="fw600" data-label="Amount">{!! format_currency($invoice->amount) !!}</td>
                <td data-label="Status"><span class="badge badge-{{ $invoice->status }}">{{ $invoice->status }}</span></td>
                <td class="text-muted" data-label="Due Date">{{ $invoice->due_date ? $invoice->due_date->format('M d, Y') : '—' }}</td>
                <td class="td-actions"><a href="{{ route('invoices.show', $invoice) }}" class="btn btn-secondary btn-xs">View</a></td>
            </tr>
            @endforeach
        </tbody>
    </table>
    @endif
</div>
@endsection
