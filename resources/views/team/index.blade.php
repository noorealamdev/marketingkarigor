@extends('layouts.app')
@section('title', 'Team')
@section('breadcrumb', 'Team')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Team Members</h2>
        <p>{{ $members->count() }} members in your workspace</p>
    </div>
    <a href="{{ route('invitations.create') }}" class="btn btn-primary">
        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
        Invite Member
    </a>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(300px,1fr));gap:14px;margin-bottom:24px;">
    @foreach($members as $member)
    <div class="card" style="padding:20px;">
        <div class="row between" style="margin-bottom:14px;">
            <div class="row center" style="gap:12px;">
                {!! user_avatar($member, 40) !!}
                <div>
                    <div class="fw700" style="font-size:0.9rem;">{{ $member->name }}</div>
                    <div class="text-xs text-muted">{{ $member->email }}</div>
                </div>
            </div>
            <a href="{{ route('team.show', $member) }}" class="btn btn-secondary btn-xs">View</a>
        </div>

        <div style="display:flex;flex-wrap:wrap;gap:4px;margin-bottom:14px;">
            @forelse($member->roles as $role)
                <span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $role->name }}</span>
            @empty
                <span class="text-xs text-faint">No roles assigned</span>
            @endforelse
        </div>

        <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:8px;text-align:center;padding-top:12px;border-top:1px solid #252936;">
            <div>
                <div class="fw700" style="font-size:1.1rem;color:#4ade80;">{{ $member->completed_tasks_count }}</div>
                <div class="text-xs text-muted">Done</div>
            </div>
            <div>
                <div class="fw700" style="font-size:1.1rem;color:#60a5fa;">{{ $member->active_tasks_count }}</div>
                <div class="text-xs text-muted">Active</div>
            </div>
            <div>
                <div class="fw700" style="font-size:1.1rem;">{{ $member->assigned_tasks_count }}</div>
                <div class="text-xs text-muted">Total</div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<div class="card">
    <div class="card-hd"><h3>Team at a Glance</h3></div>
    <table class="table">
        <thead>
            <tr>
                <th>Member</th>
                <th>Roles</th>
                <th>Tasks Done</th>
                <th>Active Tasks</th>
                <th>Total Tasks</th>
                <th></th>
            </tr>
        </thead>
        <tbody>
            @foreach($members as $member)
            <tr>
                <td class="td-header">
                    <div class="fw600">{{ $member->name }}</div>
                    <div class="text-xs text-muted">{{ $member->email }}</div>
                </td>
                <td data-label="Roles">
                    <div style="display:flex;flex-wrap:wrap;gap:4px;justify-content:flex-end;">
                        @forelse($member->roles as $role)
                            <span class="badge" style="background:rgba(108,99,255,0.12);color:#a89fff;">{{ $role->name }}</span>
                        @empty
                            <span class="text-xs text-faint">—</span>
                        @endforelse
                    </div>
                </td>
                <td data-label="Tasks Done"><span class="fw600" style="color:#4ade80;">{{ $member->completed_tasks_count }}</span></td>
                <td data-label="Active Tasks"><span class="fw600" style="color:#60a5fa;">{{ $member->active_tasks_count }}</span></td>
                <td class="text-muted" data-label="Total Tasks">{{ $member->assigned_tasks_count }}</td>
                <td class="td-actions">
                    <div class="row" style="gap:4px;">
                        <a href="{{ route('team.show', $member) }}" class="btn btn-secondary btn-xs">Stats →</a>
                        @if($member->id !== auth()->id())
                        <form method="POST" action="{{ route('team.destroy', $member) }}" onsubmit="return confirm('Remove {{ addslashes($member->name) }} from the team? This cannot be undone.')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">Remove</button>
                        </form>
                        @endif
                    </div>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
