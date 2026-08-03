@extends('layouts.app')
@section('title', 'Invitations')
@section('breadcrumb', 'Team / Invitations')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Invitations</h2>
        <p>Manage pending and accepted team invitations.</p>
    </div>
    <a href="{{ route('invitations.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Invite Member
    </a>
</div>

<form method="GET" class="filter-row mb-4">
    <input type="text" name="search" class="form-control" placeholder="Search by email or name…" value="{{ request('search') }}">
    <select name="status" class="form-control">
        <option value="">All Statuses</option>
        <option value="pending" {{ request('status')=='pending'?'selected':'' }}>Pending</option>
        <option value="accepted" {{ request('status')=='accepted'?'selected':'' }}>Accepted</option>
        <option value="expired" {{ request('status')=='expired'?'selected':'' }}>Expired</option>
    </select>
    <button type="submit" class="btn btn-secondary btn-sm">Filter</button>
    @if(request()->hasAny(['search','status']))
        <a href="{{ route('invitations.index') }}" class="btn btn-secondary btn-sm">Clear</a>
    @endif
</form>

@if(session('invite_link'))
<div style="background:rgba(108,99,255,0.1);border:1px solid rgba(108,99,255,0.3);border-radius:9px;padding:16px;margin-bottom:20px;">
    <div class="fw600" style="color:#a89fff;margin-bottom:6px;">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:middle;margin-right:4px;"><path d="M10 13a5 5 0 007.54.54l3-3a5 5 0 00-7.07-7.07l-1.72 1.71"/><path d="M14 11a5 5 0 00-7.54-.54l-3 3a5 5 0 007.07 7.07l1.71-1.71"/></svg>
        Invitation Link (share this with the invitee)
    </div>
    <div style="display:flex;gap:8px;align-items:center;">
        <input type="text" readonly value="{{ session('invite_link') }}" class="form-control" onclick="this.select()" style="font-size:0.8rem;font-family:monospace;">
        <button onclick="navigator.clipboard.writeText('{{ session('invite_link') }}');this.textContent='Copied!';setTimeout(()=>this.textContent='Copy',2000)" class="btn btn-primary btn-sm" style="white-space:nowrap;">Copy</button>
    </div>
</div>
@endif

<div class="card">
    <table class="table">
        <thead>
            <tr>
                <th>Email</th>
                <th>Name</th>
                <th>Roles</th>
                <th>Invited By</th>
                <th>Status</th>
                <th>Expires</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($invitations as $inv)
            <tr>
                <td class="fw600 td-header">{{ $inv->email }}</td>
                <td class="text-muted" data-label="Name">{{ $inv->name ?? '—' }}</td>
                <td data-label="Roles">
                    <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:flex-end;">
                        @foreach($inv->roles() as $role)
                            <span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $role->name }}</span>
                        @endforeach
                    </div>
                </td>
                <td class="text-muted" data-label="Invited By">{{ $inv->inviter->name }}</td>
                <td data-label="Status">
                    @if($inv->isAccepted())
                        <span class="badge badge-done">Accepted</span>
                    @elseif($inv->isExpired())
                        <span class="badge badge-cancelled">Expired</span>
                    @else
                        <span class="badge badge-sent">Pending</span>
                    @endif
                </td>
                <td class="text-muted text-xs" data-label="Expires">{{ $inv->expires_at?->format('M d, Y') ?? '—' }}</td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        @if(!$inv->isAccepted())
                        <a href="{{ route('invitations.accept', $inv->token) }}" target="_blank" class="btn btn-secondary btn-xs">Link</a>
                        <form method="POST" action="{{ route('invitations.destroy', $inv) }}" onsubmit="return confirm('Revoke this invitation?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Revoke</button>
                        </form>
                        @else
                        <form method="POST" action="{{ route('invitations.destroy', $inv) }}" onsubmit="return confirm('Delete this invitation record? This only removes the invite log — their account and access are not affected.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Delete</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @empty
            <tr><td colspan="7" class="td-empty">
                <div class="empty-state">
                    <p>No invitations yet. Invite your first team member.</p>
                    <a href="{{ route('invitations.create') }}" class="btn btn-primary btn-sm">Send Invitation</a>
                </div>
            </td></tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="pager">{{ $invitations->links() }}</div>
@endsection
