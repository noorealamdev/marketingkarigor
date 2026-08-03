@extends('layouts.app')
@section('title', $task->name)
@section('breadcrumb', 'Tasks / ' . $task->name)

@push('styles')
<style>
.assignee-hero { display:flex;align-items:center;gap:14px;padding:16px;background:rgba(108,99,255,0.06);border:1px solid rgba(108,99,255,0.2);border-radius:10px;margin-bottom:4px; }
.assignee-avatar { width:48px;height:48px;border-radius:50%;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:1.1rem;color:#fff;flex-shrink:0; }
.assignee-avatar.unassigned { background:#1a1e28;color:#4a5068; }
.comment-list { display:flex;flex-direction:column;gap:10px; }
.comment-item { display:flex;gap:10px;align-items:flex-start; }
.comment-avatar { width:32px;height:32px;border-radius:50%;background:#1a1e28;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#6c63ff;flex-shrink:0; }
.comment-bubble { flex:1;background:#1a1e28;border:1px solid #252936;border-radius:10px;padding:10px 12px; }
.comment-meta { display:flex;align-items:center;justify-content:space-between;margin-bottom:5px; }
.comment-author { font-size:0.8rem;font-weight:600;color:#c8cce0; }
.comment-time { font-size:0.7rem;color:#4a5068; }
.comment-body { font-size:0.845rem;color:#a0a6be;line-height:1.55;white-space:pre-line; }
.comment-form { margin-top:14px; }
.comment-textarea { width:100%;background:#1a1e28;border:1px solid #252936;border-radius:8px;padding:10px 12px;color:#c8cce0;font-size:0.845rem;resize:vertical;min-height:80px;font-family:inherit;transition:border-color 0.12s; }
.comment-textarea:focus { outline:none;border-color:#6c63ff; }

/* Attachments */
.attach-drop { border:2px dashed #252936;border-radius:10px;padding:22px 16px;text-align:center;cursor:pointer;transition:all 0.15s;background:#0e1017; }
.attach-drop:hover, .attach-drop.drag-over { border-color:#6c63ff;background:rgba(108,99,255,0.05); }
.attach-drop p { font-size:0.82rem;color:#4a5068;margin:6px 0 0; }
.attach-drop strong { color:#6c63ff; }
.attach-list { display:flex;flex-direction:column;gap:6px;margin-top:12px; }
.attach-item { display:flex;align-items:center;gap:10px;padding:8px 10px;background:#0e1017;border:1px solid #252936;border-radius:8px;transition:border-color 0.12s; }
.attach-item:hover { border-color:#3a4060; }
.attach-thumb { width:40px;height:40px;border-radius:6px;overflow:hidden;flex-shrink:0;background:#1a1e28;display:flex;align-items:center;justify-content:center; }
.attach-thumb img { width:100%;height:100%;object-fit:cover; }
.attach-ext { font-size:0.65rem;font-weight:800;font-family:monospace;letter-spacing:-0.03em; }
.attach-name { flex:1;min-width:0; }
.attach-name .name { font-size:0.82rem;color:#c8cce0;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;font-weight:500; }
.attach-name .meta { font-size:0.7rem;color:#4a5068;margin-top:1px; }
.attach-actions { display:flex;gap:4px;flex-shrink:0; }
.upload-progress-bar { height:3px;background:#1a1e28;border-radius:2px;margin-top:8px;overflow:hidden;display:none; }
.upload-progress-fill { height:100%;background:#6c63ff;border-radius:2px;transition:width 0.2s; }
/* Comment link + YouTube */
.comment-body a.link { color:#6c63ff; }
.comment-body a.link:hover { text-decoration:underline; }
.yt-embed-wrap { position:relative;width:100%;padding-top:56.25%;margin-top:8px;border-radius:8px;overflow:hidden;background:#000; }
.yt-embed-wrap iframe { position:absolute;top:0;left:0;width:100%;height:100%;border:0; }
/* Description links */
.desc-field a.desc-link { color:#6c63ff;text-decoration:none; }
.desc-field a.desc-link:hover { text-decoration:underline; }
/* Emoji picker */
.emoji-picker-wrap { position:relative;display:inline-block; }
.emoji-picker-btn { background:#1a1e28;border:1px solid #252936;border-radius:6px;padding:5px 9px;cursor:pointer;font-size:1rem;line-height:1;transition:border-color 0.12s; }
.emoji-picker-btn:hover { border-color:#6c63ff; }
.emoji-panel { display:none;position:absolute;bottom:calc(100% + 6px);left:0;z-index:300;background:#1a1e28;border:1px solid #252936;border-radius:10px;padding:10px;width:284px;box-shadow:0 8px 28px rgba(0,0,0,0.6); }
.emoji-panel.open { display:block; }
.emoji-panel-grid { display:flex;flex-wrap:wrap;gap:1px; }
.emoji-panel-grid button { background:none;border:none;font-size:1.25rem;padding:4px 5px;border-radius:5px;cursor:pointer;line-height:1;transition:background 0.1s; }
.emoji-panel-grid button:hover { background:rgba(108,99,255,0.18); }
/* Inline images in comments */
.comment-img-wrap { display:block;margin-top:10px; }
.comment-img { max-width:100%;max-height:320px;border-radius:8px;border:1px solid #252936;display:block;cursor:zoom-in;transition:opacity 0.15s; }
.comment-img:hover { opacity:0.9; }
.comment-attachments { display:flex;flex-direction:column;gap:8px;margin-top:10px; }
.comment-file-row { display:flex;align-items:center;gap:6px;font-size:0.8rem;color:#a0a6be; }
.comment-video { max-width:100%;max-height:320px;border-radius:8px;border:1px solid #252936;display:block;margin-top:10px;background:#000; }
/* Pending attachment previews (before posting) */
.comment-attach-btn { background:#1a1e28;border:1px solid #252936;border-radius:6px;padding:5px 9px;cursor:pointer;font-size:0.8rem;color:#6b7590;line-height:1.3;transition:border-color 0.12s;display:inline-flex;align-items:center;gap:5px; }
.comment-attach-btn:hover { border-color:#6c63ff;color:#c8cce0; }
.comment-attach-preview { display:flex;flex-wrap:wrap;gap:8px; }
.comment-attach-preview:not(:empty) { margin-top:10px; }
.comment-attach-chip { position:relative;width:64px;height:64px;border-radius:8px;overflow:hidden;background:#0e1017;border:1px solid #252936;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
.comment-attach-chip img, .comment-attach-chip video { width:100%;height:100%;object-fit:cover; }
.comment-attach-chip .attach-play { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;background:rgba(0,0,0,0.25);pointer-events:none; }
.comment-attach-chip .attach-generic { font-size:0.58rem;font-weight:700;color:#6c63ff;text-align:center;padding:4px;word-break:break-all;line-height:1.3; }
.comment-attach-chip .attach-remove { position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;background:rgba(0,0,0,0.7);color:#fff;border:none;font-size:12px;line-height:1;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0; }
.comment-attach-chip .attach-remove:hover { background:rgba(248,113,113,0.9); }
.comment-form.drag-over .comment-textarea { border-color:#6c63ff;background:rgba(108,99,255,0.05); }
/* Share-file dropdown */
.sf-dropdown { position:relative;display:inline-block; }
.sf-menu { display:none;position:absolute;bottom:calc(100% + 6px);left:0;z-index:300;background:#1a1e28;border:1px solid #252936;border-radius:10px;padding:5px;min-width:210px;max-height:240px;overflow-y:auto;box-shadow:0 8px 28px rgba(0,0,0,0.55); }
.sf-menu.open { display:block; }
.sf-menu button { display:flex;align-items:center;gap:8px;width:100%;padding:7px 10px;border-radius:6px;font-size:0.8rem;color:#c8cce0;background:none;border:none;cursor:pointer;text-align:left;transition:background 0.1s;white-space:nowrap;overflow:hidden;text-overflow:ellipsis; }
.sf-menu button:hover { background:rgba(108,99,255,0.1);color:#a78bfa; }
.sf-menu .sf-empty { padding:12px 10px;font-size:0.8rem;color:#4a5068;text-align:center; }
.sf-btn { background:#1a1e28;border:1px solid #252936;border-radius:6px;padding:5px 9px;cursor:pointer;font-size:0.8rem;color:#6b7590;line-height:1.3;transition:border-color 0.12s;display:inline-flex;align-items:center;gap:5px; }
.sf-btn:hover { border-color:#6c63ff;color:#c8cce0; }
/* Share dropdown */
.share-dropdown { position:relative;display:inline-block; }
.share-menu { display:none;position:absolute;top:calc(100% + 6px);right:0;z-index:300;background:#1a1e28;border:1px solid #252936;border-radius:10px;padding:5px;min-width:185px;box-shadow:0 8px 28px rgba(0,0,0,0.6); }
.share-menu.open { display:block; }
.share-menu a, .share-menu button { display:flex;align-items:center;gap:9px;width:100%;padding:7px 10px;border-radius:6px;font-size:0.82rem;color:#c8cce0;background:none;border:none;cursor:pointer;text-decoration:none;transition:background 0.1s;white-space:nowrap; }
.share-menu a:hover, .share-menu button:hover { background:rgba(108,99,255,0.1);color:#a78bfa; }
/* Toast */
#copyToast { position:fixed;bottom:24px;left:50%;transform:translateX(-50%);background:#6c63ff;color:#fff;padding:8px 20px;border-radius:8px;font-size:0.82rem;font-weight:600;z-index:999;opacity:0;transition:opacity 0.2s;pointer-events:none; }
</style>
@endpush

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $task->name }}</h2>
        <div class="row center" style="gap:8px;margin-top:4px;">
            <span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span>
            <span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span>
            @if($task->due_date?->isPast() && $task->status !== \App\Enums\TaskStatus::Done->value)
                <span style="font-size:0.75rem;color:#f87171;font-weight:600;">⚠ Overdue</span>
            @endif
            @php $clientLabel = $task->clientApprovalLabel(); @endphp
            @if($clientLabel === 'Approved')
                <span class="badge badge-done">✓ Client Approved</span>
            @elseif($clientLabel)
                <span class="badge badge-review">{{ $clientLabel }}</span>
            @endif
        </div>
    </div>
    <div class="row" style="gap:6px;">
        <a href="{{ route('tasks.edit', $task) }}" class="btn btn-secondary">Edit</a>
        @if(auth()->user()->hasAnyRole(['super-admin', 'project-manager']))
        @if($task->project?->client)
        <form method="POST" action="{{ route('tasks.share-with-client', $task) }}" onsubmit="return confirm('Share this content with the client for approval?')">
            @csrf
            <button type="submit" class="btn btn-secondary">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;margin-right:4px;"><line x1="22" y1="2" x2="11" y2="13"/><polygon points="22 2 15 22 11 13 2 9 22 2"/></svg>
                {{ $task->shared_with_client_at ? 'Re-share with Client' : 'Share with Client' }}
            </button>
        </form>
        @endif
        <form method="POST" action="{{ route('tasks.destroy', $task) }}" onsubmit="return confirm('Delete this task?')">
            @csrf @method('DELETE')
            <button type="submit" class="btn btn-danger">Delete</button>
        </form>
        @endif
        <div class="share-dropdown" id="shareDropdown">
            <button class="btn btn-secondary" onclick="toggleShare(event)" title="Share this task">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;margin-right:4px;"><circle cx="18" cy="5" r="3"/><circle cx="6" cy="12" r="3"/><circle cx="18" cy="19" r="3"/><line x1="8.59" y1="13.51" x2="15.42" y2="17.49"/><line x1="15.41" y1="6.51" x2="8.59" y2="10.49"/></svg>
                Share
            </button>
            <div class="share-menu" id="shareMenu">
                <button onclick="copyTaskLink()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2"/><path d="M5 15H4a2 2 0 01-2-2V4a2 2 0 012-2h9a2 2 0 012 2v1"/></svg>
                    Copy Link
                </button>
                <a href="https://api.whatsapp.com/send?text={{ urlencode($task->name . "\n" . route('tasks.show', $task)) }}" target="_blank" rel="noopener" onclick="closeShare()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/></svg>
                    WhatsApp
                </a>
                <a href="{{ 'https://t.me/share/url?url=' . urlencode(route('tasks.show', $task)) . '&text=' . urlencode($task->name) }}" target="_blank" rel="noopener" onclick="closeShare()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M11.944 0A12 12 0 0 0 0 12a12 12 0 0 0 12 12 12 12 0 0 0 12-12A12 12 0 0 0 12 0a12 12 0 0 0-.056 0zm4.962 7.224c.1-.002.321.023.465.14a.506.506 0 0 1 .171.325c.016.093.036.306.02.472-.18 1.898-.962 6.502-1.36 8.627-.168.9-.499 1.201-.82 1.23-.696.065-1.225-.46-1.9-.902-1.056-.693-1.653-1.124-2.678-1.8-1.185-.78-.417-1.21.258-1.91.177-.184 3.247-2.977 3.307-3.23.007-.032.014-.15-.056-.212s-.174-.041-.249-.024c-.106.024-1.793 1.14-5.061 3.345-.48.33-.913.49-1.302.48-.428-.008-1.252-.241-1.865-.44-.752-.245-1.349-.374-1.297-.789.027-.216.325-.437.893-.663 3.498-1.524 5.83-2.529 6.998-3.014 3.332-1.386 4.025-1.627 4.476-1.635z"/></svg>
                    Telegram
                </a>
                <a href="mailto:?subject={{ urlencode('Task: ' . $task->name) }}&body={{ urlencode(route('tasks.show', $task)) }}" onclick="closeShare()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                    Email
                </a>
                <button onclick="copyForSlack()">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="currentColor"><path d="M5.042 15.165a2.528 2.528 0 0 1-2.52 2.523A2.528 2.528 0 0 1 0 15.165a2.527 2.527 0 0 1 2.522-2.52h2.52v2.52zM6.313 15.165a2.527 2.527 0 0 1 2.521-2.52 2.527 2.527 0 0 1 2.521 2.52v6.313A2.528 2.528 0 0 1 8.834 24a2.528 2.528 0 0 1-2.521-2.522v-6.313zM8.834 5.042a2.528 2.528 0 0 1-2.521-2.52A2.528 2.528 0 0 1 8.834 0a2.528 2.528 0 0 1 2.521 2.522v2.52H8.834zM8.834 6.313a2.528 2.528 0 0 1 2.521 2.521 2.528 2.528 0 0 1-2.521 2.521H2.522A2.528 2.528 0 0 1 0 8.834a2.528 2.528 0 0 1 2.522-2.521h6.312zM18.956 8.834a2.528 2.528 0 0 1 2.522-2.521A2.528 2.528 0 0 1 24 8.834a2.528 2.528 0 0 1-2.522 2.521h-2.522V8.834zM17.688 8.834a2.528 2.528 0 0 1-2.523 2.521 2.527 2.527 0 0 1-2.52-2.521V2.522A2.527 2.527 0 0 1 15.165 0a2.528 2.528 0 0 1 2.523 2.522v6.312zM15.165 18.956a2.528 2.528 0 0 1 2.523 2.522A2.528 2.528 0 0 1 15.165 24a2.527 2.527 0 0 1-2.52-2.522v-2.522h2.52zM15.165 17.688a2.527 2.527 0 0 1-2.52-2.523 2.526 2.526 0 0 1 2.52-2.52h6.313A2.527 2.527 0 0 1 24 15.165a2.528 2.528 0 0 1-2.522 2.523h-6.313z"/></svg>
                    Copy for Slack
                </button>
            </div>
        </div>
    </div>
</div>

<div class="g2" style="align-items:start;">
    {{-- LEFT: Details + Comments --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- Task Details --}}
        <div class="card">
            <div class="card-hd"><h3>Task Details</h3></div>
            <div class="card-bd">
                <div style="font-size:0.72rem;color:#4a5068;text-transform:uppercase;letter-spacing:0.08em;font-weight:600;margin-bottom:6px;">Assigned To</div>
                <div class="assignee-hero" style="flex-wrap:wrap;">
                    @forelse($task->assignees as $assignee)
                        <div style="display:flex;align-items:center;gap:10px;">
                            {!! user_avatar($assignee, 40, 'assignee-avatar') !!}
                            <div>
                                <div style="font-size:0.9rem;font-weight:700;color:#c8cce0;">{{ $assignee->name }}</div>
                                <div style="font-size:0.72rem;color:#6b7590;">{{ $assignee->roles->pluck('name')->join(', ') ?: 'Member' }}</div>
                            </div>
                        </div>
                    @empty
                        <div class="assignee-avatar unassigned">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 21v-2a4 4 0 00-4-4H8a4 4 0 00-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>
                        </div>
                        <div style="font-size:0.9rem;color:#4a5068;font-style:italic;">Unassigned</div>
                    @endforelse
                </div>

                <div class="detail-row" style="margin-top:14px;">
                    <span class="detail-label">Status</span>
                    <span class="detail-value"><span class="badge badge-{{ $task->status }}">{{ str_replace('_',' ',$task->status) }}</span></span>
                    <span class="detail-label">Priority</span>
                    <span class="detail-value"><span class="badge badge-{{ $task->priority }}">{{ $task->priority }}</span></span>
                    <span class="detail-label">Due Date</span>
                    <span class="detail-value" style="{{ $task->due_date?->isPast() && $task->status!=\App\Enums\TaskStatus::Done->value ? 'color:#f87171' : '' }}">
                        {{ $task->due_date ? $task->due_date->format('M d, Y') : '—' }}
                    </span>
                    <span class="detail-label">Project</span>
                    <span class="detail-value">
                        @if($task->project)
                            <a href="{{ route('projects.show', $task->project) }}" class="link">{{ $task->project->name }}</a>
                        @else
                            <span class="text-muted">No project</span>
                        @endif
                    </span>
                    @if($task->project?->client)
                    <span class="detail-label">Client</span>
                    <span class="detail-value">
                        <a href="{{ route('clients.show', $task->project->client) }}" class="link">{{ $task->project->client->name }}</a>
                    </span>
                    @endif
                    <span class="detail-label">Created</span>
                    <span class="detail-value">{{ $task->created_at->format('M d, Y') }}</span>
                </div>

                @if($task->description)
                <div style="margin-top:14px;padding-top:14px;border-top:1px solid #252936;">
                    <div style="font-size:0.72rem;color:#4a5068;text-transform:uppercase;letter-spacing:0.08em;font-weight:600;margin-bottom:6px;">Description</div>
                    <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;" class="desc-field">{!! format_description($task->description) !!}</p>
                </div>
                @endif
            </div>
        </div>

        {{-- Comments --}}
        <div class="card">
            <div class="card-hd">
                <h3>Comments <span style="font-size:0.8rem;color:#4a5068;font-weight:400;">({{ $task->comments->count() }})</span></h3>
            </div>
            <div class="card-bd">
                @if($task->comments->isNotEmpty())
                <div class="comment-list">
                    @foreach($task->comments as $comment)
                    <div class="comment-item">
                        {!! user_avatar($comment->author, 32, 'comment-avatar') !!}
                        <div class="comment-bubble">
                            <div class="comment-meta">
                                <div class="row center" style="gap:6px;">
                                    <span class="comment-author">{{ $comment->author->name }}</span>
                                    @if($comment->author->hasRole('client'))
                                    <span class="badge badge-client">client</span>
                                    @elseif($comment->author->roles->isNotEmpty())
                                    <span class="badge badge-role">{{ $comment->author->roles->pluck('name')->join(', ') }}</span>
                                    @endif
                                </div>
                                <div class="row center" style="gap:6px;">
                                    <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                                    @if($comment->user_id === auth()->id())
                                    <form method="POST" action="{{ route('tasks.comments.destroy', [$task, $comment]) }}">
                                        @csrf @method('DELETE')
                                        <button class="btn btn-danger btn-xs" style="padding:2px 6px;" title="Delete">×</button>
                                    </form>
                                    @endif
                                </div>
                            </div>
                            <div class="comment-body">{!! format_comment($comment->body) !!}</div>
                            @php $cFiles = $comment->getMedia('comment_files'); @endphp
                            @if($cFiles->count())
                            <div class="comment-attachments">
                                @foreach($cFiles as $cf)
                                    @php
                                        $cfIsImg   = str_starts_with($cf->mime_type ?? '', 'image/');
                                        $cfIsVideo = str_starts_with($cf->mime_type ?? '', 'video/');
                                        $cfUrl     = rebase_media_url($cf->getUrl());
                                        $cfSize    = $cf->size < 1048576
                                            ? number_format($cf->size / 1024, 0) . ' KB'
                                            : number_format($cf->size / 1048576, 1) . ' MB';
                                    @endphp
                                    @if($cfIsImg)
                                        <a href="{{ $cfUrl }}" target="_blank" rel="noopener">
                                            <img src="{{ $cfUrl }}" class="comment-img" alt="{{ $cf->file_name }}" loading="lazy">
                                        </a>
                                    @elseif($cfIsVideo)
                                        <video src="{{ $cfUrl }}" class="comment-video" controls preload="metadata"></video>
                                    @else
                                        <div class="comment-file-row">
                                            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#6c63ff"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                            <a href="{{ $cfUrl }}" target="_blank" rel="noopener" class="link">{{ $cf->file_name }}</a>
                                            <span class="text-faint" style="font-size:0.7rem;">{{ $cfSize }}</span>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
                @else
                <p class="text-sm text-muted" style="margin-bottom:14px;">No comments yet. Be the first to comment.</p>
                @endif

                <form method="POST" action="{{ route('tasks.comments.store', $task) }}" class="comment-form" id="commentForm" enctype="multipart/form-data">
                    @csrf
                    <div class="comment-item" style="align-items:flex-start;">
                        {!! user_avatar(auth()->user(), 32, 'comment-avatar', 'margin-top:2px;') !!}
                        <div style="flex:1;">
                            <textarea id="commentBody" name="body" class="comment-textarea" placeholder="Write a comment…">{{ old('body') }}</textarea>
                            @error('body')<div style="font-size:0.75rem;color:#f87171;margin-top:3px;">{{ $message }}</div>@enderror
                            <div class="comment-attach-preview" id="commentAttachPreview"></div>
                            <input type="file" id="commentFilesInput" name="comment_files[]" multiple accept="image/*,video/*" style="display:none">
                            @if($task->project?->client)
                            <label style="display:flex;align-items:center;gap:6px;margin-top:8px;font-size:0.78rem;color:#6b7590;cursor:pointer;">
                                <input type="checkbox" name="visible_to_client" value="1" style="accent-color:#6c63ff;">
                                Visible to client
                            </label>
                            @endif
                            <div style="display:flex;align-items:center;gap:8px;margin-top:8px;flex-wrap:wrap;">
                                <button type="submit" class="btn btn-primary btn-sm" id="commentSubmitBtn">Post Comment</button>
                                <button type="button" class="comment-attach-btn" id="commentAttachBtn" title="Attach images or videos">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                    Attach
                                </button>
                                <div class="emoji-picker-wrap">
                                    <button type="button" class="emoji-picker-btn" onclick="toggleEmojiPanel(event)" title="Insert emoji">😊</button>
                                    <div class="emoji-panel" id="emojiPanel">
                                        <div class="emoji-panel-grid">
                                            @foreach(['😊','😂','🤣','❤️','😍','🥰','😘','😎','👍','👎','👏','🎉','🚀','🔥','💯','✅','⭐','🙏','💪','👀','🤔','😅','🥳','🤩','😭','🫡','🎯','💡','📌','⚡','🌟','💥','🎊','🏆','😤','🤦','🙈','💀','👋','🤝','✨','🫶','🥹','⚠️','🔗'] as $pickerEmoji)
                                            <button type="button" onclick="insertEmoji('{{ $pickerEmoji }}')">{{ $pickerEmoji }}</button>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                {{-- Share file from task attachments --}}
                                @php $shareableFiles = $task->getMedia('attachments'); @endphp
                                <div class="sf-dropdown" id="sfDropdown">
                                    <button type="button" class="sf-btn" onclick="toggleSfMenu(event)" title="Share a file from this task's attachments">
                                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                        Share file
                                    </button>
                                    <div class="sf-menu" id="sfMenu">
                                        @if($shareableFiles->isEmpty())
                                            <div class="sf-empty">No attachments yet</div>
                                        @else
                                            @foreach($shareableFiles as $sf)
                                            @php
                                                $sfExt    = strtolower(pathinfo($sf->file_name, PATHINFO_EXTENSION));
                                                $sfUrl    = rebase_media_url($sf->getUrl());
                                                $sfName   = $sf->file_name;
                                                $sfIsImg  = str_starts_with($sf->mime_type ?? '', 'image/');
                                            @endphp
                                            <button type="button" onclick="shareFile({{ json_encode($sfName) }}, {{ json_encode($sfUrl) }})">
                                                <span style="font-size:0.65rem;font-weight:800;font-family:monospace;color:#6c63ff;background:#0e1017;padding:1px 4px;border-radius:3px;flex-shrink:0;">{{ strtoupper($sfExt) ?: 'FILE' }}</span>
                                                {{ $sfName }}
                                            </button>
                                            @endforeach
                                        @endif
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- RIGHT: Attachments + Status + Assignee --}}
    <div style="display:flex;flex-direction:column;gap:14px;">

        {{-- ── ATTACHMENTS ── --}}
        <div class="card">
            <div class="card-hd">
                <h3>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    Attachments
                    <span style="font-size:0.78rem;color:#4a5068;font-weight:400;" id="attachCount">({{ $task->getMedia('attachments')->count() }})</span>
                </h3>
                <label for="attachInput" class="btn btn-secondary btn-xs" style="cursor:pointer;">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="12" y1="5" x2="12" y2="19"/><line x1="5" y1="12" x2="19" y2="12"/></svg>
                    Add
                </label>
            </div>
            <div class="card-bd" style="padding-top:10px;">

                {{-- Drop zone --}}
                <div class="attach-drop" id="attachDrop" onclick="document.getElementById('attachInput').click()">
                    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="#3a4060" stroke-width="1.5" style="margin:0 auto;display:block;">
                        <path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/>
                    </svg>
                    <p><strong>Click</strong> or drag files here</p>
                    <p style="font-size:0.72rem;margin-top:3px;">Any file type · up to 100 MB each</p>
                </div>
                <input type="file" id="attachInput" multiple style="display:none">

                <div class="upload-progress-bar" id="uploadProgressBar">
                    <div class="upload-progress-fill" id="uploadProgressFill" style="width:0%"></div>
                </div>
                <div id="uploadStatus" style="font-size:0.75rem;color:#4a5068;margin-top:4px;display:none;"></div>

                {{-- Existing attachments --}}
                @php $attachments = $task->getMedia('attachments'); @endphp
                <div class="attach-list" id="attachList">
                    @foreach($attachments as $att)
                    @php
                        $mime  = $att->mime_type;
                        $ext   = strtolower(pathinfo($att->file_name, PATHINFO_EXTENSION));
                        $isImg = str_starts_with($mime, 'image/');
                        $sizeMb = $att->size < 1048576
                            ? number_format($att->size / 1024, 0) . ' KB'
                            : number_format($att->size / 1048576, 1) . ' MB';
                        $extColor = match($ext) {
                            'pdf'         => '#f87171',
                            'blend'       => '#fb923c',
                            'psd','ai'    => '#a78bfa',
                            'doc','docx'  => '#60a5fa',
                            'xls','xlsx'  => '#4ade80',
                            'mp4','mov','avi','webm' => '#fbbf24',
                            'zip','rar'   => '#f472b6',
                            'fig'         => '#a78bfa',
                            default       => '#6b7590',
                        };
                        $canDelete = $att->getCustomProperty('uploaded_by') == auth()->id() || auth()->user()->isAdmin();
                    @endphp
                    <div class="attach-item" id="attach-{{ $att->id }}">
                        <div class="attach-thumb">
                            @if($isImg && $att->hasGeneratedConversion('thumb'))
                                <img src="{{ rebase_media_url($att->getUrl('thumb')) }}" alt="{{ $att->name }}" loading="lazy">
                            @elseif($isImg)
                                <img src="{{ rebase_media_url($att->getUrl()) }}" alt="{{ $att->name }}" loading="lazy">
                            @else
                                <span class="attach-ext" style="color:{{ $extColor }};">.{{ strtoupper($ext) }}</span>
                            @endif
                        </div>
                        <div class="attach-name">
                            <div class="name" title="{{ $att->file_name }}">{{ $att->name ?: $att->file_name }}</div>
                            <div class="meta">{{ $sizeMb }} · {{ $att->created_at->format('M j, Y') }} · by {{ \App\Models\User::find($att->getCustomProperty('uploaded_by'))?->name ?? 'Unknown' }}</div>
                        </div>
                        <div class="attach-actions">
                            <a href="{{ route('tasks.attachments.download', [$task, $att]) }}" class="btn btn-secondary btn-xs" title="Download">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            </a>
                            @if($canDelete)
                            <button onclick="deleteAttachment({{ $att->id }}, '{{ route('tasks.attachments.destroy', [$task, $att]) }}')" class="btn btn-danger btn-xs" title="Delete">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6m4-6v6"/></svg>
                            </button>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>

                @if($attachments->isEmpty())
                <p class="text-xs text-faint" style="text-align:center;margin-top:8px;" id="emptyAttach">No attachments yet.</p>
                @endif
            </div>
        </div>

        {{-- Status update --}}
        <div class="card">
            <div class="card-hd"><h3>Update Status</h3></div>
            <div class="card-bd">
                @foreach(\App\Enums\TaskStatus::cases() as $statusCase)
                @php($s = $statusCase->value)
                @php($label = $statusCase->label())
                <form method="POST" action="{{ route('tasks.update', $task) }}" style="margin-bottom:6px;">
                    @csrf @method('PATCH')
                    <input type="hidden" name="name" value="{{ $task->name }}">
                    <input type="hidden" name="status" value="{{ $s }}">
                    <input type="hidden" name="priority" value="{{ $task->priority }}">
                    <input type="hidden" name="project_id" value="{{ $task->project_id }}">
                    @foreach($task->assignees as $assignee)
                    <input type="hidden" name="assignees[]" value="{{ $assignee->id }}">
                    @endforeach
                    <input type="hidden" name="due_date" value="{{ $task->due_date?->format('Y-m-d') }}">
                    <input type="hidden" name="description" value="{{ $task->description }}">
                    <button type="submit" class="btn {{ $task->status==$s ? 'btn-primary' : 'btn-secondary' }}" style="width:100%;justify-content:center;">
                        <span class="badge badge-{{ $s }}" style="margin-right:6px;">{{ $label }}</span>
                        {{ $task->status==$s ? '← Current' : 'Set to '.$label }}
                    </button>
                </form>
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<div id="copyToast"></div>
<script>
/* ── Emoji picker ── */
function toggleEmojiPanel(e) {
    e.stopPropagation();
    document.getElementById('emojiPanel').classList.toggle('open');
    document.getElementById('shareMenu')?.classList.remove('open');
}
function insertEmoji(emoji) {
    const ta = document.getElementById('commentBody');
    const s = ta.selectionStart, end = ta.selectionEnd;
    ta.value = ta.value.substring(0, s) + emoji + ta.value.substring(end);
    ta.selectionStart = ta.selectionEnd = s + emoji.length;
    ta.focus();
    document.getElementById('emojiPanel').classList.remove('open');
}

/* ── Share dropdown ── */
function toggleShare(e) {
    e.stopPropagation();
    document.getElementById('shareMenu').classList.toggle('open');
    document.getElementById('emojiPanel')?.classList.remove('open');
}
function closeShare() {
    document.getElementById('shareMenu').classList.remove('open');
}
function showToast(msg) {
    const t = document.getElementById('copyToast');
    t.textContent = msg;
    t.style.opacity = '1';
    clearTimeout(t._timer);
    t._timer = setTimeout(() => { t.style.opacity = '0'; }, 2200);
}
function copyTaskLink() {
    navigator.clipboard.writeText(window.location.href)
        .then(() => showToast('✓ Link copied to clipboard'))
        .catch(() => showToast('Could not copy — try manually'));
    closeShare();
}
function copyForSlack() {
    const text = '*{{ addslashes($task->name) }}*\n{{ route('tasks.show', $task) }}';
    navigator.clipboard.writeText(text)
        .then(() => showToast('✓ Copied for Slack'))
        .catch(() => showToast('Could not copy'));
    closeShare();
}

/* ── Close popovers on outside click ── */
document.addEventListener('click', () => {
    document.getElementById('emojiPanel')?.classList.remove('open');
    document.getElementById('shareMenu')?.classList.remove('open');
    document.getElementById('sfMenu')?.classList.remove('open');
});

/* ── Share-file dropdown ── */
function toggleSfMenu(e) {
    e.stopPropagation();
    document.getElementById('emojiPanel')?.classList.remove('open');
    document.getElementById('shareMenu')?.classList.remove('open');
    document.getElementById('sfMenu').classList.toggle('open');
}
function shareFile(name, url) {
    const ta  = document.getElementById('commentBody');
    const ref = '\n📎 ' + name + '\n' + url + '\n';
    const pos = ta.selectionEnd ?? ta.value.length;
    ta.value  = ta.value.slice(0, pos) + ref + ta.value.slice(pos);
    ta.focus();
    document.getElementById('sfMenu').classList.remove('open');
    showToast('📎 File reference added');
}

/* ── Comment attachments (images/videos with live preview) ── */
(function () {
    const form       = document.getElementById('commentForm');
    const fileInput  = document.getElementById('commentFilesInput');
    const attachBtn  = document.getElementById('commentAttachBtn');
    const preview    = document.getElementById('commentAttachPreview');
    if (!form || !fileInput) return;

    const MAX_FILES = 10;
    let pendingFiles = [];

    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => addFiles(fileInput.files));

    ['dragenter', 'dragover'].forEach(evt => form.addEventListener(evt, e => {
        e.preventDefault();
        form.classList.add('drag-over');
    }));
    ['dragleave', 'drop'].forEach(evt => form.addEventListener(evt, e => {
        e.preventDefault();
        form.classList.remove('drag-over');
    }));
    form.addEventListener('drop', e => {
        if (e.dataTransfer.files.length) addFiles(e.dataTransfer.files);
    });

    function addFiles(fileList) {
        const incoming = Array.from(fileList);
        const room     = MAX_FILES - pendingFiles.length;

        if (incoming.length > room) {
            showToast(`⚠️ Only ${MAX_FILES} attachments allowed per comment`);
        }

        pendingFiles = pendingFiles.concat(incoming.slice(0, Math.max(room, 0)));
        syncInput();
        renderPreview();
    }

    function removeFile(index) {
        pendingFiles.splice(index, 1);
        syncInput();
        renderPreview();
    }

    function syncInput() {
        const dt = new DataTransfer();
        pendingFiles.forEach(f => dt.items.add(f));
        fileInput.files = dt.files;
    }

    function renderPreview() {
        preview.innerHTML = '';
        pendingFiles.forEach((file, idx) => {
            const chip = document.createElement('div');
            chip.className = 'comment-attach-chip';

            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.src = URL.createObjectURL(file);
                chip.appendChild(img);
            } else if (file.type.startsWith('video/')) {
                const vid = document.createElement('video');
                vid.src   = URL.createObjectURL(file);
                vid.muted = true;
                chip.appendChild(vid);
                const play = document.createElement('div');
                play.className   = 'attach-play';
                play.textContent = '▶';
                chip.appendChild(play);
            } else {
                const generic = document.createElement('div');
                generic.className = 'attach-generic';
                generic.textContent = file.name;
                chip.appendChild(generic);
            }

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'attach-remove';
            removeBtn.title = 'Remove';
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', () => removeFile(idx));
            chip.appendChild(removeBtn);

            preview.appendChild(chip);
        });
    }
})();

/* ── Attachments ── */
(function () {
    const input     = document.getElementById('attachInput');
    const dropZone  = document.getElementById('attachDrop');
    const list      = document.getElementById('attachList');
    const bar       = document.getElementById('uploadProgressBar');
    const fill      = document.getElementById('uploadProgressFill');
    const status    = document.getElementById('uploadStatus');
    const countEl   = document.getElementById('attachCount');
    const emptyMsg  = document.getElementById('emptyAttach');
    const uploadUrl = '{{ route('tasks.attachments.store', $task) }}';
    const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

    // Drag events
    ['dragenter','dragover'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('drag-over'); }));
    ['dragleave','drop'].forEach(e => dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('drag-over'); }));
    dropZone.addEventListener('drop', ev => { if (ev.dataTransfer.files.length) uploadFiles(ev.dataTransfer.files); });
    input.addEventListener('change', () => { if (input.files.length) uploadFiles(input.files); });

    function uploadFiles(files) {
        const fd = new FormData();
        Array.from(files).forEach(f => fd.append('files[]', f));
        fd.append('_token', csrfToken);

        bar.style.display = 'block';
        fill.style.width  = '0%';
        status.style.display = 'block';
        status.textContent = 'Uploading…';

        const xhr = new XMLHttpRequest();
        xhr.open('POST', uploadUrl);
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
        xhr.setRequestHeader('Accept', 'application/json');

        xhr.upload.addEventListener('progress', ev => {
            if (ev.lengthComputable) fill.style.width = Math.round(ev.loaded / ev.total * 100) + '%';
        });

        xhr.addEventListener('load', () => {
            fill.style.width = '100%';
            status.textContent = 'Upload complete — reloading…';
            setTimeout(() => location.reload(), 500);
        });

        xhr.addEventListener('error', () => {
            fill.style.background = '#f87171';
            status.textContent = 'Upload failed. Please try again.';
        });

        xhr.send(fd);
    }
})();

function deleteAttachment(id, url) {
    if (!confirm('Delete this attachment?')) return;

    fetch(url, {
        method: 'POST',
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: '_method=DELETE',
    }).then(r => {
        if (r.ok) {
            const el = document.getElementById('attach-' + id);
            if (el) el.remove();
            const count = document.querySelectorAll('.attach-item').length;
            document.getElementById('attachCount').textContent = '(' + count + ')';
            if (count === 0) {
                const msg = document.createElement('p');
                msg.className = 'text-xs text-faint';
                msg.style.textAlign = 'center';
                msg.style.marginTop = '8px';
                msg.textContent = 'No attachments yet.';
                document.getElementById('attachList').after(msg);
            }
        } else {
            alert('Could not delete. You may not have permission.');
        }
    });
}
</script>
@endpush
