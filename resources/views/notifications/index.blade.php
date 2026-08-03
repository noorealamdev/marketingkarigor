@extends('layouts.app')
@section('title', 'Notifications')
@section('breadcrumb', 'Notifications')

@section('content')
<div class="page-hd row between" style="margin-bottom:20px;">
    <div>
        <h2>Notifications</h2>
        <p class="text-sm text-muted">{{ auth()->user()->unreadNotifications->count() }} unread</p>
    </div>
    @if($notifications->count())
    <form method="POST" action="{{ route('notifications.mark-all-read') }}">
        @csrf
        <button type="submit" class="btn btn-secondary btn-sm">Mark all read</button>
    </form>
    @endif
</div>

@if($notifications->isEmpty())
<div class="card">
    <div style="padding:60px 20px;text-align:center;color:#4a5068;">
        <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2" style="margin:0 auto 14px;display:block;opacity:0.3;">
            <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9M13.73 21a2 2 0 01-3.46 0"/>
        </svg>
        <p style="font-size:1rem;font-weight:600;color:#6b7590;margin-bottom:6px;">All caught up!</p>
        <p style="font-size:0.84rem;">No notifications yet.</p>
    </div>
</div>
@else
<div style="display:flex;flex-direction:column;gap:6px;">
    @foreach($notifications as $n)
    @php $d = $n->data; $read = !is_null($n->read_at); @endphp
    <div style="background:{{ $read ? '#13161d' : '#16192a' }};border:1px solid {{ $read ? '#252936' : '#2d3560' }};border-radius:10px;padding:14px 16px;display:flex;align-items:flex-start;gap:12px;">
        <div style="width:36px;height:36px;border-radius:50%;background:{{ $read ? '#1a1e28' : 'rgba(108,99,255,0.2)' }};display:flex;align-items:center;justify-content:center;flex-shrink:0;font-weight:700;font-size:0.85rem;color:{{ $read ? '#4a5068' : '#6c63ff' }};">
            {{ $d['actor_initial'] ?? '?' }}
        </div>
        <div style="flex:1;min-width:0;">
            <div style="font-size:0.875rem;font-weight:{{ $read ? '400' : '600' }};color:{{ $read ? '#6b7590' : '#c8cce0' }};margin-bottom:3px;">
                {{ $d['title'] ?? 'Notification' }}
            </div>
            @if(!empty($d['body']))
            <div style="font-size:0.8rem;color:#4a5068;margin-bottom:5px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $d['body'] }}</div>
            @endif
            <div style="font-size:0.72rem;color:#3a4060;">{{ $n->created_at->diffForHumans() }}</div>
        </div>
        <div style="display:flex;gap:4px;flex-shrink:0;">
            @if(!empty($d['url']))
            <a href="{{ route('notifications.read', $n->id) }}" class="btn btn-secondary btn-xs">View</a>
            @endif
            <form method="POST" action="{{ route('notifications.destroy', $n->id) }}">
                @csrf @method('DELETE')
                <button class="btn btn-danger btn-xs" title="Delete">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/></svg>
                </button>
            </form>
        </div>
    </div>
    @endforeach
</div>

@if($notifications->hasPages())
<div class="pager" style="margin-top:16px;">{{ $notifications->links() }}</div>
@endif
@endif
@endsection
