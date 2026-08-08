@extends('layouts.portal')
@section('title', 'Notifications')

@section('content')
<div class="page-hd">
    <h2>Notifications</h2>
    <p>Updates about your content, reports, and account.</p>
</div>

<div class="card">
    @forelse($notifications as $n)
        @php $d = $n->data; @endphp
        <a href="{{ $d['url'] ?? '#' }}" class="notif-item" @if(!$n->read_at) style="background:rgba(242,183,5,0.05);" @endif>
            <div class="notif-item-avatar">{{ $d['actor_initial'] ?? '•' }}</div>
            <div style="flex:1;min-width:0;">
                <div class="notif-item-title">{{ $d['title'] ?? 'Notification' }}</div>
                @if(!empty($d['body']))
                <div class="text-xs text-muted" style="margin-top:2px;">{{ $d['body'] }}</div>
                @endif
                <div class="notif-item-time">{{ $n->created_at->diffForHumans() }}</div>
            </div>
            @if(!$n->read_at)
            <span class="badge" style="background:rgba(242,183,5,0.15);color:#f2b705;flex-shrink:0;">New</span>
            @endif
        </a>
    @empty
        <div class="empty-state"><p>No notifications yet.</p></div>
    @endforelse
</div>

@if($notifications->hasPages())
<div class="pager">{{ $notifications->links() }}</div>
@endif
@endsection
