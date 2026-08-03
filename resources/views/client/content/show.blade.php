@extends('layouts.portal')
@section('title', $task->name)

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $task->name }}</h2>
        <div class="row center" style="gap:8px;margin-top:4px;">
            @php $label = $task->clientApprovalLabel(); @endphp
            @if($label === 'Approved')
                <span class="badge badge-done">Approved</span>
            @else
                <span class="badge badge-review">Awaiting Your Approval</span>
            @endif
            @if($task->project)
            <span class="text-sm text-muted">{{ $task->project->name }}</span>
            @endif
        </div>
    </div>
    <a href="{{ route('client.content.index') }}" class="btn btn-secondary">← All Content</a>
</div>

<div class="card mb6" style="margin-bottom:16px;">
    <div class="card-hd"><h3>Content</h3></div>
    <div class="card-bd">
        @if($task->description)
        <p style="font-size:0.875rem;color:#c8cce0;line-height:1.6;margin-bottom:14px;">{{ $task->description }}</p>
        @endif

        @php $files = $task->getMedia('attachments'); @endphp
        @if($files->isEmpty())
            <p class="text-muted text-sm">No files attached to this content yet.</p>
        @else
        <div class="comment-attachments">
            @foreach($files as $file)
                @php
                    $isImg   = str_starts_with($file->mime_type ?? '', 'image/');
                    $isVideo = str_starts_with($file->mime_type ?? '', 'video/');
                    $url     = rebase_media_url($file->getUrl());
                @endphp
                @if($isImg)
                    <a href="{{ $url }}" target="_blank" rel="noopener"><img src="{{ $url }}" class="comment-img" alt="{{ $file->file_name }}" loading="lazy"></a>
                @elseif($isVideo)
                    <video src="{{ $url }}" class="comment-video" controls preload="metadata"></video>
                @else
                    <div class="comment-file-row">
                        <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#f2b705"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                        <a href="{{ $url }}" target="_blank" rel="noopener" class="link">{{ $file->file_name }}</a>
                    </div>
                @endif
            @endforeach
        </div>
        @endif
    </div>
</div>

@if($label !== 'Approved')
<div class="card mb6" style="margin-bottom:16px;">
    <div class="card-hd"><h3>Your Decision</h3></div>
    <div class="card-bd">
        <div class="row" style="gap:8px;margin-bottom:16px;">
            <form method="POST" action="{{ route('client.content.approve', $task) }}" onsubmit="return confirm('Approve this content?')">
                @csrf
                <button type="submit" class="btn btn-primary">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="20 6 9 17 4 12"/></svg>
                    Approve
                </button>
            </form>
        </div>

        <p class="text-sm text-muted" style="margin-bottom:10px;">Or describe what needs to change:</p>
        <form method="POST" action="{{ route('client.content.request-revision', $task) }}" class="comment-form" id="revisionForm" enctype="multipart/form-data">
            @csrf
            <textarea name="body" id="revisionBody" class="comment-textarea" placeholder="What would you like changed?">{{ old('body') }}</textarea>
            @error('body')<div style="font-size:0.75rem;color:#f87171;margin-top:3px;">{{ $message }}</div>@enderror
            <div class="comment-attach-preview" id="revisionAttachPreview"></div>
            <input type="file" id="revisionFilesInput" name="files[]" multiple accept="image/*,video/*" style="display:none">
            <div style="display:flex;align-items:center;gap:8px;margin-top:8px;">
                <button type="submit" class="btn btn-secondary btn-sm">Request Revision</button>
                <button type="button" class="comment-attach-btn" id="revisionAttachBtn" title="Attach a reference file">
                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                    Attach
                </button>
            </div>
        </form>
    </div>
</div>
@endif

<div class="card">
    <div class="card-hd"><h3>Conversation</h3></div>
    <div class="card-bd">
        @if($task->visibleComments->isEmpty())
            <p class="text-sm text-muted">No messages yet.</p>
        @else
        <div class="comment-list">
            @foreach($task->visibleComments as $comment)
            <div class="comment-item">
                <div class="comment-avatar">{{ strtoupper(substr($comment->author->name, 0, 1)) }}</div>
                <div class="comment-bubble">
                    <div class="comment-meta">
                        <span class="comment-author">{{ $comment->author->name }}</span>
                        <span class="comment-time">{{ $comment->created_at->diffForHumans() }}</span>
                    </div>
                    @if($comment->body)
                    <div class="comment-body">{!! format_comment($comment->body) !!}</div>
                    @endif
                    @php $cFiles = $comment->getMedia('comment_files'); @endphp
                    @if($cFiles->count())
                    <div class="comment-attachments">
                        @foreach($cFiles as $cf)
                            @php
                                $cfIsImg   = str_starts_with($cf->mime_type ?? '', 'image/');
                                $cfIsVideo = str_starts_with($cf->mime_type ?? '', 'video/');
                                $cfUrl     = rebase_media_url($cf->getUrl());
                            @endphp
                            @if($cfIsImg)
                                <a href="{{ $cfUrl }}" target="_blank" rel="noopener"><img src="{{ $cfUrl }}" class="comment-img" alt="{{ $cf->file_name }}" loading="lazy"></a>
                            @elseif($cfIsVideo)
                                <video src="{{ $cfUrl }}" class="comment-video" controls preload="metadata"></video>
                            @else
                                <div class="comment-file-row">
                                    <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#f2b705"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>
                                    <a href="{{ $cfUrl }}" target="_blank" rel="noopener" class="link">{{ $cf->file_name }}</a>
                                </div>
                            @endif
                        @endforeach
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function () {
    const form      = document.getElementById('revisionForm');
    const fileInput = document.getElementById('revisionFilesInput');
    const attachBtn = document.getElementById('revisionAttachBtn');
    const preview   = document.getElementById('revisionAttachPreview');
    if (!form || !fileInput) return;

    const MAX_FILES = 10;
    let pendingFiles = [];

    attachBtn.addEventListener('click', () => fileInput.click());
    fileInput.addEventListener('change', () => addFiles(fileInput.files));

    ['dragenter', 'dragover'].forEach(evt => form.addEventListener(evt, e => { e.preventDefault(); form.classList.add('drag-over'); }));
    ['dragleave', 'drop'].forEach(evt => form.addEventListener(evt, e => { e.preventDefault(); form.classList.remove('drag-over'); }));
    form.addEventListener('drop', e => { if (e.dataTransfer.files.length) addFiles(e.dataTransfer.files); });

    function addFiles(fileList) {
        const incoming = Array.from(fileList);
        const room     = MAX_FILES - pendingFiles.length;
        pendingFiles   = pendingFiles.concat(incoming.slice(0, Math.max(room, 0)));
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
            }

            const removeBtn = document.createElement('button');
            removeBtn.type = 'button';
            removeBtn.className = 'attach-remove';
            removeBtn.textContent = '×';
            removeBtn.addEventListener('click', () => removeFile(idx));
            chip.appendChild(removeBtn);

            preview.appendChild(chip);
        });
    }
})();
</script>
@endpush
@endsection
