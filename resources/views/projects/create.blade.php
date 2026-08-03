@extends('layouts.app')
@section('title', 'New Project')
@section('breadcrumb', 'Projects / New')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Create New Project</h2>
        <p>Set up a new project and link it to a client.</p>
    </div>
    <a href="{{ route('projects.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:720px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('projects.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Project Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Website Redesign" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Brief overview of this project…">{{ old('description') }}</textarea>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        <option value="active" {{ old('status','active')=='active'?'selected':'' }}>Active</option>
                        <option value="on_hold" {{ old('status')=='on_hold'?'selected':'' }}>On Hold</option>
                        <option value="completed" {{ old('status')=='completed'?'selected':'' }}>Completed</option>
                        <option value="cancelled" {{ old('status')=='cancelled'?'selected':'' }}>Cancelled</option>
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Priority *</label>
                    <select name="priority" class="form-control">
                        <option value="low" {{ old('priority')=='low'?'selected':'' }}>Low</option>
                        <option value="medium" {{ old('priority','medium')=='medium'?'selected':'' }}>Medium</option>
                        <option value="high" {{ old('priority')=='high'?'selected':'' }}>High</option>
                        <option value="urgent" {{ old('priority')=='urgent'?'selected':'' }}>Urgent</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label class="form-label">Client</label>
                <x-client-picker :clients="$clients" :selected="old('client_id', request('client_id'))" empty-option="— No Client —" />
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Start Date</label>
                    <input type="date" name="start_date" class="form-control" value="{{ old('start_date') }}">
                </div>
                <div class="form-group">
                    <label class="form-label">Deadline</label>
                    <input type="date" name="deadline" class="form-control" value="{{ old('deadline') }}">
                    @error('deadline')<div class="form-error">{{ $message }}</div>@enderror
                </div>
            </div>
            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Create Project</button>
                <a href="{{ route('projects.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
