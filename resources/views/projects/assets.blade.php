@extends('layouts.app')
@section('title', $project->name . ' — Assets')
@section('breadcrumb', 'Projects / ' . $project->name . ' / Assets')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $project->name }}</h2>
        <p>Asset Library — {{ $assets->count() }} files uploaded</p>
    </div>
    <div class="row" style="gap:6px;">
        <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">← Project</a>
    </div>
</div>

{{-- Upload Form --}}
<div class="card mb6">
    <div class="card-hd"><h3>Upload Asset</h3></div>
    <div class="card-bd">
        <form method="POST" action="{{ route('projects.assets.store', $project) }}" enctype="multipart/form-data">
            @csrf
            @if($errors->any())
            <div class="alert alert-error">{{ $errors->first() }}</div>
            @endif
            <div style="display:grid;grid-template-columns:1fr 1fr 1fr auto;gap:10px;align-items:end;">
                <div>
                    <label class="form-label">File *</label>
                    <input type="file" name="file" class="form-control" required>
                </div>
                <div>
                    <label class="form-label">Asset Name</label>
                    <input type="text" name="name" class="form-control" placeholder="e.g. Hero Banner v2" value="{{ old('name') }}">
                </div>
                <div>
                    <label class="form-label">Category</label>
                    <select name="category" class="form-control">
                        <option value="general">General</option>
                        <option value="3d-render">3D Render</option>
                        <option value="creative">Ad Creative</option>
                        <option value="video">Video</option>
                        <option value="reference">Reference</option>
                        <option value="document">Document</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary">Upload</button>
            </div>
            <div style="margin-top:10px;">
                <label class="form-label">Description (optional)</label>
                <input type="text" name="description" class="form-control" placeholder="Short note about this asset…" value="{{ old('description') }}">
            </div>
            <div style="margin-top:6px;font-size:0.76rem;color:#4a5068;">Max file size: 50MB. Supports images, videos, PDFs, .blend, and any other file type.</div>
        </form>
    </div>
</div>

{{-- Category Filter --}}
@if($assets->count())
@php
    $categories = $assets->groupBy('category');
    $selectedCat = request('cat', 'all');
@endphp
<div class="filter-row mb4">
    <a href="{{ route('projects.assets', $project) }}" class="btn {{ $selectedCat=='all' ? 'btn-primary' : 'btn-secondary' }} btn-sm">All ({{ $assets->count() }})</a>
    @foreach($categories as $cat => $items)
    <a href="{{ route('projects.assets', $project) }}?cat={{ $cat }}" class="btn {{ $selectedCat==$cat ? 'btn-primary' : 'btn-secondary' }} btn-sm">
        {{ ucwords(str_replace('-',' ',$cat)) }} ({{ $items->count() }})
    </a>
    @endforeach
</div>
@endif

{{-- Assets Grid --}}
@php
    $filtered = $selectedCat && $selectedCat !== 'all' ? $assets->where('category', $selectedCat) : $assets;
@endphp

@if($filtered->isEmpty())
<div class="card">
    <div class="empty-state"><p>No assets uploaded yet. Use the form above to upload the first file.</p></div>
</div>
@else
<div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(220px,1fr));gap:14px;">
    @foreach($filtered as $asset)
    <div class="card" style="overflow:hidden;">
        {{-- Preview --}}
        <div style="height:160px;background:#1a1e28;display:flex;align-items:center;justify-content:center;overflow:hidden;border-bottom:1px solid #252936;">
            @if($asset->isImage())
                <img src="{{ Storage::disk('public')->url($asset->file_path) }}" alt="{{ $asset->name }}" style="width:100%;height:100%;object-fit:cover;">
            @elseif($asset->isVideo())
                <video style="width:100%;height:100%;object-fit:cover;" muted>
                    <source src="{{ Storage::disk('public')->url($asset->file_path) }}" type="{{ $asset->file_type }}">
                </video>
                <div style="position:absolute;"><svg width="36" height="36" viewBox="0 0 24 24" fill="rgba(255,255,255,0.7)" stroke="none"><circle cx="12" cy="12" r="12" fill="rgba(0,0,0,0.5)"/><polygon points="10,8 16,12 10,16"/></svg></div>
            @else
                {{-- File type icon --}}
                @php
                    $ext = strtolower(pathinfo($asset->file_name, PATHINFO_EXTENSION));
                    $iconColor = match($ext) {
                        'pdf' => '#f87171',
                        'blend' => '#fb923c',
                        'psd', 'ai' => '#a78bfa',
                        'doc','docx' => '#60a5fa',
                        'xls','xlsx' => '#4ade80',
                        'mp4','mov','avi' => '#fbbf24',
                        default => '#6b7590'
                    };
                @endphp
                <div style="text-align:center;">
                    <div style="font-size:2rem;font-weight:800;color:{{ $iconColor }};font-family:monospace;">.{{ strtoupper($ext) }}</div>
                    <div style="font-size:0.72rem;color:#4a5068;margin-top:4px;">{{ $asset->formattedSize() }}</div>
                </div>
            @endif
        </div>

        <div style="padding:12px;">
            <div class="fw600" style="font-size:0.845rem;margin-bottom:2px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;" title="{{ $asset->name }}">{{ $asset->name }}</div>
            @if($asset->description)
            <div class="text-xs text-muted" style="margin-bottom:6px;overflow:hidden;text-overflow:ellipsis;white-space:nowrap;">{{ $asset->description }}</div>
            @endif
            <div class="row between" style="font-size:0.72rem;color:#4a5068;margin-bottom:8px;">
                <span>{{ $asset->formattedSize() }}</span>
                <span>{{ $asset->created_at->format('M d') }}</span>
            </div>
            <div style="font-size:0.7rem;color:#4a5068;margin-bottom:8px;">by {{ $asset->uploader->name }}</div>
            <div class="row" style="gap:4px;">
                <a href="{{ Storage::disk('public')->url($asset->file_path) }}" target="_blank" class="btn btn-secondary btn-xs" style="flex:1;justify-content:center;">Download</a>
                <form method="POST" action="{{ route('projects.assets.destroy', [$project, $asset]) }}" onsubmit="return confirm('Delete this asset?')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger btn-xs">Del</button>
                </form>
            </div>
        </div>
    </div>
    @endforeach
</div>
@endif
@endsection
