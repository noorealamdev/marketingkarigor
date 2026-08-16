@extends('layouts.app')

@push('styles')
<style>
    .strength-bar { height: 4px; flex: 1; border-radius: 2px; background: #252936; transition: background 0.15s; }
</style>
@endpush

@section('content')
<div class="page-hd">
    <h2>Account Settings</h2>
</div>

<div style="display:grid;grid-template-columns:1fr 1fr;gap:16px;max-width:860px;">

    {{-- ── AVATAR ── --}}
    <div class="card" style="grid-column:1/-1;">
        <div class="card-hd"><h3>Profile Photo</h3></div>
        <div class="card-bd">
            <div style="display:flex;align-items:center;gap:20px;flex-wrap:wrap;">
                {{-- Current avatar display --}}
                <div id="avatarCircle" style="width:80px;height:80px;border-radius:50%;overflow:hidden;flex-shrink:0;background:linear-gradient(135deg,#6c63ff,#a78bfa);display:flex;align-items:center;justify-content:center;font-size:2rem;font-weight:700;color:#fff;">
                    @php $avatarMedia = $user->getFirstMedia('avatar'); @endphp
                    @if($avatarMedia)
                        {!! user_avatar($user, 80) !!}
                    @else
                        <span id="avatarInitial">{{ strtoupper(substr($user->name,0,1)) }}</span>
                    @endif
                </div>

                <div>
                    <div style="display:flex;align-items:center;gap:8px;flex-wrap:wrap;margin-bottom:8px;">
                        {{-- Upload --}}
                        <form method="POST" action="{{ route('profile.avatar.update') }}"
                              enctype="multipart/form-data" id="avatarUploadForm">
                            @csrf
                            <label class="btn btn-secondary" style="cursor:pointer;margin:0;">
                                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="vertical-align:-1px;margin-right:5px;"><path d="M21 15v4a2 2 0 01-2 2H5a2 2 0 01-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                                Upload Photo
                                <input type="file" name="avatar" accept="image/*" style="display:none;"
                                       onchange="previewAndSubmitAvatar(this)">
                            </label>
                        </form>

                        {{-- Remove --}}
                        @if($avatarMedia)
                        <form method="POST" action="{{ route('profile.avatar.destroy') }}"
                              onsubmit="return confirm('Remove your profile photo?')">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-danger btn-sm">Remove</button>
                        </form>
                        @endif
                    </div>
                    <p style="font-size:0.72rem;color:#4a5068;">JPG, PNG, WebP or GIF · max 2 MB · square recommended</p>
                    @if($errors->avatar->has('avatar'))
                        <p style="font-size:0.75rem;color:#f87171;margin-top:4px;">{{ $errors->avatar->first('avatar') }}</p>
                    @endif
                    @if(session('status') === 'avatar-updated')
                        <p style="font-size:0.75rem;color:#4ade80;margin-top:6px;">✓ Profile photo updated.</p>
                    @elseif(session('status') === 'avatar-removed')
                        <p style="font-size:0.75rem;color:#4ade80;margin-top:6px;">✓ Profile photo removed.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>

    {{-- ── PROFILE INFO ── --}}
    <div class="card">
        <div class="card-hd"><h3>Profile Information</h3></div>
        <div class="card-bd">
            <form method="POST" action="{{ route('profile.update') }}">
                @csrf @method('PATCH')
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Full Name</label>
                        <input type="text" name="name" class="form-control"
                               value="{{ old('name', $user->name) }}" required autofocus>
                        @error('name')<p style="font-size:0.75rem;color:#f87171;margin-top:3px;">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="form-label">Email Address</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')<p style="font-size:0.75rem;color:#f87171;margin-top:3px;">{{ $message }}</p>@enderror
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="submit" class="btn btn-primary btn-sm">Save Changes</button>
                        @if(session('status') === 'profile-updated')
                            <span style="font-size:0.75rem;color:#4ade80;">✓ Saved.</span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── CHANGE PASSWORD ── --}}
    <div class="card">
        <div class="card-hd"><h3>Change Password</h3></div>
        <div class="card-bd">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf @method('PUT')
                <div style="display:flex;flex-direction:column;gap:14px;">
                    <div>
                        <label class="form-label">Current Password</label>
                        <input type="password" name="current_password" class="form-control"
                               autocomplete="current-password">
                        @error('current_password', 'updatePassword')
                            <p style="font-size:0.75rem;color:#f87171;margin-top:3px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">New Password</label>
                        <input type="password" name="password" id="newPassword" class="form-control"
                               autocomplete="new-password" oninput="updatePasswordStrength(this.value)">
                        <p style="font-size:0.7rem;color:#4a5068;margin-top:4px;">
                            @if($user->hasRole('client'))
                                Min 6 characters.
                            @else
                                Min 8 characters, with uppercase, lowercase, a number, and a symbol.
                            @endif
                        </p>
                        <div style="display:flex;gap:4px;margin-top:6px;" id="strengthBars">
                            <div class="strength-bar" data-bar="1"></div>
                            <div class="strength-bar" data-bar="2"></div>
                            <div class="strength-bar" data-bar="3"></div>
                            <div class="strength-bar" data-bar="4"></div>
                        </div>
                        <p style="font-size:0.72rem;margin-top:4px;" id="strengthLabel">&nbsp;</p>
                        @error('password', 'updatePassword')
                            <p style="font-size:0.75rem;color:#f87171;margin-top:3px;">{{ $message }}</p>
                        @enderror
                    </div>
                    <div>
                        <label class="form-label">Confirm New Password</label>
                        <input type="password" name="password_confirmation" class="form-control"
                               autocomplete="new-password">
                    </div>
                    <div style="display:flex;align-items:center;gap:10px;">
                        <button type="submit" class="btn btn-primary btn-sm">Update Password</button>
                        @if(session('status') === 'password-updated')
                            <span style="font-size:0.75rem;color:#4ade80;">✓ Password updated.</span>
                        @endif
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- ── NOTIFICATION PREFERENCES ── --}}
    @unless(auth()->user()->hasRole('client'))
    <div class="card" style="grid-column:1/-1;">
        <div class="card-hd"><h3>Notification Preferences</h3></div>
        <div class="card-bd">
            <form method="POST" action="{{ route('profile.notifications.update') }}">
                @csrf
                <div style="overflow-x:auto;">
                    <table style="width:100%;border-collapse:collapse;min-width:520px;">
                        <thead>
                            <tr style="text-align:left;border-bottom:1px solid #252936;">
                                <th style="padding:8px 10px 10px 0;font-size:0.72rem;color:#6b7590;text-transform:uppercase;letter-spacing:0.04em;">Notification</th>
                                <th style="padding:8px 10px 10px;font-size:0.72rem;color:#6b7590;text-transform:uppercase;letter-spacing:0.04em;width:90px;text-align:center;">In-app</th>
                                <th style="padding:8px 10px 10px;font-size:0.72rem;color:#6b7590;text-transform:uppercase;letter-spacing:0.04em;width:90px;text-align:center;">Email</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach(\App\Enums\NotificationType::staffTypes() as $type)
                            <tr style="border-bottom:1px solid #1c1f29;">
                                <td style="padding:12px 10px 12px 0;">
                                    <div style="font-size:0.85rem;font-weight:600;color:#c8cce0;">{{ $type->label() }}</div>
                                    <div style="font-size:0.75rem;color:#4a5068;margin-top:2px;">{{ $type->description() }}</div>
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" name="preferences[{{ $type->value }}][database]" value="1"
                                           style="accent-color:#6c63ff;width:16px;height:16px;"
                                           {{ auth()->user()->wantsNotification($type, 'database') ? 'checked' : '' }}>
                                </td>
                                <td style="text-align:center;">
                                    <input type="checkbox" name="preferences[{{ $type->value }}][mail]" value="1"
                                           style="accent-color:#6c63ff;width:16px;height:16px;"
                                           {{ auth()->user()->wantsNotification($type, 'mail') ? 'checked' : '' }}>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                <div style="display:flex;align-items:center;gap:10px;margin-top:16px;">
                    <button type="submit" class="btn btn-primary btn-sm">Save Preferences</button>
                    @if(session('status') === 'notifications-updated')
                        <span style="font-size:0.75rem;color:#4ade80;">✓ Saved.</span>
                    @endif
                </div>
            </form>
        </div>
    </div>
    @endunless

</div>
@endsection

@push('scripts')
<script>
function previewAndSubmitAvatar(input) {
    if (!input.files || !input.files[0]) return;
    const reader = new FileReader();
    reader.onload = function(e) {
        const circle = document.getElementById('avatarCircle');
        circle.innerHTML = '<img src="' + e.target.result + '" style="width:100%;height:100%;object-fit:cover;">';
    };
    reader.readAsDataURL(input.files[0]);
    // Auto-submit after brief delay so preview shows
    setTimeout(() => document.getElementById('avatarUploadForm').requestSubmit(), 150);
}

const CLIENT_MODE = @json($user->hasRole('client'));

function updatePasswordStrength(value) {
    const bars = document.querySelectorAll('#strengthBars .strength-bar');
    const label = document.getElementById('strengthLabel');

    let score = 0;
    if (CLIENT_MODE) {
        score = value.length >= 6 ? 4 : (value.length ? 1 : 0);
    } else {
        if (value.length >= 8) score++;
        if (/[a-z]/.test(value) && /[A-Z]/.test(value)) score++;
        if (/[0-9]/.test(value)) score++;
        if (/[^A-Za-z0-9]/.test(value)) score++;
    }

    const colors = ['#f87171', '#f87171', '#facc15', '#4ade80'];
    const labels = ['Too weak', 'Weak', 'Fair', 'Strong'];

    bars.forEach((bar, i) => {
        bar.style.background = (value.length && i < Math.max(score, 1)) ? colors[Math.max(score, 1) - 1] : '#252936';
    });

    if (!value.length) {
        label.textContent = ' ';
        label.style.color = '#4a5068';
    } else {
        label.textContent = labels[Math.max(score, 1) - 1];
        label.style.color = colors[Math.max(score, 1) - 1];
    }
}
</script>
@endpush
