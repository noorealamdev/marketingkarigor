<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
    .page { padding: 48px 52px; }

    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 36px; border-bottom: 3px solid #b8860b; padding-bottom: 22px; }
    .brand h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.04em; color: #1a1a2e; }
    .brand img { max-height: 46px; max-width: 220px; }
    .brand p { font-size: 11px; color: #6b7280; margin-top: 3px; letter-spacing: 0.04em; text-transform: uppercase; }
    .report-label { text-align: right; }
    .report-label .period { font-size: 18px; font-weight: 800; color: #b8860b; letter-spacing: -0.02em; text-transform: uppercase; }
    .report-label .range { font-size: 12px; color: #6b7280; margin-top: 4px; }
    .report-label .title { font-size: 12px; color: #1a1a2e; font-weight: 600; margin-top: 4px; }

    .meta-grid { display: table; width: 100%; table-layout: fixed; margin-bottom: 32px; }
    .meta-col { display: table-cell; vertical-align: top; width: 33.33%; }
    .meta-col + .meta-col { border-left: 1px solid #e5e7eb; padding-left: 28px; }
    .meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 4px; }
    .meta-value { font-size: 13px; color: #1a1a2e; font-weight: 500; }

    .stats-table { width: 100%; border-collapse: separate; border-spacing: 10px; margin: 0 -10px 20px; }
    .stats-table td { width: 33.33%; vertical-align: top; }
    .stat-box { border: 1px solid #e5e7eb; border-radius: 8px; padding: 14px 16px; text-align: center; }
    .stat-box .label { font-size: 9.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.05em; color: #9ca3af; margin-bottom: 6px; }
    .stat-box .value { font-size: 19px; font-weight: 800; color: #b8860b; letter-spacing: -0.02em; }

    .section { margin-bottom: 22px; }
    .section h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #92720c; margin-bottom: 8px; }
    .section p { font-size: 13px; color: #374151; line-height: 1.7; }
    .notes-box { background: #fdf6e3; border-left: 4px solid #b8860b; padding: 14px 18px; border-radius: 0 6px 6px 0; }

    .footer { border-top: 1px solid #e5e7eb; padding-top: 18px; display: flex; justify-content: space-between; align-items: flex-end; margin-top: 40px; }
    .footer-left { font-size: 11px; color: #9ca3af; line-height: 1.7; }
    .footer-right { font-size: 11px; color: #9ca3af; text-align: right; }
    .thank-you { font-size: 14px; font-weight: 700; color: #b8860b; }

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
            <p>{{ $report->report_type ?: 'Report' }}</p>
        </div>
        <div class="report-label">
            <div class="period">{{ $report->periodEnum()->label() }} Report</div>
            <div class="range">{{ $report->period_start->format('M d') }} – {{ $report->period_end->format('M d, Y') }}</div>
            @if($report->title)
            <div class="title">{{ $report->title }}</div>
            @endif
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
            <div class="meta-label">Service</div>
            <div class="meta-value">{{ $report->report_type ?: '—' }}</div>
        </div>
    </div>

    @if(!empty($report->metrics))
    <table class="stats-table">
        @foreach(array_chunk($report->metrics, 3) as $chunk)
        <tr>
            @foreach($chunk as $m)
            <td>
                <div class="stat-box">
                    <div class="label">{{ $m['label'] }}</div>
                    <div class="value">{{ ($m['value'] ?? '') !== '' ? $m['value'] : '—' }}</div>
                </div>
            </td>
            @endforeach
            @for($i = count($chunk); $i < 3; $i++)<td></td>@endfor
        </tr>
        @endforeach
    </table>
    @endif

    @if($report->summary)
    <div class="section">
        <h4>Summary</h4>
        <p>{{ $report->summary }}</p>
    </div>
    @endif

    @if($report->next_plan)
    <div class="section">
        <h4>Next Steps / Plan</h4>
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
            <div style="color:#b8860b;font-weight:600;">{{ preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')) }}</div>
        </div>
    </div>

</div>
</body>
</html>
