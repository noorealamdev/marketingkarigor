@extends('layouts.app')
@section('title', 'Edit Task')
@section('breadcrumb', 'Tasks / Edit')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Edit Task</h2>
        <p>{{ $task->name }}</p>
    </div>
    <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:640px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('tasks.update', $task) }}">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Task Name *</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $task->name) }}" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <textarea name="description" class="form-control" rows="3">{{ old('description', $task->description) }}</textarea>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Status *</label>
                    <select name="status" class="form-control">
                        @foreach(\App\Enums\TaskStatus::cases() as $status)
                        <option value="{{ $status->value }}" {{ old('status', $task->status)==$status->value?'selected':'' }}>{{ $status->label() }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Priority *</label>
                    <select name="priority" class="form-control">
                        <option value="low" {{ old('priority',$task->priority)=='low'?'selected':'' }}>Low</option>
                        <option value="medium" {{ old('priority',$task->priority)=='medium'?'selected':'' }}>Medium</option>
                        <option value="high" {{ old('priority',$task->priority)=='high'?'selected':'' }}>High</option>
                        <option value="urgent" {{ old('priority',$task->priority)=='urgent'?'selected':'' }}>Urgent</option>
                    </select>
                </div>
            </div>
            <div class="g2">
                <div class="form-group">
                    <label class="form-label">Project</label>
                    <select name="project_id" class="form-control">
                        <option value="">— No Project —</option>
                        @foreach($projects as $project)
                        <option value="{{ $project->id }}" {{ old('project_id',$task->project_id)==$project->id?'selected':'' }}>{{ $project->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group">
                    <label class="form-label">Due Date</label>
                    <input type="date" name="due_date" class="form-control" value="{{ old('due_date', $task->due_date?->format('Y-m-d')) }}">
                </div>
            </div>
            @if(auth()->user()->isAdmin())
            <div class="form-group">
                <label class="form-label">Task Fee (৳) <span class="text-faint text-xs">(optional — what you pay the assignee once it is done; only you can see this)</span></label>
                <input type="number" name="payment_amount" class="form-control" step="0.01" min="0" placeholder="0.00" value="{{ old('payment_amount', $task->payment_amount) }}">
                @error('payment_amount')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            @endif
            @if(auth()->user()->hasAnyRole(['super-admin', 'project-manager']))
            <div class="form-group">
                <label class="form-label">Assign To <span class="text-faint text-xs">(select any number of team members)</span></label>
                @error('assignees')<div class="form-error">{{ $message }}</div>@enderror
                @php $selectedAssignees = old('assignees', $task->assignees->pluck('id')->all()); @endphp
                <div class="row between" style="margin-top:6px;margin-bottom:6px;gap:8px;">
                    <input type="text" class="form-control" placeholder="Search team members…" oninput="filterAssigneeRows(this)" style="flex:1;">
                    <span class="text-faint text-xs" id="assigneeCount" style="white-space:nowrap;"></span>
                </div>
                <div id="assigneeList" style="display:flex;flex-direction:column;gap:6px;max-height:240px;overflow-y:auto;">
                    @foreach($members as $member)
                    <label class="assignee-row" data-name="{{ strtolower($member->name) }}" style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:8px;cursor:pointer;border:1px solid #252936;background:#1a1e28;">
                        <input type="checkbox" name="assignees[]" value="{{ $member->id }}" class="assignee-checkbox" {{ in_array($member->id, $selectedAssignees)?'checked':'' }} onchange="updateAssigneeCount()" style="accent-color:#6c63ff;">
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
                @if($task->assignees->isNotEmpty())
                <div style="display:flex;flex-direction:column;gap:6px;">
                    @foreach($task->assignees as $assignee)
                    <div class="form-control" style="color:#6b7590;cursor:default;display:flex;align-items:center;gap:8px;">
                        <span style="width:22px;height:22px;border-radius:50%;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:inline-flex;align-items:center;justify-content:center;font-size:0.7rem;font-weight:700;color:#fff;flex-shrink:0;">{{ strtoupper(substr($assignee->name,0,1)) }}</span>
                        {{ $assignee->name }}
                        @if($assignee->id === auth()->id()) <span style="font-size:0.72rem;color:#4a5068;">(you)</span> @endif
                        <span style="font-size:0.72rem;color:#4a5068;margin-left:auto;">Only managers can reassign</span>
                    </div>
                    @endforeach
                </div>
                @else
                <div class="form-control" style="color:#4a5068;cursor:default;">
                    Unassigned — <span style="font-size:0.78rem;">only managers can assign tasks</span>
                </div>
                @endif
            </div>
            @endif
            <div class="row" style="gap:8px;margin-top:4px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('tasks.show', $task) }}" class="btn btn-secondary">Cancel</a>
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
