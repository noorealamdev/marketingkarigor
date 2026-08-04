<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }} — @yield('title', 'Dashboard')</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link href="{{ asset('build/assets/app.css') }}" rel="stylesheet" />
    <script src="{{ asset('build/assets/app.js') }}"></script>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body { background: #0d0f14; color: #e8eaf0; font-family: system-ui, -apple-system, sans-serif; min-height: 100vh; }
        a { color: inherit; text-decoration: none; }
        .sidebar { background: #13161d; border-right: 1px solid #252936; width: 230px; height: 100vh; height: 100dvh; position: fixed; top: 0; left: 0; z-index: 40; display: flex; flex-direction: column; overflow: hidden; }
        .sidebar-logo { padding: 22px 18px 18px; border-bottom: 1px solid #252936; }
        .sidebar-logo h1 { font-size: 1.05rem; font-weight: 800; letter-spacing: -0.03em; line-height: 1.25; }
        .sidebar-logo h1 span { color: #6c63ff; }
        .sidebar-logo small { font-size: 0.68rem; color: #4a5068; letter-spacing: 0.04em; text-transform: uppercase; }
        .sidebar-logo img { max-width: 100%; object-fit: contain; }
        .nav-section { padding: 14px 10px 0; }
        .nav-label { font-size: 0.63rem; font-weight: 700; letter-spacing: 0.1em; text-transform: uppercase; color: #3a4060; padding: 0 8px 8px; }
        .nav-item { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 7px; color: #6b7590; font-size: 0.845rem; font-weight: 500; margin-bottom: 1px; transition: all 0.12s; }
        .nav-item:hover { background: #1a1e28; color: #c8cce0; }
        .nav-item.active { background: rgba(108,99,255,0.12); color: #6c63ff; }
        .nav-item svg { flex-shrink: 0; width: 15px; height: 15px; }
        .main { margin-left: 230px; min-height: 100vh; display: flex; flex-direction: column; }
        .topbar { background: #13161d; border-bottom: 1px solid #252936; padding: 0 24px; height: 56px; display: flex; align-items: center; justify-content: space-between; position: sticky; top: 0; z-index: 30; }
        .topbar-title { font-size: 0.875rem; color: #6b7590; }
        .page { padding: 24px; flex: 1; }
        .btn { display: inline-flex; align-items: center; gap: 6px; padding: 7px 14px; border-radius: 7px; font-size: 0.845rem; font-weight: 500; cursor: pointer; border: none; transition: all 0.12s; }
        .btn-primary { background: #6c63ff; color: #fff; }
        .btn-primary:hover { background: #7b73ff; }
        .btn-secondary { background: #1a1e28; color: #c8cce0; border: 1px solid #252936; }
        .btn-secondary:hover { background: #252936; }
        .btn-danger { background: rgba(248,113,113,0.1); color: #f87171; border: 1px solid rgba(248,113,113,0.2); }
        .btn-danger:hover { background: rgba(248,113,113,0.2); }
        .btn-sm { padding: 5px 10px; font-size: 0.78rem; }
        .btn-xs { padding: 3px 8px; font-size: 0.72rem; }
        .card { background: #13161d; border: 1px solid #252936; border-radius: 10px; }
        .card-hd { padding: 14px 18px; border-bottom: 1px solid #252936; display: flex; align-items: center; justify-content: space-between; }
        .card-hd h3 { font-size: 0.9rem; font-weight: 700; }
        .card-bd { padding: 16px 18px; }
        .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 16px; }
        .g3 { display: grid; grid-template-columns: repeat(3, 1fr); gap: 14px; }
        .g4 { display: grid; grid-template-columns: repeat(4, 1fr); gap: 14px; }
        .stat-card { background: #13161d; border: 1px solid #252936; border-radius: 10px; padding: 18px 20px; }
        .stat-label { font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #4a5068; margin-bottom: 8px; }
        .stat-value { font-size: 1.9rem; font-weight: 800; letter-spacing: -0.04em; color: #e8eaf0; line-height: 1; margin-bottom: 8px; }
        .stat-sub { font-size: 0.78rem; color: #4a5068; }
        .mb3 { margin-bottom: 8px; }
        .mb4 { margin-bottom: 12px; }
        .mb6 { margin-bottom: 16px; }
        .row { display: flex; align-items: center; }
        .row.center { align-items: center; }
        .row.between { justify-content: space-between; }
        .text-sm { font-size: 0.845rem; }
        .text-xs { font-size: 0.72rem; }
        .text-muted { color: #6b7590; }
        .text-faint { color: #4a5068; }
        .fw600 { font-weight: 600; }
        .fw700 { font-weight: 700; }
        .link { color: #6c63ff; }
        .link:hover { text-decoration: underline; }
        .desc-field a.desc-link { color: #6c63ff; text-decoration: none; }
        .desc-field a.desc-link:hover { text-decoration: underline; }
        .avatar { width: 32px; height: 32px; border-radius: 50%; background: linear-gradient(135deg,#6c63ff,#a78bfa); display: flex; align-items: center; justify-content: center; font-size: 0.8rem; font-weight: 700; color: #fff; }
        .badge { display: inline-flex; align-items: center; padding: 2px 8px; border-radius: 999px; font-size: 0.72rem; font-weight: 600; }
        .badge-todo { background: rgba(107,117,144,0.15); color: #9ca3af; }
        .badge-doing, .badge-in_progress, .badge-in-progress { background: rgba(96,165,250,0.15); color: #60a5fa; }
        .badge-review { background: rgba(251,191,36,0.15); color: #fbbf24; }
        .badge-done { background: rgba(74,222,128,0.12); color: #4ade80; }
        .currency-symbol { font-size: 1.3em; font-weight: 700; vertical-align: -0.03em; }
        .badge-low { background: rgba(107,117,144,0.12); color: #6b7590; }
        .badge-medium { background: rgba(251,191,36,0.12); color: #fbbf24; }
        .badge-high { background: rgba(251,146,60,0.15); color: #fb923c; }
        .badge-urgent { background: rgba(248,113,113,0.15); color: #f87171; }
        .badge-active { background: rgba(74,222,128,0.12); color: #4ade80; }
        .badge-completed { background: rgba(96,165,250,0.12); color: #60a5fa; }
        .badge-on_hold { background: rgba(251,191,36,0.12); color: #fbbf24; }
        .badge-cancelled { background: rgba(248,113,113,0.12); color: #f87171; }
        .badge-draft { background: rgba(167,139,250,0.12); color: #a78bfa; }
        .badge-sent { background: rgba(96,165,250,0.12); color: #60a5fa; }
        .badge-paid { background: rgba(74,222,128,0.12); color: #4ade80; }
        .badge-overdue { background: rgba(248,113,113,0.12); color: #f87171; }
        .badge-client { background: rgba(96,165,250,0.12); color: #60a5fa; }
        .badge-role { background: rgba(107,117,144,0.15); color: #9ca3af; }
        .table { width: 100%; border-collapse: collapse; }
        .table th { text-align: left; padding: 10px 14px; font-size: 0.72rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; color: #4a5068; border-bottom: 1px solid #252936; }
        .table td { padding: 11px 14px; font-size: 0.845rem; border-bottom: 1px solid #1a1e28; vertical-align: middle; }
        .table tbody tr:last-child td { border-bottom: none; }
        .table tbody tr:hover td { background: rgba(255,255,255,0.015); }
        .form-label { display: block; font-size: 0.78rem; font-weight: 600; color: #6b7590; margin-bottom: 5px; }
        .form-control { width: 100%; background: #1a1e28; border: 1px solid #252936; border-radius: 7px; padding: 8px 11px; color: #c8cce0; font-size: 0.845rem; transition: border-color 0.12s; }
        .form-control:focus { outline: none; border-color: #6c63ff; }
        .form-group { margin-bottom: 14px; }
        /* Client picker (searchable client select) */
        .client-picker { position: relative; }
        .client-picker-input-wrap { position: relative; }
        .client-picker-clear { position: absolute; top: 50%; right: 6px; transform: translateY(-50%); width: 20px; height: 20px; border: none; background: transparent; color: #4a5068; font-size: 1.1rem; line-height: 1; cursor: pointer; align-items: center; justify-content: center; border-radius: 50%; }
        .client-picker-clear:hover { background: #252936; color: #c8cce0; }
        .client-picker-search { padding-right: 28px; }
        .client-picker-dropdown { display: none; position: absolute; top: calc(100% + 4px); left: 0; right: 0; max-height: 220px; overflow-y: auto; background: #13161d; border: 1px solid #252936; border-radius: 8px; box-shadow: 0 8px 24px rgba(0,0,0,0.4); z-index: 60; }
        .client-picker-dropdown.open { display: block; }
        .client-picker-option { padding: 8px 12px; font-size: 0.83rem; color: #c8cce0; cursor: pointer; }
        .client-picker-option:hover, .client-picker-option.active { background: rgba(108,99,255,0.15); color: #a89fff; }
        .client-picker-empty { display: none; padding: 10px 12px; font-size: 0.8rem; color: #4a5068; }
        .client-picker-empty.show { display: block; }
        .alert { padding: 10px 14px; border-radius: 8px; font-size: 0.845rem; margin-bottom: 14px; }
        .alert-success { background: rgba(74,222,128,0.08); border: 1px solid rgba(74,222,128,0.2); color: #4ade80; }
        .alert-error { background: rgba(248,113,113,0.08); border: 1px solid rgba(248,113,113,0.2); color: #f87171; }
        .empty-state { text-align: center; padding: 40px 20px; color: #4a5068; }
        .empty-state p { font-size: 0.875rem; margin-bottom: 14px; }
        .page-hd { margin-bottom: 20px; }
        .page-hd h2 { font-size: 1.5rem; font-weight: 800; letter-spacing: -0.03em; }
        .page-hd p { font-size: 0.845rem; color: #6b7590; margin-top: 3px; }
        .detail-row { display: grid; grid-template-columns: 130px 1fr; gap: 8px 12px; align-items: center; }
        .detail-label { font-size: 0.72rem; font-weight: 600; color: #4a5068; text-transform: uppercase; letter-spacing: 0.06em; }
        .detail-value { font-size: 0.845rem; color: #c8cce0; }
        .progress-bar { height: 6px; background: #1a1e28; border-radius: 3px; overflow: hidden; }
        .progress-fill { height: 100%; background: linear-gradient(90deg,#6c63ff,#a78bfa); border-radius: 3px; transition: width 0.3s; }
        .filter-row { display: flex; gap: 8px; flex-wrap: nowrap; align-items: center; overflow-x: auto; }
        .filter-row .form-control { width: auto; min-width: 0; flex-shrink: 1; }
        .filter-row input.form-control { min-width: 140px; }
        .filter-row select.form-control { flex: 0 0 auto; }
        .filter-row .client-picker { flex: 0 0 auto; width: 200px; }
        .filter-row .btn { flex-shrink: 0; white-space: nowrap; }
        .user-wrap { display: flex; align-items: center; gap: 10px; padding: 10px; }
        .user-name { font-size: 0.845rem; font-weight: 600; }
        .user-email { font-size: 0.72rem; color: #4a5068; }
        .sidebar-footer { margin-top: auto; padding: 10px; border-top: 1px solid #252936; }
        .pager { display: flex; align-items: center; gap: 4px; justify-content: center; padding-top: 14px; }
        .pager a, .pager span { padding: 5px 10px; border-radius: 6px; font-size: 0.78rem; color: #6b7590; background: #1a1e28; border: 1px solid #252936; }
        .pager a:hover { background: #252936; color: #e8eaf0; }
        .pager .pager-active { background: rgba(108,99,255,0.15); color: #6c63ff; border-color: rgba(108,99,255,0.3); }

        /* Notification bell */
        .notif-bell { position: relative; display: flex; align-items: center; justify-content: center; width: 46px; height: 46px; border-radius: 10px; color: #6b7590; cursor: pointer; transition: all 0.12s; background: transparent; border: none; }
        .notif-bell:hover { background: #1a1e28; color: #c8cce0; }
        .notif-badge { position: absolute; top: 5px; right: 5px; width: 17px; height: 17px; border-radius: 50%; background: #6c63ff; color: #fff; font-size: 0.62rem; font-weight: 700; display: flex; align-items: center; justify-content: center; border: 2px solid #13161d; }
        .notif-dropdown { position: absolute; top: calc(100% + 8px); right: 0; width: 340px; background: #13161d; border: 1px solid #252936; border-radius: 12px; box-shadow: 0 8px 32px rgba(0,0,0,0.4); z-index: 100; overflow: hidden; display: none; }
        .notif-dropdown.open { display: block; }
        .notif-dropdown-hd { padding: 12px 16px; border-bottom: 1px solid #252936; display: flex; align-items: center; justify-content: space-between; }
        .notif-dropdown-hd span { font-size: 0.845rem; font-weight: 700; color: #c8cce0; }
        .notif-item { display: flex; align-items: flex-start; gap: 10px; padding: 10px 14px; border-bottom: 1px solid #1a1e28; transition: background 0.1s; }
        .notif-item:hover { background: #1a1e28; }
        .notif-item:last-child { border-bottom: none; }
        .notif-item-avatar { width: 30px; height: 30px; border-radius: 50%; background: rgba(108,99,255,0.2); display: flex; align-items: center; justify-content: center; font-size: 0.72rem; font-weight: 700; color: #6c63ff; flex-shrink: 0; margin-top: 1px; }
        .notif-item-title { font-size: 0.8rem; color: #c8cce0; line-height: 1.4; margin-bottom: 2px; }
        .notif-item-time { font-size: 0.7rem; color: #4a5068; }
        .notif-item.read .notif-item-title { color: #6b7590; }
        .notif-dropdown-ft { padding: 10px 14px; text-align: center; }
        .notif-wrap { position: relative; }

        /* ── Hamburger / mobile sidebar toggle ── */
        .sidebar-toggle {
            display: none;
            align-items: center;
            justify-content: center;
            width: 36px; height: 36px;
            background: transparent;
            border: 1px solid #252936;
            border-radius: 7px;
            color: #c8cce0;
            cursor: pointer;
            flex-shrink: 0;
        }
        .sidebar-toggle:hover { background: #1a1e28; }

        /* Dim overlay behind sidebar on mobile */
        .sidebar-overlay {
            display: none;
            position: fixed;
            inset: 0;
            background: rgba(0,0,0,0.55);
            z-index: 39;
        }
        .sidebar-overlay.open { display: block; }

        /* ── Responsive breakpoints ── */

        /* Tablet: 1024px and below */
        @media (max-width: 1024px) {
            .g4 { grid-template-columns: repeat(2, 1fr); }
        }

        /* Mobile: 768px and below */
        @media (max-width: 768px) {
            /* Sidebar becomes a slide-in drawer */
            .sidebar {
                transform: translateX(-100%);
                transition: transform 0.22s cubic-bezier(.4,0,.2,1);
                z-index: 50;
                width: 240px;
            }
            .sidebar.open { transform: translateX(0); }

            /* Main fills full width */
            .main { margin-left: 0; }

            /* Topbar layout on mobile */
            .topbar { padding: 0 14px; gap: 8px; }
            .topbar-title { font-size: 0.8rem; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; flex: 1; min-width: 0; }

            /* Show hamburger */
            .sidebar-toggle { display: flex; }

            /* Page padding */
            .page { padding: 14px; }

            /* Grid helpers */
            .g4 { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .g2 { grid-template-columns: 1fr; gap: 12px; }
            .g3 { grid-template-columns: 1fr !important; }

            /* Page header: stack vertically */
            .page-hd.row.between {
                flex-direction: column;
                align-items: flex-start;
                gap: 10px;
            }
            .page-hd h2 { font-size: 1.25rem; }

            /* Stat cards slightly smaller */
            .stat-value { font-size: 1.6rem; }

            /* Tables become stacked cards instead of scrolling horizontally */
            .table thead { display: none; }
            .table, .table tbody, .table tr, .table td { display: block; width: 100%; min-width: 0; }
            .table tr {
                background: #13161d;
                border: 1px solid #252936;
                border-radius: 10px;
                padding: 12px 14px;
                margin-bottom: 10px;
            }
            .table tbody tr:last-child { margin-bottom: 0; }
            .table tbody tr:hover td { background: transparent; }
            .table td {
                border-bottom: 1px solid #1a1e28;
                padding: 9px 0;
                display: flex;
                align-items: center;
                justify-content: space-between;
                max-width: none !important;
                gap: 10px;
                text-align: right;
            }
            .table td:last-child { border-bottom: none; }
            .table td[data-label]::before {
                content: attr(data-label);
                font-size: 0.64rem;
                font-weight: 700;
                text-transform: uppercase;
                letter-spacing: 0.05em;
                color: #4a5068;
                text-align: left;
                flex-shrink: 0;
            }
            .table td.td-header {
                display: block;
                text-align: left;
                padding-top: 0;
                padding-bottom: 10px;
                margin-bottom: 2px;
                border-bottom: 1px solid #252936;
            }
            .table td.td-actions {
                display: block;
                text-align: left;
                padding-top: 10px;
            }
            .table td.td-actions .row { flex-wrap: wrap; }
            .table td.td-empty { display: block; text-align: center; border-bottom: none; padding: 4px 0; }

            /* Detail rows: full width */
            .detail-row { grid-template-columns: 1fr; gap: 3px 0; }
            .detail-label { margin-top: 10px; }

            /* Filter rows: allow wrap */
            .filter-row { flex-wrap: wrap; }
            .filter-row input.form-control { min-width: 120px; }

            /* Buttons row on page-hd */
            .page-hd .row { flex-wrap: wrap; gap: 6px; }

            /* Notification dropdown width */
            .notif-dropdown { width: 290px; right: -60px; }
        }

        /* Small mobile: 480px */
        @media (max-width: 480px) {
            .g4 { grid-template-columns: 1fr 1fr; gap: 8px; }
            .stat-card { padding: 14px 14px; }
            .stat-value { font-size: 1.4rem; }
            .stat-label { font-size: 0.68rem; }
            .page { padding: 12px; }
            .topbar { height: 50px; }
            /* Full-width notification dropdown */
            .notif-dropdown { width: calc(100vw - 28px); right: -14px; }
        }
    </style>
    @stack('styles')
    @livewireStyles
</head>
<body>
<div class="sidebar">
    <div class="sidebar-logo">
        @if($logoUrl = app_logo_url())
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
        @else
            <h1>{{ config('app.name') }}</h1>
        @endif
        <small>Management Suite</small>
    </div>
    <nav style="flex:1;overflow-y:auto;padding-top:6px;">
        @php $user = Auth::user(); @endphp

        {{-- Admin-only: Dashboard & Overview --}}
        @if($user->isAdmin())
        <div class="nav-section">
            <div class="nav-label">Overview</div>
            <a href="{{ route('dashboard') }}" class="nav-item {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7" rx="1"/><rect x="14" y="3" width="7" height="7" rx="1"/><rect x="14" y="14" width="7" height="7" rx="1"/><rect x="3" y="14" width="7" height="7" rx="1"/></svg>
                Dashboard
            </a>
            <a href="{{ route('calendar.index') }}" class="nav-item {{ request()->routeIs('calendar.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                Calendar
            </a>
        </div>
        @endif

        <div class="nav-section" style="margin-top:{{ $user->isAdmin() ? '6px' : '0' }};">
            <div class="nav-label">Work</div>
            @if($user->hasAnyRole(['super-admin', 'project-manager']))
            <a href="{{ route('projects.index') }}" class="nav-item {{ request()->routeIs('projects.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 19a2 2 0 01-2 2H4a2 2 0 01-2-2V5a2 2 0 012-2h5l2 3h9a2 2 0 012 2z"/></svg>
                Projects
            </a>
            @endif
            <a href="{{ route('tasks.index') }}" class="nav-item {{ request()->routeIs('tasks.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="9 11 12 14 22 4"/><path d="M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11"/></svg>
                {{ $user->hasAnyRole(['super-admin', 'project-manager']) ? 'All Tasks' : 'My Tasks' }}
            </a>
        </div>

        @if($user->hasAnyRole(['super-admin', 'project-manager']))
        <div class="nav-section" style="margin-top:6px;">
            <div class="nav-label">Business</div>
            <a href="{{ route('clients.index') }}" class="nav-item {{ request()->routeIs('clients.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 00-3-3.87M16 3.13a4 4 0 010 7.75"/></svg>
                Clients
            </a>
            <a href="{{ route('invoices.index') }}" class="nav-item {{ request()->routeIs('invoices.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>
                Invoices
            </a>
            <a href="{{ route('reports.index') }}" class="nav-item {{ request()->routeIs('reports.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 3v18h18"/><path d="M18 17V9"/><path d="M13 17V5"/><path d="M8 17v-3"/></svg>
                Reports
            </a>
        </div>
        @endif

        @if($user->isAdmin())
        <div class="nav-section" style="margin-top:6px;">
            <div class="nav-label">Team</div>
            <a href="{{ route('team.index') }}" class="nav-item {{ request()->routeIs('team.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="8" r="4"/><path d="M6 20v-2a6 6 0 0112 0v2"/></svg>
                Members
            </a>
            <a href="{{ route('invitations.index') }}" class="nav-item {{ request()->routeIs('invitations.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                Invitations
            </a>
            <a href="{{ route('roles.index') }}" class="nav-item {{ request()->routeIs('roles.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 15a3 3 0 100-6 3 3 0 000 6z"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 11-2.83 2.83l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 11-4 0v-.09a1.65 1.65 0 00-1-1.51 1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 11-2.83-2.83l.06-.06a1.65 1.65 0 00.33-1.82 1.65 1.65 0 00-1.51-1H3a2 2 0 110-4h.09a1.65 1.65 0 001.51-1 1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 112.83-2.83l.06.06a1.65 1.65 0 001.82.33H9a1.65 1.65 0 001-1.51V3a2 2 0 114 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 112.83 2.83l-.06.06a1.65 1.65 0 00-.33 1.82V9a1.65 1.65 0 001.51 1H21a2 2 0 110 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>
                Roles &amp; Permissions
            </a>
            <a href="{{ route('salaries.index') }}" class="nav-item {{ request()->routeIs('salaries.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="12" y1="1" x2="12" y2="23"/><path d="M17 5H9.5a3.5 3.5 0 000 7h5a3.5 3.5 0 010 7H6"/></svg>
                Salaries
            </a>
            <a href="{{ route('finance.index') }}" class="nav-item {{ request()->routeIs('finance.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="5" width="20" height="14" rx="2"/><line x1="2" y1="10" x2="22" y2="10"/></svg>
                Finance
            </a>
        </div>
        <div class="nav-section" style="margin-top:6px;">
            <div class="nav-label">System</div>
            <a href="{{ route('settings.index') }}" class="nav-item {{ request()->routeIs('settings.*') ? 'active' : '' }}">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.07 4.93a10 10 0 010 14.14M4.93 4.93a10 10 0 000 14.14M12 2v2M12 20v2M2 12h2M20 12h2"/></svg>
                Settings
            </a>
        </div>
        @endif
    </nav>
    <div class="sidebar-footer">
        <a href="{{ route('profile.edit') }}" class="user-wrap" style="text-decoration:none;display:flex;align-items:center;gap:10px;padding:10px 14px;border-radius:8px;transition:background 0.12s;cursor:pointer;" onmouseover="this.style.background='#1a1e28'" onmouseout="this.style.background='transparent'">
            {!! user_avatar(Auth::user(), 32) !!}
            <div style="min-width:0;flex:1;">
                <div class="user-name">{{ Auth::user()->name }}</div>
                <div class="user-email" style="font-size:0.68rem;color:#3a4060;">Edit Profile</div>
            </div>
            <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="#3a4060" stroke-width="2"><path d="M11 4H4a2 2 0 00-2 2v14a2 2 0 002 2h14a2 2 0 002-2v-7"/><path d="M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"/></svg>
        </a>
        <form method="POST" action="{{ route('logout') }}" style="padding:4px 8px 2px;">
            @csrf
            <button type="submit" class="btn btn-secondary btn-sm" style="width:100%;justify-content:center;">
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</div>

{{-- Overlay backdrop for mobile sidebar --}}
<div class="sidebar-overlay" id="sidebarOverlay" onclick="closeSidebar()"></div>

<div class="main">
    <div class="topbar">
        <div class="row center" style="gap:10px;min-width:0;flex:1;">
            <button class="sidebar-toggle" onclick="toggleSidebar()" aria-label="Menu">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5">
                    <line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
            </button>
            <span class="topbar-title">@yield('breadcrumb', 'Dashboard')</span>
        </div>
        <div class="row center" style="gap:10px;flex-shrink:0;">

            {{-- Notification Bell --}}
            @php $unreadCount = Auth::user()->unreadNotifications->count(); @endphp
            <div class="notif-wrap" id="notifWrap">
                <button class="notif-bell" id="notifBtn" onclick="toggleNotifDropdown()" title="Notifications">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/>
                        <path d="M13.73 21a2 2 0 01-3.46 0"/>
                    </svg>
                    @if($unreadCount > 0)
                    <span class="notif-badge">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                    @endif
                </button>

                <div class="notif-dropdown" id="notifDropdown">
                    <div class="notif-dropdown-hd">
                        <span>Notifications @if($unreadCount > 0)<span style="color:#6c63ff;font-size:0.75rem;">({{ $unreadCount }} new)</span>@endif</span>
                        @if($unreadCount > 0)
                        <form method="POST" action="{{ route('notifications.mark-all-read') }}">
                            @csrf
                            <button type="submit" style="background:none;border:none;color:#6c63ff;font-size:0.75rem;cursor:pointer;padding:0;">Mark all read</button>
                        </form>
                        @endif
                    </div>

                    @php $recent = Auth::user()->notifications()->latest()->take(6)->get(); @endphp
                    @if($recent->isEmpty())
                    <div style="padding:24px;text-align:center;color:#4a5068;font-size:0.82rem;">No notifications yet</div>
                    @else
                    @foreach($recent as $n)
                    @php $d = $n->data; @endphp
                    <a href="{{ route('notifications.read', $n->id) }}" class="notif-item {{ $n->read_at ? 'read' : '' }}" style="display:flex;text-decoration:none;">
                        <div class="notif-item-avatar">{{ $d['actor_initial'] ?? '?' }}</div>
                        <div style="flex:1;min-width:0;">
                            <div class="notif-item-title">{{ Str::limit($d['title'] ?? 'Notification', 55) }}</div>
                            <div class="notif-item-time">{{ $n->created_at->diffForHumans() }}</div>
                        </div>
                        @if(!$n->read_at)
                        <div style="width:6px;height:6px;border-radius:50%;background:#6c63ff;flex-shrink:0;margin-top:6px;"></div>
                        @endif
                    </a>
                    @endforeach
                    @endif

                    <div class="notif-dropdown-ft">
                        <a href="{{ route('notifications.index') }}" style="font-size:0.78rem;color:#6c63ff;">View all notifications →</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="page">
        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="alert alert-error">{{ session('error') }}</div>
        @endif
        @yield('content')
    </div>
</div>

<script>
function toggleNotifDropdown() {
    document.getElementById('notifDropdown').classList.toggle('open');
}
document.addEventListener('click', function(e) {
    var wrap = document.getElementById('notifWrap');
    if (wrap && !wrap.contains(e.target)) {
        document.getElementById('notifDropdown').classList.remove('open');
    }
});

// ── Mobile sidebar ──
function toggleSidebar() {
    var sidebar = document.querySelector('.sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var isOpen  = sidebar.classList.toggle('open');
    overlay.classList.toggle('open', isOpen);
    document.body.style.overflow = isOpen ? 'hidden' : '';
}
function closeSidebar() {
    document.querySelector('.sidebar').classList.remove('open');
    document.getElementById('sidebarOverlay').classList.remove('open');
    document.body.style.overflow = '';
}
// Close sidebar on nav-item click (mobile navigation)
document.querySelectorAll('.nav-item').forEach(function(link) {
    link.addEventListener('click', function() {
        if (window.innerWidth <= 768) closeSidebar();
    });
});
// Close on Escape key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSidebar();
});

// ── Client picker (searchable client select) ──
document.querySelectorAll('.client-picker').forEach(function(picker) {
    var input    = picker.querySelector('.client-picker-search');
    var hidden   = picker.querySelector('.client-picker-value');
    var clearBtn = picker.querySelector('.client-picker-clear');
    var dropdown = picker.querySelector('.client-picker-dropdown');
    var empty    = picker.querySelector('.client-picker-empty');
    var options  = Array.prototype.slice.call(picker.querySelectorAll('.client-picker-option'));
    var activeIndex = -1;

    function visibleOptions() {
        return options.filter(function(o) { return o.style.display !== 'none'; });
    }
    function setActive(index) {
        visibleOptions().forEach(function(o) { o.classList.remove('active'); });
        var vis = visibleOptions();
        if (vis[index]) {
            vis[index].classList.add('active');
            vis[index].scrollIntoView({ block: 'nearest' });
        }
        activeIndex = index;
    }
    function filter() {
        var q = input.value.toLowerCase();
        var anyVisible = false;
        options.forEach(function(o) {
            var match = o.dataset.label.toLowerCase().includes(q);
            o.style.display = match ? '' : 'none';
            if (match) anyVisible = true;
        });
        empty.classList.toggle('show', !anyVisible);
        activeIndex = -1;
    }
    function open() {
        filter();
        dropdown.classList.add('open');
    }
    function close() {
        dropdown.classList.remove('open');
        activeIndex = -1;
    }
    function select(option) {
        hidden.value = option.dataset.id;
        input.value = option.dataset.id ? option.dataset.label : '';
        clearBtn.style.display = option.dataset.id ? 'flex' : 'none';
        close();
        hidden.dispatchEvent(new Event('change'));
    }

    input.addEventListener('focus', open);
    input.addEventListener('input', function() {
        hidden.value = '';
        clearBtn.style.display = 'none';
        open();
    });
    input.addEventListener('keydown', function(e) {
        var vis = visibleOptions();
        if (e.key === 'ArrowDown') {
            e.preventDefault();
            if (!dropdown.classList.contains('open')) return open();
            setActive(Math.min(activeIndex + 1, vis.length - 1));
        } else if (e.key === 'ArrowUp') {
            e.preventDefault();
            setActive(Math.max(activeIndex - 1, 0));
        } else if (e.key === 'Enter') {
            if (dropdown.classList.contains('open') && vis[activeIndex]) {
                e.preventDefault();
                select(vis[activeIndex]);
            }
        } else if (e.key === 'Escape') {
            close();
        }
    });

    options.forEach(function(option) {
        option.addEventListener('mousedown', function(e) {
            e.preventDefault(); // keep input focused so blur doesn't close before click registers
            select(option);
        });
    });

    clearBtn.addEventListener('click', function() {
        hidden.value = '';
        input.value = '';
        clearBtn.style.display = 'none';
        input.focus();
        open();
        hidden.dispatchEvent(new Event('change'));
    });

    document.addEventListener('click', function(e) {
        if (!picker.contains(e.target)) close();
    });
});
</script>
@stack('scripts')
@livewireScripts
</body>
</html>
