@extends('layouts.app')
@section('title', 'Roles & Permissions')
@section('breadcrumb', 'Team / Roles & Permissions')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Roles &amp; Permissions</h2>
        <p>Manage roles and the permissions each one grants.</p>
    </div>
    <a href="{{ route('roles.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        New Role
    </a>
</div>

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Role</th>
                <th>Description</th>
                <th>Permissions</th>
                <th>Members</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($roles as $role)
            <tr>
                <td class="fw600 td-header">{{ $role->name }}</td>
                <td class="text-muted" data-label="Description">{{ $role->description ?: '—' }}</td>
                <td data-label="Permissions"><span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $role->permissions_count }}</span></td>
                <td class="text-muted" data-label="Members">{{ $role->users_count }}</td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('roles.edit', $role) }}" class="btn btn-secondary btn-xs">Edit</a>
                        @if($role->name !== 'super-admin')
                        <form method="POST" action="{{ route('roles.destroy', $role) }}" onsubmit="return confirm('Delete this role?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="5" class="td-empty">
                <div class="empty-state"><p>No roles yet.</p></div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
