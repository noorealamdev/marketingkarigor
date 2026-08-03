@extends('layouts.app')
@section('title', 'New Role')
@section('breadcrumb', 'Team / Roles / New')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>New Role</h2>
        <p>Define a role and choose which permissions it grants.</p>
    </div>
    <a href="{{ route('roles.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div class="card" style="max-width:560px;">
    <div class="card-bd">
        <form method="POST" action="{{ route('roles.store') }}">
            @csrf
            <div class="form-group">
                <label class="form-label">Role Name * <span class="text-faint text-xs">(lowercase, hyphenated, e.g. content-writer)</span></label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="e.g. content-writer" required>
                @error('name')<div class="form-error">{{ $message }}</div>@enderror
            </div>
            <div class="form-group">
                <label class="form-label">Description</label>
                <input type="text" name="description" class="form-control" value="{{ old('description') }}" placeholder="What this role is for">
            </div>
            <div class="form-group">
                <label class="form-label">Permissions</label>
                <div style="margin-top:6px;display:flex;flex-direction:column;gap:8px;">
                    @foreach($permissions as $permission)
                    <label style="display:flex;align-items:center;gap:10px;padding:9px 12px;border-radius:7px;cursor:pointer;border:1px solid #252936;background:#1a1e28;">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" {{ in_array($permission->name, old('permissions', [])) ? 'checked' : '' }} style="accent-color:#6c63ff;">
                        <span class="text-sm">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>
            </div>
            <div class="row" style="gap:8px;margin-top:6px;">
                <button type="submit" class="btn btn-primary">Create Role</button>
                <a href="{{ route('roles.index') }}" class="btn btn-secondary">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
