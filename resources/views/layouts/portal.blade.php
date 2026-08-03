<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Client Portal')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet" />
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0d0f14; color: #e8eaf0; font-family: system-ui, -apple-system, sans-serif; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        .portal-topbar { background: #13161d; border-bottom: 1px solid #252936; padding: 0 20px; height: 60px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 30; }
        .portal-brand { font-size: 1.05rem; font-weight: 800; letter-spacing: -0.03em; display: inline-flex; align-items: center; }
        .portal-brand span { color: #f2b705; }
        .portal-brand img { max-width: 160px; object-fit: contain; }
        .portal-nav { display: flex; align-items: center; gap: 4px; }
        .portal-nav a { padding: 7px 12px; border-radius: 7px; font-size: 0.845rem; font-weight: 500; color: #6b7590; }
        .portal-nav a:hover { background: #1a1e28; color: #c8cce0; }
        .portal-nav a.active { background: rgba(242,183,5,0.12); color: #f2b705; }
        .portal-user { display: flex; align-items: center; gap: 10px; }
        .portal-user span { font-size: 0.8rem; color: #6b7590; }
        .client-id-badge { background: rgba(242,183,5,0.12); border: 1px solid rgba(242,183,5,0.3); color: #f8d878 !important; font-size: 0.72rem !important; font-weight: 700; padding: 3px 10px; border-radius: 20px; letter-spacing: 0.03em; }
        .portal-main { max-width: 960px; margin: 0 auto; padding: 28px 20px; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 7px; font-size: 0.845rem; font-weight: 500; cursor: pointer; border: none; transition: all 0.12s; }
        .btn-primary { background: #f2b705; color: #1a1024; }
        .btn-primary:hover { background: #ffc933; }
        .btn-secondary { background: #1a1e28; color: #c8cce0; border: 1px solid #252936; }
        .btn-secondary:hover { background: #252936; }
        .btn-sm { padding: 5px 10px; font-size: 0.78rem; }
        .card { background: #13161d; border: 1px solid #252936; border-radius: 10px; }
        .card-hd { padding: 14px 18px; border-bottom: 1px solid #252936; display: flex; align-items: center; justify-content: space-between; }
        .card-hd h3 { font-size: 0.9rem; font-weight: 700; }
        .card-bd { padding: 16px 18px; }
        .g4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .stat-card { background: #13161d; border: 1px solid #252936; border-radius: 10px; padding: 18px 20px; }
        .stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #4a5068; margin-bottom: 8px; }
        .stat-value { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.04em; color: #e8eaf0; line-height: 1; margin-bottom: 8px; }
        .stat-sub { font-size: 0.78rem; color: #4a5068; }
        .row { display: flex; align-items: center; }
        .row.center { align-items: center; }
        .row.between { justify-content: space-between; }
        .text-sm { font-size: 0.845rem; }
        .text-xs { font-size: 0.72rem; }
        .text-muted { color: #6b7590; }
        .text-faint { color: #4a5068; }
        .fw600 { font-weight: 600; }
        .fw700 { font-weight: 700; }
        .link { color: #f2b705; }
        .link:hover { text-decoration: underline; }
        .currency-symbol { font-size: 1.3em; font-weight: 700; vertical-align: -0.03em; }
        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 0.72rem; font-weight: 600; }
        .badge-todo { background: rgba(107,117,144,0.15); color: #9ca3af; }
        .badge-done { background: rgba(74,222,128,0.12); color: #4ade80; }
        .badge-active { background: rgba(74,222,128,0.12); color: #4ade80; }
        .badge-inactive { background: rgba(107,117,144,0.12); color: #6b7590; }
        .badge-prospect { background: rgba(251,191,36,0.12); color: #fbbf24; }
        .badge-review { background: rgba(251,191,36,0.15); color: #fbbf24; }
        .badge-revision { background: rgba(248,113,113,0.15); color: #f87171; }

        /* Comment thread + attachment previews (mirrors admin task page) */
        .comment-list { display:flex;flex-direction:column;gap:10px; }
        .comment-item { display:flex;gap:10px;align-items:flex-start; }
        .comment-avatar { width:32px;height:32px;border-radius:50%;background:#1a1e28;display:flex;align-items:center;justify-content:center;font-size:0.75rem;font-weight:700;color:#f2b705;flex-shrink:0; }
        .comment-bubble { flex:1;background:#1a1e28;border:1px solid #252936;border-radius:10px;padding:10px 12px; }
        .comment-meta { display:flex;align-items:center;justify-content:space-between;margin-bottom:5px; }
        .comment-author { font-size:0.8rem;font-weight:600;color:#c8cce0; }
        .comment-time { font-size:0.7rem;color:#4a5068; }
        .comment-body { font-size:0.845rem;color:#a0a6be;line-height:1.55;white-space:pre-line; }
        .comment-attachments { display:flex;flex-direction:column;gap:8px;margin-top:10px; }
        .comment-img { max-width:100%;max-height:320px;border-radius:8px;border:1px solid #252936;display:block;cursor:zoom-in; }
        .comment-video { max-width:100%;max-height:320px;border-radius:8px;border:1px solid #252936;display:block;background:#000; }
        .comment-file-row { display:flex;align-items:center;gap:6px;font-size:0.8rem;color:#a0a6be; }
        .comment-textarea { width:100%;background:#1a1e28;border:1px solid #252936;border-radius:8px;padding:10px 12px;color:#c8cce0;font-size:0.845rem;resize:vertical;min-height:80px;font-family:inherit; }
        .comment-textarea:focus { outline:none;border-color:#f2b705; }
        .comment-attach-btn { background:#1a1e28;border:1px solid #252936;border-radius:6px;padding:5px 9px;cursor:pointer;font-size:0.8rem;color:#6b7590;display:inline-flex;align-items:center;gap:5px; }
        .comment-attach-btn:hover { border-color:#f2b705;color:#c8cce0; }
        .comment-attach-preview { display:flex;flex-wrap:wrap;gap:8px; }
        .comment-attach-preview:not(:empty) { margin-top:10px; }
        .comment-attach-chip { position:relative;width:64px;height:64px;border-radius:8px;overflow:hidden;background:#0e1017;border:1px solid #252936;display:flex;align-items:center;justify-content:center;flex-shrink:0; }
        .comment-attach-chip img, .comment-attach-chip video { width:100%;height:100%;object-fit:cover; }
        .comment-attach-chip .attach-play { position:absolute;inset:0;display:flex;align-items:center;justify-content:center;color:#fff;font-size:1rem;background:rgba(0,0,0,0.25); }
        .comment-attach-chip .attach-remove { position:absolute;top:2px;right:2px;width:18px;height:18px;border-radius:50%;background:rgba(0,0,0,0.7);color:#fff;border:none;font-size:12px;cursor:pointer;display:flex;align-items:center;justify-content:center;padding:0; }
        .comment-form.drag-over .comment-textarea { border-color:#f2b705;background:rgba(242,183,5,0.05); }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 10px 14px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #4a5068; border-bottom: 1px solid #252936; }
        .table td { padding: 11px 14px; font-size: 0.845rem; border-bottom: 1px solid #1a1e28; vertical-align: middle; }
        .table tbody tr:last-child td { border-bottom: none; }
        .empty-state { text-align: center; padding: 40px 20px; color: #4a5068; }
        .empty-state p { font-size: 0.875rem; margin-bottom: 14px; }
        .page-hd { margin-bottom: 20px; }
        .page-hd h2 { font-size: 1.4rem; font-weight: 800; letter-spacing: -0.03em; }
        .page-hd p { font-size: 0.845rem; color: #6b7590; margin-top: 3px; }
        .detail-row { display: grid; grid-template-columns: 130px 1fr; gap: 8px 12px; align-items: center; }
        .detail-label { font-size: 0.72rem; font-weight: 600; color: #4a5068; text-transform: uppercase; letter-spacing: 0.06em; }
        .detail-value { font-size: 0.845rem; color: #c8cce0; }
        .alert { padding: 10px 14px; border-radius: 8px; font-size: 0.845rem; margin-bottom: 14px; }
        .alert-success { background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.2); color: #4ade80; }
        .alert-error { background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.2); color: #f87171; }
        .pager { display: flex; align-items: center; gap: 4px; justify-content: center; padding-top: 14px; }
        .pager a, .pager span { padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; color: #6b7590; background: #1a1e28; border: 1px solid #252936; }
        .pager .pager-active { background: rgba(242,183,5,0.15); color: #f2b705; border-color: rgba(242,183,5,0.3); }

        @media (max-width: 768px) {
            .g4 { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .g2 { grid-template-columns: 1fr; gap: 12px; }
            .portal-main { padding: 16px 14px; }
            .page-hd.row.between { flex-direction: column; align-items: flex-start; gap: 10px; }
            .portal-user span:not(.client-id-badge) { display: none; }
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; }
            .table tr { background: #13161d; border: 1px solid #252936; border-radius: 10px; padding: 12px 14px; margin-bottom: 10px; }
            .table tbody tr:last-child { margin-bottom: 0; }
            .table td { border-bottom: 1px solid #1a1e28; padding: 9px 0; display: flex; align-items: center; justify-content: space-between; gap: 10px; text-align: right; }
            .table td:last-child { border-bottom: none; }
            .table td[data-label]::before { content: attr(data-label); font-size: 0.64rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #4a5068; text-align: left; flex-shrink: 0; }
            .table td.td-header { display: block; text-align: left; padding-top: 0; padding-bottom: 10px; margin-bottom: 2px; border-bottom: 1px solid #252936; }
            .table td.td-actions { display: block; text-align: left; padding-top: 10px; }
            .table td.td-empty { display: block; text-align: center; border-bottom: none; padding: 4px 0; }
        }
    </style>
    @stack('styles')
</head>
<body>
<div class="portal-topbar">
    <div class="row" style="gap:24px;">
        <a href="{{ route('client.dashboard') }}" class="portal-brand">
            @if($logoUrl = app_logo_url())
                <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
            @else
                {{ config('app.name') }}
            @endif
        </a>
        <nav class="portal-nav">
            <a href="{{ route('client.dashboard') }}" class="{{ request()->routeIs('client.dashboard') ? 'active' : '' }}">Dashboard</a>
            <a href="{{ route('client.content.index') }}" class="{{ request()->routeIs('client.content.*') ? 'active' : '' }}">Content</a>
            <a href="{{ route('client.reports.index') }}" class="{{ request()->routeIs('client.reports.*') ? 'active' : '' }}">Reports</a>
        </nav>
    </div>
    <div class="portal-user">
        @if($portalClient = Auth::user()->client)
        <span class="client-id-badge" title="Your Client ID — quote this when contacting support">ID: {{ $portalClient->id }}</span>
        @endif
        <span>{{ Auth::user()->name }}</span>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm">Log out</button>
        </form>
    </div>
</div>
<div class="portal-main">
    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="alert alert-error">{{ session('error') }}</div>
    @endif
    @yield('content')
</div>
@stack('scripts')
</body>
</html>
