@extends('layouts.app')
@section('title', 'New Report')
@section('breadcrumb', 'Reports / New')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Create Report</h2>
        <p>Generate a one-time, weekly, or monthly report for any service to share with a client.</p>
    </div>
    <a href="{{ route('reports.index') }}" class="btn btn-secondary">← Back</a>
</div>

@php
    $serviceTypes = ['Facebook Marketing', 'Google Ads', 'TikTok Ads', 'Web Development', 'Other'];
@endphp

<div class="card" style="max-width:680px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('reports.store') }}" id="reportForm">
            @csrf
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Client *</label>
                    <x-client-picker :clients="$clients" :selected="old('client_id', $selected_client?->id)" required />
                    @error('client_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">— No Project —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ old('project_id')==$project->id?'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Service / Report Type</label>
                    <select name="report_type" id="reportType" class="form-control">
                        <option value="">— Select service —</option>
                        @foreach($serviceTypes as $type)
                        <option value="{{ $type }}" {{ old('report_type')==$type?'selected':'' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Report Title <span class="text-faint text-xs">(optional)</span></label>
                    <input type="text" name="title" class="form-control" value="{{ old('title') }}" placeholder="e.g. August Ad Campaign Summary">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Report Period *</label>
                <select name="period_type" id="periodType" class="form-control" required>
                    @foreach(\App\Enums\ReportPeriod::cases() as $period)
                    <option value="{{ $period->value }}" {{ old('period_type','monthly')==$period->value?'selected':'' }}>{{ $period->label() }}</option>
                    @endforeach
                </select>
                @error('period_type')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Period Start *</label>
                    <input type="date" name="period_start" id="periodStart" class="form-control" value="{{ old('period_start', now()->startOfWeek()->format('Y-m-d')) }}" required>
                    @error('period_start')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Period End *</label>
                    <input type="date" name="period_end" id="periodEnd" class="form-control" value="{{ old('period_end', now()->endOfWeek()->format('Y-m-d')) }}" required>
                    @error('period_end')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="page-hd" style="margin:20px 0 8px;">
                <h3 style="font-size:1rem;">Metrics</h3>
                <p class="text-xs text-muted">Add any metrics relevant to this service. Pick a service type above for quick suggestions.</p>
            </div>
            <div id="presetChips" style="display:flex;flex-wrap:wrap;gap:6px;margin-bottom:12px;"></div>
            @php $oldLabels = old('metric_label', []); $oldValues = old('metric_value', []); @endphp
            <div id="metricRows">
                @foreach($oldLabels as $i => $lbl)
                <div class="metric-row" style="display:flex;gap:8px;margin-bottom:8px;">
                    <input type="text" name="metric_label[]" class="form-control" placeholder="Metric name" style="flex:1;" value="{{ $lbl }}">
                    <input type="text" name="metric_value[]" class="form-control" placeholder="Value" style="flex:1;" value="{{ $oldValues[$i] ?? '' }}">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="this.closest('.metric-row').remove()" style="flex-shrink:0;">✕</button>
                </div>
                @endforeach
            </div>
            <button type="button" class="btn btn-secondary btn-sm" onclick="addMetricRow()" style="margin-bottom:4px;">+ Add Metric</button>

            <div class="form-group" style="margin-top:20px;">
                <label class="form-label">Summary <span class="text-faint text-xs">(shown to client)</span></label>
                <textarea name="summary" class="form-control" rows="3" placeholder="A short overview of results and highlights…">{{ old('summary') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Next Steps / Plan</label>
                <textarea name="next_plan" class="form-control" rows="3" placeholder="What's planned for the next period…">{{ old('next_plan') }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Internal Notes <span class="text-faint text-xs">(not shown to client)</span></label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes') }}</textarea>
            </div>

            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Create Report</button>
                <a href="{{ route('reports.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
(function() {
    var periodType  = document.getElementById('periodType');
    var periodStart = document.getElementById('periodStart');
    var periodEnd   = document.getElementById('periodEnd');

    function suggestEnd() {
        if (!periodStart.value) return;
        var start = new Date(periodStart.value + 'T00:00:00');
        var end   = new Date(start);

        if (periodType.value === 'one-time') {
            // same day
        } else if (periodType.value === 'weekly') {
            end.setDate(start.getDate() + 6);
        } else if (periodType.value === 'monthly') {
            end = new Date(start.getFullYear(), start.getMonth() + 1, 0);
        }

        periodEnd.value = end.toISOString().slice(0, 10);
    }

    periodType.addEventListener('change', suggestEnd);
    periodStart.addEventListener('change', suggestEnd);
})();

// ── Flexible metrics builder ──
var METRIC_PRESETS = {
    'Facebook Marketing': ['Reach', 'Engagement', 'Video Views', 'Followers Gained', 'Link Clicks', 'Best Performing Post'],
    'Google Ads': ['Impressions', 'Clicks', 'CTR', 'Cost', 'Conversions', 'Cost / Conversion', 'Avg. CPC'],
    'TikTok Ads': ['Impressions', 'Video Views', 'Engagement', 'Followers Gained', 'CTR', 'Conversions'],
    'Web Development': ['Pages Delivered', 'Features Completed', 'Avg. Load Time', 'Uptime', 'Bugs Fixed'],
    'Other': []
};

function addMetricRow(label, value) {
    var row = document.createElement('div');
    row.className = 'metric-row';
    row.style.cssText = 'display:flex;gap:8px;margin-bottom:8px;';
    row.innerHTML =
        '<input type="text" name="metric_label[]" class="form-control" placeholder="Metric name" style="flex:1;">' +
        '<input type="text" name="metric_value[]" class="form-control" placeholder="Value" style="flex:1;">' +
        '<button type="button" class="btn btn-secondary btn-sm" style="flex-shrink:0;">✕</button>';
    row.querySelector('.btn').addEventListener('click', function() { row.remove(); });
    if (label) row.querySelector('input[name="metric_label[]"]').value = label;
    if (value) row.querySelector('input[name="metric_value[]"]').value = value;
    document.getElementById('metricRows').appendChild(row);
}

function renderPresetChips() {
    var type = document.getElementById('reportType').value;
    var wrap = document.getElementById('presetChips');
    wrap.innerHTML = '';
    var presets = METRIC_PRESETS[type] || [];
    presets.forEach(function(label) {
        var chip = document.createElement('button');
        chip.type = 'button';
        chip.textContent = '+ ' + label;
        chip.style.cssText = 'background:#1a1e28;border:1px solid #252936;color:#a0a6be;font-size:0.75rem;padding:4px 10px;border-radius:999px;cursor:pointer;';
        chip.addEventListener('click', function() { addMetricRow(label, ''); });
        wrap.appendChild(chip);
    });
}

document.getElementById('reportType').addEventListener('change', renderPresetChips);
renderPresetChips();
// Start with one empty row if none were repopulated from a validation error
if (document.querySelectorAll('#metricRows .metric-row').length === 0) {
    addMetricRow();
}

(function() {
    var clientHidden  = document.querySelector('.client-picker-value');
    var projectSelect = document.querySelector('select[name="project_id"]');
    if (!clientHidden || !projectSelect) return;

    function filterProjects() {
        var clientId = clientHidden.value;
        var currentValid = false;
        projectSelect.querySelectorAll('option[data-client-id]').forEach(function(opt) {
            var match = !clientId || opt.dataset.clientId === clientId;
            opt.hidden = !match;
            if (match && opt.value === projectSelect.value) currentValid = true;
        });
        if (!currentValid) projectSelect.value = '';
    }

    clientHidden.addEventListener('change', filterProjects);
    filterProjects();
})();
</script>
@endpush
@endsection
