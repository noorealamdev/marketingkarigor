@extends('layouts.app')
@section('title', 'Edit Project')
@section('breadcrumb', 'Projects / Edit')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Edit Project</h2>
        <p>{{ $project->name }}</p>
    </div>
    <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('projects.update', $project) }}">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Project Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $project->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $project->description) }}</textarea>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status',$project->status)=='active'?'selected':'' }}>Active</option>
                        <option value="on_hold" {{ old('status',$project->status)=='on_hold'?'selected':'' }}>On Hold</option>
                        <option value="completed" {{ old('status',$project->status)=='completed'?'selected':'' }}>Completed</option>
                        <option value="cancelled" {{ old('status',$project->status)=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Priority *</label>
                    <select name="priority" class="form-control">
                        <option value="low" {{ old('priority',$project->priority)=='low'?'selected':'' }}>Low</option>
                        <option value="medium" {{ old('priority',$project->priority)=='medium'?'selected':'' }}>Medium</option>
                        <option value="high" {{ old('priority',$project->priority)=='high'?'selected':'' }}>High</option>
                        <option value="urgent" {{ old('priority',$project->priority)=='urgent'?'selected':'' }}>Urgent</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Client</label>
                <x-client-picker :clients="$clients" :selected="old('client_id', $project->client_id)" empty-option="— No Client —" />
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date', $project->start_date?->format('Y-m-d')) }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline', $project->deadline?->format('Y-m-d')) }}">
                </div>
            </div>
            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
