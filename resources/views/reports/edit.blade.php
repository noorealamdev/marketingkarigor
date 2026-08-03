@extends('layouts.app')
@section('title', 'Edit Report')
@section('breadcrumb', 'Reports / Edit')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Edit Report</h2>
        <p>{{ $report->client->company ?: $report->client->name }} — {{ $report->periodEnum()->label() }}</p>
    </div>
    <a href="{{ route('reports.show', $report) }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:680px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('reports.update', $report) }}">
            @csrf @method('PATCH')
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Client *</label>
                    <x-client-picker :clients="$clients" :selected="old('client_id', $report->client_id)" required />
                    @error('client_id')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">— No Project —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" data-client-id="{{ $project->client_id }}" {{ old('project_id', $report->project_id)==$project->id?'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Report Period *</label>
                <select name="period_type" class="form-control" required>
                    @foreach(\App\Enums\ReportPeriod::cases() as $period)
                    <option value="{{ $period->value }}" {{ old('period_type', $report->period_type)==$period->value?'selected':'' }}>{{ $period->label() }}</option>
                    @endforeach
                </select>
                @error('period_type')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Period Start *</label>
                    <input type="date" name="period_start" class="form-control" value="{{ old('period_start', $report->period_start->format('Y-m-d')) }}" required>
                    @error('period_start')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Period End *</label>
                    <input type="date" name="period_end" class="form-control" value="{{ old('period_end', $report->period_end->format('Y-m-d')) }}" required>
                    @error('period_end')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>

            <div class="page-hd" style="margin:20px 0 12px;">
                <h3 style="font-size:1rem;">Performance Metrics</h3>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Reach</label>
                    <input type="number" name="reach" class="form-control" min="0" value="{{ old('reach', $report->reach) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Engagement</label>
                    <input type="number" name="engagement" class="form-control" min="0" value="{{ old('engagement', $report->engagement) }}">
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Video Views</label>
                <input type="number" name="video_views" class="form-control" min="0" value="{{ old('video_views', $report->video_views) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Best Performing Post</label>
                <input type="text" name="best_performing_post" class="form-control" value="{{ old('best_performing_post', $report->best_performing_post) }}">
            </div>
            <div class="form-group">
                <label class="form-label">Next Period's Plan</label>
                <textarea name="next_plan" class="form-control" rows="3">{{ old('next_plan', $report->next_plan) }}</textarea>
            </div>
            <div class="form-group">
                <label class="form-label">Internal Notes <span class="text-faint text-xs">(not shown to client)</span></label>
                <textarea name="notes" class="form-control" rows="2">{{ old('notes', $report->notes) }}</textarea>
            </div>

            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('reports.show', $report) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
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
