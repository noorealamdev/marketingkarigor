@extends('layouts.app')
@section('title', $project->name . ' — Media')
@section('breadcrumb', 'Projects / ' . $project->name . ' / Media')

@push('styles')
<style>
.media-layout { display: grid; grid-template-columns: 260px 1fr; gap: 20px; align-items: start; }
.media-sidebar { position: sticky; top: 20px; }
.collection-btn { display: flex; align-items: center; justify-content: space-between; padding: 9px 12px; border-radius: 8px; cursor: pointer; margin-bottom: 2px; color: #6b7590; font-size: 0.845rem; font-weight: 500; transition: all 0.12s; text-decoration: none; }
.collection-btn:hover { background: #1a1e28; color: #c8cce0; }
.collection-btn.active { background: rgba(108,99,255,0.15); color: #6c63ff; }
.collection-btn .count { background: #1a1e28; border-radius: 999px; padding: 1px 8px; font-size: 0.72rem; font-weight: 600; }
.collection-btn.active .count { background: rgba(108,99,255,0.25); color: #6c63ff; }

.drop-zone { border: 2px dashed #2d3148; border-radius: 12px; padding: 32px 20px; text-align: center; cursor: pointer; transition: all 0.15s; background: #13161d; }
.drop-zone.drag-over { border-color: #6c63ff; background: rgba(108,99,255,0.06); }
.drop-zone-icon { width: 44px; height: 44px; margin: 0 auto 12px; color: #3a4060; }
.drop-zone p { color: #4a5068; font-size: 0.845rem; margin: 0; }
.drop-zone strong { color: #6c63ff; }

.media-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(180px, 1fr)); gap: 12px; }
.media-card { background: #13161d; border: 1px solid #252936; border-radius: 10px; overflow: hidden; transition: border-color 0.12s; }
.media-card:hover { border-color: #3a4060; }
.media-thumb { height: 140px; background: #0e1017; display: flex; align-items: center; justify-content: center; overflow: hidden; position: relative; }
.media-thumb img { width: 100%; height: 100%; object-fit: cover; }
.media-thumb video { width: 100%; height: 100%; object-fit: cover; }
.media-thumb .play-icon { position: absolute; }
.media-thumb .file-icon { text-align: center; padding: 10px; }
.media-thumb .file-icon .ext { font-size: 1.6rem; font-weight: 800; font-family: monospace; letter-spacing: -0.04em; }
.media-thumb .file-icon .size { font-size: 0.7rem; color: #4a5068; margin-top: 4px; }
.media-info { padding: 10px 12px; }
.media-name { font-size: 0.82rem; font-weight: 600; color: #c8cce0; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; margin-bottom: 3px; }
.media-meta { font-size: 0.7rem; color: #4a5068; display: flex; justify-content: space-between; margin-bottom: 8px; }
.media-actions { display: flex; gap: 4px; }
.media-actions a, .media-actions button { flex: 1; }

.upload-progress { display: none; margin-top: 12px; }
.upload-progress.show { display: block; }
.progress-item { display: flex; align-items: center; gap: 10px; padding: 8px 0; border-bottom: 1px solid #252936; font-size: 0.8rem; }
.progress-item:last-child { border-bottom: none; }
.progress-bar-wrap { flex: 1; height: 4px; background: #1a1e28; border-radius: 2px; overflow: hidden; }
.progress-bar-fill { height: 100%; background: #6c63ff; transition: width 0.2s; border-radius: 2px; }

.stat-row { display: flex; gap: 16px; flex-wrap: wrap; margin-bottom: 16px; }
.stat-box { background: #13161d; border: 1px solid #252936; border-radius: 8px; padding: 10px 14px; flex: 1; min-width: 80px; }
.stat-box .val { font-size: 1.2rem; font-weight: 700; color: #c8cce0; }
.stat-box .lbl { font-size: 0.68rem; color: #4a5068; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 2px; }

.empty-panel { padding: 60px 20px; text-align: center; color: #4a5068; }
.empty-panel svg { margin: 0 auto 16px; opacity: 0.3; }
</style>
@endpush

@section('content')
<div class="page-hd row between" style="margin-bottom:20px;">
    <div>
        <h2>{{ $project->name }}</h2>
        <div class="text-sm text-muted" style="margin-top:3px;">Media Library · {{ $allMedia->count() }} files · {{ number_format($totalSize / 1048576, 1) }} MB</div>
    </div>
    <div class="row" style="gap:6px;">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary btn-sm">← Back to Project</a>
    </div>
</div>

@if($errors->any())
<div class="alert alert-error" style="margin-bottom:16px;">{{ $errors->first() }}</div>
@endif

<div class="media-layout">
    {{-- LEFT SIDEBAR --}}
    <div class="media-sidebar">
        {{-- Stats --}}
        <div class="stat-row" style="flex-direction:column;gap:0;margin-bottom:0;">
            <div class="card" style="margin-bottom:16px;padding:14px;">
                <div style="font-size:0.7rem;color:#4a5068;text-transform:uppercase;letter-spacing:0.08em;margin-bottom:10px;">Storage</div>
                <div style="font-size:1.4rem;font-weight:800;color:#6c63ff;">{{ number_format($totalSize / 1048576, 1) }} <span style="font-size:0.8rem;font-weight:500;color:#4a5068;">MB</span></div>
                <div style="font-size:0.72rem;color:#4a5068;margin-top:2px;">{{ $allMedia->count() }} total files</div>
            </div>
        </div>

        {{-- Collections Nav --}}
        <div class="card" style="padding:12px;">
            <div style="font-size:0.68rem;color:#3a4060;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;padding:0 4px;margin-bottom:8px;">Collections</div>

            <a href="{{ route('projects.media', $project) }}" class="collection-btn {{ $filter === 'all' ? 'active' : '' }}">
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:7px;"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/></svg>
                    All Files
                </span>
                <span class="count">{{ $allMedia->count() }}</span>
            </a>

            @foreach([
                'images'    => ['icon' => 'M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z', 'label' => 'Images'],
                'videos'    => ['icon' => 'M15 10l4.553-2.069A1 1 0 0121 8.81v6.38a1 1 0 01-1.447.894L15 14M3 8a2 2 0 012-2h8a2 2 0 012 2v8a2 2 0 01-2 2H5a2 2 0 01-2-2V8z', 'label' => 'Videos'],
                'documents' => ['icon' => 'M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z', 'label' => 'Documents'],
                'renders'   => ['icon' => 'M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4', 'label' => '3D / Renders'],
                'other'     => ['icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z', 'label' => 'Other'],
            ] as $col => $meta)
            <a href="{{ route('projects.media', [$project, 'type' => $col]) }}" class="collection-btn {{ $filter === $col ? 'active' : '' }}">
                <span>
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:inline;vertical-align:-2px;margin-right:7px;"><path d="{{ $meta['icon'] }}"/></svg>
                    {{ $meta['label'] }}
                </span>
                <span class="count">{{ $counts[$col] ?? 0 }}</span>
            </a>
            @endforeach
        </div>

        {{-- Upload Form --}}
        <div class="card" style="margin-top:14px;padding:14px;">
            <div style="font-size:0.7rem;color:#3a4060;text-transform:uppercase;letter-spacing:0.1em;font-weight:700;margin-bottom:12px;">Upload Files</div>

            <form id="uploadForm" method="POST" action="{{ route('projects.media.store', $project) }}" enctype="multipart/form-data">
                @csrf
                <div id="dropZone" class="drop-zone" onclick="document.getElementById('fileInput').click()">
                    <svg class="drop-zone-icon" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5">
                        <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                    </svg>
                    <p><strong>Click to browse</strong> or drag files here</p>
                    <p style="margin-top:6px;font-size:0.75rem;">Images, videos, PDFs, .blend — up to 100 MB each</p>
                </div>
                <input type="file" id="fileInput" name="file" style="display:none" multiple accept="*/*">

                <div style="margin-top:10px;">
                    <label class="form-label" style="font-size:0.75rem;">Collection</label>
                    <select name="collection" class="form-control" style="font-size:0.82rem;">
                        <option value="">Auto-detect</option>
                        <option value="images" {{ $filter === 'images' ? 'selected' : '' }}>Images</option>
                        <option value="videos" {{ $filter === 'videos' ? 'selected' : '' }}>Videos</option>
                        <option value="documents" {{ $filter === 'documents' ? 'selected' : '' }}>Documents</option>
                        <option value="renders" {{ $filter === 'renders' ? 'selected' : '' }}>3D / Renders</option>
                        <option value="other" {{ $filter === 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>
                <div style="margin-top:8px;">
                    <label class="form-label" style="font-size:0.75rem;">Custom Name <span style="color:#4a5068;">(optional)</span></label>
                    <input type="text" name="custom_name" class="form-control" style="font-size:0.82rem;" placeholder="Leave blank to use filename">
                </div>

                <div id="uploadProgress" class="upload-progress"></div>

                <button type="submit" id="uploadBtn" class="btn btn-primary" style="width:100%;margin-top:10px;justify-content:center;">
                    Upload
                </button>
            </form>
        </div>
    </div>

    {{-- MAIN PANEL --}}
    <div>
        @if($filtered->isEmpty())
        <div class="card">
            <div class="empty-panel">
                <svg width="56" height="56" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.2">
                    <path d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/>
                </svg>
                <p style="font-size:1rem;font-weight:600;color:#6b7590;margin:0 0 6px;">No files yet</p>
                <p style="font-size:0.84rem;">Upload files using the panel on the left.</p>
            </div>
        </div>
        @else
        {{-- Sort / view options --}}
        <div class="row between" style="margin-bottom:12px;">
            <div class="text-sm text-muted">{{ $filtered->count() }} {{ $filtered->count() === 1 ? 'file' : 'files' }}{{ $filter !== 'all' ? ' in this collection' : '' }}</div>
        </div>

        <div class="media-grid">
            @foreach($filtered as $media)
            @php
                $mime = $media->mime_type;
                $ext  = strtolower(pathinfo($media->file_name, PATHINFO_EXTENSION));
                $isImage = str_starts_with($mime, 'image/');
                $isVideo = str_starts_with($mime, 'video/');
                $sizeMb  = number_format($media->size / 1048576, 2);
                $extColor = match($ext) {
                    'pdf'            => '#f87171',
                    'blend'          => '#fb923c',
                    'psd', 'ai'      => '#a78bfa',
                    'doc', 'docx'    => '#60a5fa',
                    'xls', 'xlsx'    => '#4ade80',
                    'mp4','mov','avi','webm' => '#fbbf24',
                    'zip','rar','7z' => '#f472b6',
                    default          => '#6b7590',
                };
            @endphp
            <div class="media-card">
                {{-- Thumbnail --}}
                <div class="media-thumb">
                    @if($isImage)
                        @if($media->hasGeneratedConversion('thumb'))
                            <img src="{{ rebase_media_url($media->getUrl('thumb')) }}" alt="{{ $media->name }}" loading="lazy">
                        @else
                            <img src="{{ rebase_media_url($media->getUrl()) }}" alt="{{ $media->name }}" loading="lazy">
                        @endif
                    @elseif($isVideo)
                        <video muted preload="metadata" style="width:100%;height:100%;object-fit:cover;">
                            <source src="{{ rebase_media_url($media->getUrl()) }}" type="{{ $mime }}">
                        </video>
                        <div class="play-icon">
                            <svg width="32" height="32" viewBox="0 0 44 44">
                                <circle cx="22" cy="22" r="22" fill="rgba(0,0,0,0.55)"/>
                                <polygon points="18,14 32,22 18,30" fill="rgba(255,255,255,0.85)"/>
                            </svg>
                        </div>
                    @else
                        <div class="file-icon">
                            <div class="ext" style="color:{{ $extColor }};">.{{ strtoupper($ext) }}</div>
                            <div class="size">{{ $sizeMb }} MB</div>
                        </div>
                    @endif
                </div>

                {{-- Info --}}
                <div class="media-info">
                    <div class="media-name" title="{{ $media->name }}">{{ $media->name }}</div>
                    <div class="media-meta">
                        <span>{{ $sizeMb }} MB</span>
                        <span>{{ $media->created_at->format('M j, Y') }}</span>
                    </div>
                    <div class="media-actions">
                        <a href="{{ route('projects.media.download', [$project, $media]) }}" class="btn btn-secondary btn-xs" style="justify-content:center;">
                            <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/></svg>
                            Download
                        </a>
                        <form method="POST" action="{{ route('projects.media.destroy', [$project, $media]) }}" onsubmit="return confirm('Delete «{{ $media->name }}»?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-xs">
                                <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14H6L5 6"/><path d="M10 11v6m4-6v6"/><path d="M9 6V4h6v2"/></svg>
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
(function() {
    const dropZone  = document.getElementById('dropZone');
    const fileInput = document.getElementById('fileInput');
    const form      = document.getElementById('uploadForm');
    const progress  = document.getElementById('uploadProgress');
    const uploadBtn = document.getElementById('uploadBtn');

    // Drag & drop
    ['dragenter','dragover'].forEach(e => {
        dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.add('drag-over'); });
    });
    ['dragleave','drop'].forEach(e => {
        dropZone.addEventListener(e, ev => { ev.preventDefault(); dropZone.classList.remove('drag-over'); });
    });
    dropZone.addEventListener('drop', ev => {
        const files = ev.dataTransfer.files;
        if (files.length) uploadFiles(files);
    });

    fileInput.addEventListener('change', () => {
        if (fileInput.files.length) uploadFiles(fileInput.files);
    });

    // Show a dismissible error banner above the form
    function showError(msg) {
        let banner = document.getElementById('uploadError');
        if (!banner) {
            banner = document.createElement('div');
            banner.id = 'uploadError';
            banner.style.cssText = 'background:#3a1a1a;border:1px solid #7f1d1d;color:#fca5a5;border-radius:8px;padding:10px 14px;margin-bottom:12px;font-size:0.82rem;display:flex;align-items:flex-start;gap:8px;';
            form.parentNode.insertBefore(banner, form);
        }
        banner.innerHTML = `<span style="flex:1;">${msg}</span><button onclick="this.parentNode.remove()" style="background:none;border:none;color:#fca5a5;cursor:pointer;font-size:1rem;line-height:1;padding:0;">×</button>`;
    }

    function uploadFiles(files) {
        // Remove any previous error banner
        const old = document.getElementById('uploadError');
        if (old) old.remove();

        // Reset progress area
        progress.innerHTML = '';
        Array.from(files).forEach((file, i) => {
            const row = document.createElement('div');
            row.className = 'progress-item';
            row.innerHTML = `
                <span style="flex:0 0 120px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#c8cce0;">${file.name}</span>
                <div class="progress-bar-wrap"><div class="progress-bar-fill" id="bar_${i}" style="width:0%"></div></div>
                <span id="pct_${i}" style="flex:0 0 36px;text-align:right;color:#4a5068;">0%</span>
            `;
            progress.appendChild(row);
        });
        progress.classList.add('show');
        uploadBtn.disabled = true;

        const collection = form.querySelector('[name=collection]').value;
        const customName = form.querySelector('[name=custom_name]').value;

        let done = 0;
        let anyError = false;

        Array.from(files).forEach((file, i) => {
            const fd = new FormData();
            fd.append('_token', '{{ csrf_token() }}');
            fd.append('file', file);
            if (collection) fd.append('collection', collection);
            if (customName && files.length === 1) fd.append('custom_name', customName);

            const xhr = new XMLHttpRequest();
            xhr.open('POST', form.action);
            xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');
            xhr.timeout = 300000; // 5 min timeout for large files

            xhr.upload.addEventListener('progress', ev => {
                if (ev.lengthComputable) {
                    const pct = Math.round(ev.loaded / ev.total * 100);
                    document.getElementById('bar_' + i).style.width = pct + '%';
                    document.getElementById('pct_' + i).textContent = pct + '%';
                }
            });

            xhr.addEventListener('load', () => {
                done++;
                if (xhr.status >= 200 && xhr.status < 300) {
                    document.getElementById('bar_' + i).style.background = '#4ade80';
                    document.getElementById('pct_' + i).textContent = '✓';
                } else {
                    anyError = true;
                    document.getElementById('bar_' + i).style.background = '#f87171';
                    document.getElementById('pct_' + i).textContent = '✗';
                    // Try to parse a useful message from the response
                    let errMsg = `Upload failed (HTTP ${xhr.status})`;
                    if (xhr.status === 413) {
                        errMsg = `<strong>${file.name}</strong> is too large for the server. Ask your administrator to increase <code>upload_max_filesize</code> and <code>post_max_size</code> in php.ini.`;
                    } else if (xhr.status === 422) {
                        try {
                            const json = JSON.parse(xhr.responseText);
                            const msgs = json.errors ? Object.values(json.errors).flat() : [json.message];
                            errMsg = `<strong>${file.name}</strong>: ${msgs.join(' ')}`;
                        } catch(e) {}
                    } else if (xhr.status === 0) {
                        errMsg = `<strong>${file.name}</strong>: Connection lost or server timed out. Try again.`;
                    }
                    showError(errMsg);
                }
                if (done === files.length) {
                    uploadBtn.disabled = false;
                    if (!anyError) setTimeout(() => location.reload(), 600);
                }
            });

            xhr.addEventListener('error', () => {
                anyError = true;
                document.getElementById('bar_' + i).style.background = '#f87171';
                document.getElementById('pct_' + i).textContent = '✗';
                showError(`<strong>${file.name}</strong>: Network error — check your connection and try again.`);
                done++;
                if (done === files.length) uploadBtn.disabled = false;
            });

            xhr.addEventListener('timeout', () => {
                anyError = true;
                document.getElementById('bar_' + i).style.background = '#fb923c';
                document.getElementById('pct_' + i).textContent = '✗';
                showError(`<strong>${file.name}</strong>: Upload timed out. The file may be too large or your connection too slow.`);
                done++;
                if (done === files.length) uploadBtn.disabled = false;
            });

            xhr.send(fd);
        });
    }
})();
</script>
@endpush
@endsection
