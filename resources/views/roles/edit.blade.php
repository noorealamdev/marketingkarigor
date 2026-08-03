@extends('layouts.app')
@section('title', 'Edit Role')
@section('breadcrumb', 'Team / Roles / Edit')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>{{ $role->name }}</h2>
        <p>Update the description and permissions for this role.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('roles.update', $role) }}">
            @csrf @method('PATCH')
            <div class="form-group">
                <label class="form-label">Role Name</label>
                <input type="text" class="form-control" value="{{ $role->name }}" disabled>
                <div class="text-xs text-faint" style="margin-top:4px;">Role names can't be changed after creation.</div>
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description', $role->description) }}" placeholder="What this role is for">
            </div>
            <div class="form-group">
                <label class="form-label">Permissions</label>
                <div style="margin-top:6px;display:flex;flex-direction:column;gap:8px;">
                    @php $current = $role->permissions->pluck('name')->all(); @endphp
                    @foreach($permissions as $permission)
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:7px;cursor:pointer;border:1px solid #252936;background:#1a1e28;">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ in_array($permission->name, old('permissions', $current)) ? 'checked' : '' }} style="accent-color:#6c63ff;">
                        <span class="text-sm">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="row" style="gap:8px;margin-top:6px;">
                <button type="submit" class="btn btn-primary">Save Changes</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
