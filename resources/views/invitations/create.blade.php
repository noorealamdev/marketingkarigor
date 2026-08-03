@extends('layouts.app')
@section('title', 'Invite Member')
@section('breadcrumb', 'Team / Invite Member')

@section('content')
<div class="page-hd row between">
    <div>
        <h2>Invite Team Member</h2>
        <p>Send an invitation link to a new team member and assign their roles.</p>
    </div>
    <a href="{{ route('invitations.index') }}" class="btn btn-secondary">← Back</a>
</div>

<div style="max-width:600px;">
    <div class="card">
        <div class="card-bd">
            <form method="POST" action="{{ route('invitations.store') }}">
                @csrf
                <div class="form-group">
                    <label class="form-label">Email Address *</label>
                    <input type="email" name="email" class="form-control" value="{{ old('email') }}" placeholder="colleague@example.com" required>
                    @error('email')<div class="form-error">{{ $message }}</div>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">Their Name (optional)</label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" placeholder="Jane Smith">
                </div>
                <div class="form-group">
                    <label class="form-label">Assign Roles * <span class="text-faint text-xs">(can select multiple)</span></label>
                    @error('role_ids')<div class="form-error">{{ $message }}</div>@enderror
                    <div style="margin-top:6px;display:flex;flex-direction:column;gap:8px;">
                        @foreach($roles as $role)
                        <label style="display:flex;align-items:flex-start;gap:12px;padding:12px 14px;border-radius:8px;cursor:pointer;border:1px solid #252936;background:#1a1e28;transition:all 0.12s;" class="role-label">
                            <input type="checkbox" name="role_ids[]" value="{{ $role->name }}" {{ in_array($role->name, old('role_ids',[]))?'checked':'' }} style="margin-top:2px;accent-color:#6c63ff;">
                            <div>
                                <div class="fw600" style="font-size:0.875rem;">{{ $role->name }}</div>
                                @if($role->description)
                                <div class="text-xs text-muted" style="margin-top:2px;">{{ $role->description }}</div>
                                @endif
                            </div>
                        </label>
                        @endforeach
                    </div>
                </div>
                <div class="form-group">
                    <label class="form-label">Client <span class="text-faint text-xs">(required if inviting the Client role)</span></label>
                    @error('client_id')<div class="form-error">{{ $message }}</div>@enderror
                    <x-client-picker :clients="$clients" :selected="old('client_id')" empty-option="— None —" />
                </div>
                <div class="row" style="gap:8px;margin-top:6px;">
                    <button type="submit" class="btn btn-primary">Generate Invitation Link</button>
                    <a href="{{ route('invitations.index') }}" class="btn btn-secondary">Cancel</a>
                </div>
            </form>
        </div>
    </div>

    <div class="card" style="margin-top:14px;padding:16px;">
        <div class="fw600" style="margin-bottom:8px;font-size:0.875rem;">How it works</div>
        <div style="font-size:0.8rem;color:#6b7590;line-height:1.7;">
            <div>1. Fill in the email and select roles for the new member.</div>
            <div>2. An invitation link is generated — share it with them directly.</div>
            <div>3. They click the link, set their name and password, and join the team.</div>
            <div>4. Their roles are automatically assigned upon registration.</div>
            <div style="color:#4a5068;margin-top:6px;">Links expire after 7 days.</div>
        </div>
    </div>
</div>
@endsection
