@extends('layouts.app')
@section('title', 'Settings')
@section('breadcrumb', 'Settings')

@section('content')
<div class="page-hd">
    <div>
        <h2 style="font-size:1.35rem;font-weight:700;margin:0;">Settings</h2>
        <p style="color:#4a5068;font-size:0.82rem;margin:4px 0 0;">Admin-only configuration &amp; system tools</p>
    </div>
</div>

@if(session('cache_success'))
<div class="alert alert-success" style="display:flex;align-items:flex-start;gap:10px;background:#0d2b1a;border:1px solid #1a4731;border-radius:8px;padding:14px 16px;margin-bottom:20px;color:#4ade80;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><polyline points="20 6 9 17 4 12"/></svg>
    <div>
        <div style="font-weight:600;font-size:0.88rem;">Cache cleared successfully</div>
        <div style="font-size:0.82rem;opacity:0.85;margin-top:2px;">{{ session('cache_success') }}</div>
    </div>
</div>
@endif

@if(session('cache_errors'))
<div style="background:#2b0d0d;border:1px solid #4b1c1c;border-radius:8px;padding:14px 16px;margin-bottom:20px;color:#f87171;">
    <div style="font-weight:600;font-size:0.88rem;margin-bottom:6px;">Some caches could not be cleared:</div>
    @foreach(session('cache_errors') as $err)
        <div style="font-size:0.8rem;opacity:0.85;">• {{ $err }}</div>
    @endforeach
</div>
@endif

@if(session('backup_success'))
<div class="alert alert-success" style="display:flex;align-items:flex-start;gap:10px;background:#0d2b1a;border:1px solid #1a4731;border-radius:8px;padding:14px 16px;margin-bottom:20px;color:#4ade80;">
    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" style="flex-shrink:0;margin-top:1px;"><polyline points="20 6 9 17 4 12"/></svg>
    <div style="font-size:0.88rem;">{{ session('backup_success') }}</div>
</div>
@endif

@if(session('backup_error'))
<div style="background:#2b0d0d;border:1px solid #4b1c1c;border-radius:8px;padding:14px 16px;margin-bottom:20px;color:#f87171;font-size:0.85rem;">
    {{ session('backup_error') }}
</div>
@endif

<div style="display:grid;grid-template-columns:1fr 1fr;gap:20px;">

    {{-- ── Logo ── --}}
    <div class="card" style="grid-column:1/-1;">
        <div class="card-bd">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:20px;">
            <div style="display:flex;align-items:center;gap:16px;">
                <div style="width:64px;height:64px;background:#1a1e28;border-radius:10px;display:flex;align-items:center;justify-content:center;overflow:hidden;flex-shrink:0;">
                    @if($logoUrl = app_logo_url())
                        <img src="{{ $logoUrl }}" alt="Logo" style="max-width:100%;max-height:100%;object-fit:contain;">
                    @else
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="#4a5068" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/></svg>
                    @endif
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;">Logo</div>
                    <div style="font-size:0.78rem;color:#4a5068;margin-top:1px;">Shown across the app — sidebar, portal, login page, and PDF invoices/reports</div>
                </div>
            </div>
            <div style="display:flex;align-items:center;gap:8px;">
                <form method="POST" action="{{ route('settings.logo.update') }}" enctype="multipart/form-data" id="logoForm">
                    @csrf
                    <label class="btn btn-primary" style="cursor:pointer;margin:0;">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-2px;margin-right:5px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        {{ $logoUrl ? 'Replace Logo' : 'Upload Logo' }}
                        <input type="file" name="logo" accept="image/png,image/jpeg,image/svg+xml,image/webp" style="display:none;" onchange="document.getElementById('logoForm').requestSubmit()">
                    </label>
                </form>
                @if($logoUrl)
                <form method="POST" action="{{ route('settings.logo.destroy') }}" onsubmit="return confirm('Remove the logo? Sidebar and login pages will fall back to text branding.')">
                    @csrf @method('DELETE')
                    <button type="submit" class="btn btn-danger">Remove</button>
                </form>
                @endif
            </div>
        </div>
        @error('logo')<div class="text-xs" style="color:#f87171;margin-top:10px;">{{ $message }}</div>@enderror
        <p class="text-xs text-muted" style="margin-top:10px;">PNG, JPG, SVG or WebP &middot; max 2MB &middot; square or wide logos work best.</p>
        </div>
    </div>

    {{-- ── Cache Management ── --}}
    <div class="card" style="grid-column:1/-1;">
        <div class="card-bd">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;background:#1a1e28;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;">Cache Management</div>
                    <div style="font-size:0.78rem;color:#4a5068;margin-top:1px;">Clear all cached data, compiled views, routes, and config</div>
                </div>
            </div>
            <form method="POST" action="{{ route('settings.cache.clear') }}" onsubmit="return confirmClear(this)">
                @csrf
                <button type="submit" class="btn btn-primary" id="clearCacheBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg>
                    Clear All Caches
                </button>
            </form>
        </div>

        <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(170px,1fr));gap:10px;">
            @php
                $cacheItems = [
                    ['label' => 'Application Cache', 'desc' => 'Runtime data & sessions', 'icon' => '<path d="M5 12h14M12 5l7 7-7 7"/>'],
                    ['label' => 'Config Cache',      'desc' => 'Compiled configuration',  'icon' => '<circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14"/>'],
                    ['label' => 'Route Cache',       'desc' => 'Cached URL routes',        'icon' => '<line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/>'],
                    ['label' => 'View Cache',        'desc' => 'Compiled Blade templates', 'icon' => '<path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/>'],
                    ['label' => 'Compiled Files',    'desc' => 'Bootstrap class map',      'icon' => '<polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/>'],
                    ['label' => 'Media Temp Files',  'desc' => 'Leftover upload temp dir', 'icon' => '<polyline points="3 6 5 6 21 6"/><path d="M19 6l-1 14a2 2 0 01-2 2H8a2 2 0 01-2-2L5 6"/>'],
                ];
            @endphp
            @foreach($cacheItems as $item)
            <div style="background:#1a1e28;border-radius:8px;padding:12px 14px;">
                <div style="display:flex;align-items:center;gap:8px;margin-bottom:4px;">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2">{!! $item['icon'] !!}</svg>
                    <span style="font-size:0.8rem;font-weight:600;color:#c8cad8;">{{ $item['label'] }}</span>
                </div>
                <div style="font-size:0.72rem;color:#4a5068;">{{ $item['desc'] }}</div>
            </div>
            @endforeach
        </div>
        </div>
    </div>

    {{-- ── Database Backups ── --}}
    <div class="card" style="grid-column:1/-1;">
        <div class="card-bd">
        <div style="display:flex;align-items:center;justify-content:space-between;flex-wrap:wrap;gap:12px;margin-bottom:20px;">
            <div style="display:flex;align-items:center;gap:10px;">
                <div style="width:36px;height:36px;background:#1a1e28;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                </div>
                <div>
                    <div style="font-weight:700;font-size:0.95rem;">Database Backups</div>
                    <div style="font-size:0.78rem;color:#4a5068;margin-top:1px;">Runs automatically every night at 2:00 AM &middot; kept per retention policy</div>
                </div>
            </div>
            <form method="POST" action="{{ route('settings.backups.run') }}" onsubmit="return confirmBackup(this)">
                @csrf
                <button type="submit" class="btn btn-primary" id="runBackupBtn">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
                    Backup Now
                </button>
            </form>
        </div>

        <table class="table">
            <thead>
                <tr>
                    <th>Created</th>
                    <th>Size</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @forelse($backups as $backup)
                <tr>
                    <td class="td-header">{{ $backup['date']->format('M d, Y g:i A') }}</td>
                    <td class="text-muted" data-label="Size">{{ $backup['size'] }}</td>
                    <td class="td-actions">
                        <div class="row" style="gap:4px;">
                            <a href="{{ route('settings.backups.download', [$backup['disk'], $backup['filename']]) }}" class="btn btn-secondary btn-xs">Download</a>
                            <form method="POST" action="{{ route('settings.backups.destroy', [$backup['disk'], $backup['filename']]) }}"
                                  onsubmit="return confirm('Delete this backup? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-danger btn-xs">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="3" class="td-empty">
                    <div class="empty-state"><p>No backups yet. Click "Backup Now" to create the first one.</p></div>
                </td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    {{-- ── System Info ── --}}
    <div class="card">
        <div class="card-bd">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <div style="width:36px;height:36px;background:#1a1e28;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><rect x="2" y="3" width="20" height="14" rx="2"/><line x1="8" y1="21" x2="16" y2="21"/><line x1="12" y1="17" x2="12" y2="21"/></svg>
            </div>
            <div>
                <div style="font-weight:700;font-size:0.95rem;">System Information</div>
                <div style="font-size:0.78rem;color:#4a5068;margin-top:1px;">Runtime &amp; environment details</div>
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            @php
                $rows = [
                    ['PHP Version',    $info['php_version']],
                    ['Laravel',        $info['laravel_version']],
                    ['Environment',    $info['app_env']],
                    ['Debug Mode',     $info['app_debug']],
                    ['Database',       strtoupper($info['db_driver'])],
                    ['Cache Driver',   strtoupper($info['cache_driver'])],
                    ['Queue Driver',   strtoupper($info['queue_driver'])],
                ];
            @endphp
            @foreach($rows as [$label, $value])
            <tr style="border-bottom:1px solid #1e2230;">
                <td style="padding:9px 0;font-size:0.8rem;color:#4a5068;width:50%;">{{ $label }}</td>
                <td style="padding:9px 0;font-size:0.8rem;font-weight:600;text-align:right;">{{ $value }}</td>
            </tr>
            @endforeach
        </table>
        </div>
    </div>

    {{-- ── Storage Info ── --}}
    <div class="card">
        <div class="card-bd">
        <div style="display:flex;align-items:center;gap:10px;margin-bottom:18px;">
            <div style="width:36px;height:36px;background:#1a1e28;border-radius:8px;display:flex;align-items:center;justify-content:center;">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="#6c63ff" stroke-width="2"><ellipse cx="12" cy="5" rx="9" ry="3"/><path d="M21 12c0 1.66-4 3-9 3s-9-1.34-9-3"/><path d="M3 5v14c0 1.66 4 3 9 3s9-1.34 9-3V5"/></svg>
            </div>
            <div>
                <div style="font-weight:700;font-size:0.95rem;">Storage Usage</div>
                <div style="font-size:0.78rem;color:#4a5068;margin-top:1px;">Disk usage breakdown</div>
            </div>
        </div>
        <table style="width:100%;border-collapse:collapse;">
            @php
                $storageRows = [
                    ['Database (' . strtoupper($info['db_driver']) . ')', $info['db_size']],
                    ['App Storage',       $info['storage_size']],
                    ['Media Library',     $info['media_size']],
                    ['Cache Files',       $info['cache_size']],
                ];
            @endphp
            @foreach($storageRows as [$label, $value])
            <tr style="border-bottom:1px solid #1e2230;">
                <td style="padding:9px 0;font-size:0.8rem;color:#4a5068;width:50%;">{{ $label }}</td>
                <td style="padding:9px 0;font-size:0.8rem;font-weight:600;text-align:right;">{{ $value }}</td>
            </tr>
            @endforeach
        </table>
        </div>
    </div>

</div>

<script>
function confirmClear(form) {
    var btn = document.getElementById('clearCacheBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg> Clearing…';
    return true;
}
function confirmBackup(form) {
    var btn = document.getElementById('runBackupBtn');
    btn.disabled = true;
    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="animation:spin 1s linear infinite"><polyline points="23 4 23 10 17 10"/><polyline points="1 20 1 14 7 14"/><path d="M3.51 9a9 9 0 0114.85-3.36L23 10M1 14l4.64 4.36A9 9 0 0020.49 15"/></svg> Backing up…';
    return true;
}
</script>
<style>
@keyframes spin { from { transform:rotate(0deg); } to { transform:rotate(360deg); } }
</style>
@endsection
