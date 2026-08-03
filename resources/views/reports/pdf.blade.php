<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
    .page { padding: 48px 52px; }

    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 40px; border-bottom: 3px solid #6c63ff; padding-bottom: 24px; }
    .brand h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.04em; color: #1a1a2e; }
    .brand h1 span { color: #6c63ff; }
    .brand img { max-height: 46px; max-width: 220px; }
    .brand p { font-size: 11px; color: #6b7280; margin-top: 3px; letter-spacing: 0.04em; text-transform: uppercase; }
    .report-label { text-align: right; }
    .report-label .period { font-size: 20px; font-weight: 800; color: #6c63ff; letter-spacing: -0.02em; text-transform: uppercase; }
    .report-label .range { font-size: 12px; color: #6b7280; margin-top: 4px; }

    .meta-grid { display: flex; gap: 0; margin-bottom: 36px; }
    .meta-col { flex: 1; }
    .meta-col + .meta-col { border-left: 1px solid #e5e7eb; padding-left: 28px; margin-left: 28px; }
    .meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 4px; }
    .meta-value { font-size: 13px; color: #1a1a2e; font-weight: 500; }

    .stats-grid { display: flex; gap: 16px; margin-bottom: 32px; }
    .stat-box { flex: 1; border: 1px solid #e5e7eb; border-radius: 8px; padding: 16px 18px; text-align: center; }
    .stat-box .label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #9ca3af; margin-bottom: 6px; }
    .stat-box .value { font-size: 22px; font-weight: 800; color: #6c63ff; letter-spacing: -0.03em; }

    .section { margin-bottom: 24px; }
    .section h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #6c63ff; margin-bottom: 8px; }
    .section p { font-size: 13px; color: #374151; line-height: 1.6; }
    .notes-box { background: #f8f7ff; border-left: 4px solid #6c63ff; padding: 14px 18px; border-radius: 0 6px 6px 0; }

    .footer { border-top: 1px solid #e5e7eb; padding-top: 18px; display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; }
    .footer-left { font-size: 11px; color: #9ca3af; line-height: 1.7; }
    .footer-right { font-size: 11px; color: #9ca3af; text-align: right; }
    .thank-you { font-size: 14px; font-weight: 700; color: #6c63ff; }

    @page { margin: 0; }
</style>
</head>
<body>
<div class="page">

    <div class="header">
        <div class="brand">
            @if($logoPath = app_logo_path())
                <img src="{{ $logoPath }}" alt="{{ config('app.name') }}">
            @else
                <h1>{{ config('app.name') }}</h1>
            @endif
            <p>Performance Report</p>
        </div>
        <div class="report-label">
            <div class="period">{{ $report->periodEnum()->label() }} Report</div>
            <div class="range">{{ $report->period_start->format('F d') }} – {{ $report->period_end->format('F d, Y') }}</div>
        </div>
    </div>

    <div class="meta-grid">
        <div class="meta-col">
            <div class="meta-label">Prepared For</div>
            <div class="meta-value" style="font-weight:700;font-size:14px;">{{ $report->client->name }}</div>
            @if($report->client->company)
            <div class="meta-value">{{ $report->client->company }}</div>
            @endif
        </div>
        <div class="meta-col">
            <div class="meta-label">Project</div>
            <div class="meta-value">{{ $report->project?->name ?? '—' }}</div>
        </div>
        <div class="meta-col">
            <div class="meta-label">Best Performing Post</div>
            <div class="meta-value">{{ $report->best_performing_post ?: '—' }}</div>
        </div>
    </div>

    <div class="stats-grid">
        <div class="stat-box">
            <div class="label">Reach</div>
            <div class="value">{{ $report->reach !== null ? number_format($report->reach) : '—' }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Engagement</div>
            <div class="value">{{ $report->engagement !== null ? number_format($report->engagement) : '—' }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Video Views</div>
            <div class="value">{{ $report->video_views !== null ? number_format($report->video_views) : '—' }}</div>
        </div>
        <div class="stat-box">
            <div class="label">Tasks Completed</div>
            <div class="value">{{ $report->tasksCompletedCount() }}</div>
        </div>
    </div>

    @if($report->next_plan)
    <div class="section">
        <h4>Next Period's Plan</h4>
        <div class="notes-box">
            <p>{{ $report->next_plan }}</p>
        </div>
    </div>
    @endif

    <div class="footer">
        <div class="footer-left">
            <div class="thank-you">Thank you for growing with us!</div>
            <div style="margin-top:4px;">Generated by {{ config('app.name') }} &middot; {{ now()->format('F d, Y') }}</div>
        </div>
        <div class="footer-right">
            <div>Questions? Contact us at</div>
            <div style="color:#6c63ff;font-weight:600;">{{ preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')) }}</div>
        </div>
    </div>

</div>
</body>
</html>
