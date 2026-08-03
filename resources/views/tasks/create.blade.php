@extends('layouts.app')
@section('title', 'New Task')
@section('breadcrumb', 'Tasks / New')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Add New Task</h2>
        <p>Create a task and optionally link it to a project.</p>
    </div>
    <a href="{{ route('tasks.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('tasks.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Task Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. Design mockup for homepage" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3" placeholder="Details about this task…">{{ old('description') }}</textarea>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        @foreach(\App\Enums\TaskStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ old('status', 'todo')==$status->value?'selected':'' }}>{{ $status->label() }}</option>
                        @endforeach
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
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">— No Project —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id', $selected_project?->id)==$project->id?'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date') }}">
                </div>
            </div>
            @if(auth()->user()->hasAnyRole(['super-admin', 'project-manager']))
            <div class="form-group">
                <label class="form-label">Assign To <span class="text-faint text-xs">(select any number of team members)</span></label>
                @error('assignees')<div class="form-error">{{ $message }}</div>@enderror
                <div class="row between" style="margin-top:6px;margin-bottom:6px;gap:8px;">
                    <input type="text" class="form-control" placeholder="Search team members…" oninput="filterAssigneeRows(this)" style="flex:1;">
                    <span class="text-faint text-xs" id="assigneeCount" style="white-space:nowrap;"></span>
                </div>
                <div id="assigneeList" style="display:flex;flex-direction:column;gap:6px;max-height:240px;overflow-y:auto;">
                    @foreach($members as $member)
                    <label class="assignee-row" data-name="{{ strtolower($member->name) }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;cursor:pointer;border:1px solid #252936;background:#1a1e28;">
                        <input type="checkbox" name="assignees[]" value="{{ $member->id }}" class="assignee-checkbox" {{ in_array($member->id, old('assignees',[]))?'checked':'' }} onchange="updateAssigneeCount()" style="accent-color:#6c63ff;">
                        <span style="font-size:0.845rem;color:#c8cce0;">{{ $member->name }}</span>
                        @if($member->roles->isNotEmpty())
                        <span class="text-faint text-xs">{{ $member->roles->pluck('name')->join(', ') }}</span>
                        @endif
                    </label>
                    @endforeach
                    <p class="text-faint text-xs" id="assigneeNoResults" style="display:none;padding:6px 12px;">No members match your search.</p>
                </div>
            </div>
            @else
            <div class="form-group">
                <label class="form-label">Assign To</label>
                <div class="form-control" style="color:#6b7590;cursor:default;display:flex;align-items:center;gap:8px;">
                    <span style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</span>
                    {{ auth()->user()->name }} <span style="font-size:0.72rem;color:#4a5068;">(you)</span>
                </div>
            </div>
            @endif
            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Create Task</button>
                <a href="{{ route('tasks.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>

@push('scripts')
<script>
function filterAssigneeRows(input) {
    const query = input.value.trim().toLowerCase();
    const rows = document.querySelectorAll('#assigneeList .assignee-row');
    let visible = 0;
    rows.forEach(row => {
        const match = row.dataset.name.includes(query);
        row.style.display = match ? 'flex' : 'none';
        if (match) visible++;
    });
    const noResults = document.getElementById('assigneeNoResults');
    if (noResults) noResults.style.display = visible === 0 ? 'block' : 'none';
}
function updateAssigneeCount() {
    const count = document.querySelectorAll('#assigneeList .assignee-checkbox:checked').length;
    const el = document.getElementById('assigneeCount');
    if (el) el.textContent = count > 0 ? count + ' selected' : '';
}
document.addEventListener('DOMContentLoaded', updateAssigneeCount);
</script>
@endpush
@endsection
