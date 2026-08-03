@extends('layouts.app')
@section('title', 'Clients')
@section('breadcrumb', 'Clients')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Clients</h2>
        <p>{{ $clients->total() }} total clients</p>
    </div>
    <a href="{{ route('clients.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Add Client
    </a>
</div>

<form method="GET" class="filter-row mb4">
    <input type="text" name="search" class="form-control" placeholder="Search by name, company, email, or Client ID…" value="{{ request('search') }}">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <option value="active" {{ request('status')=='active'?'selected':'' }}>Active</option>
        <option value="inactive" {{ request('status')=='inactive'?'selected':'' }}>Inactive</option>
        <option value="prospect" {{ request('status')=='prospect'?'selected':'' }}>Prospect</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request('search') || request('status'))
        <a href="{{ route('clients.index') }}" class="btn btn-secondary btn-sm">Clear</a>
    @endif
</form>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Name</th>
                <th>Company</th>
                <th>Email</th>
                <th>Phone</th>
                <th>Projects</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($clients as $client)
            <tr>
                <td class="td-header">
                    <a href="{{ route('clients.show', $client) }}" class="fw600 link">{{ $client->name }}</a>
                    <span class="text-faint text-xs">#{{ $client->id }}</span>
                </td>
                <td class="text-muted" data-label="Company">{{ $client->company ?: '—' }}</td>
                <td class="text-muted" data-label="Email">{{ $client->email ?: '—' }}</td>
                <td class="text-muted" data-label="Phone">{{ $client->phone ?: '—' }}</td>
                <td data-label="Projects"><span class="badge badge-todo">{{ $client->projects_count }}</span></td>
                <td data-label="Status"><span class="badge badge-{{ $client->status }}">{{ $client->status }}</span></td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary btn-xs">View</a>
                        <a href="{{ route('clients.edit', $client) }}" class="btn btn-secondary btn-xs">Edit</a>
                        <form method="POST" action="{{ route('clients.destroy', $client) }}" onsubmit="return confirm('Delete this client?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="td-empty">
                <div class="empty-state">
                    <p>No clients found. Add your first client to get started.</p>
                    <a href="{{ route('clients.create') }}" class="btn btn-primary btn-sm">Add Client</a>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>

@if($clients->hasPages())
<div class="pager">{{ $clients->links() }}</div>
@endif
@endsection
