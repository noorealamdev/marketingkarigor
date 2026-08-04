<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Join {{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        * { box-sizing:border-box; margin:0; padding:0; }
        body { background:#0d0f14; color:#e8eaf0; font-family:system-ui,sans-serif; display:flex; align-items:center; justify-content:center; min-height:100vh; padding:24px; }
        .box { background:#13161d; border:1px solid #252936; border-radius:14px; padding:32px; width:100%; max-width:440px; }
        .logo { font-size:1.4rem; font-weight:800; letter-spacing:-0.03em; margin-bottom:6px; }
        .logo span { color:#6c63ff; }
        .logo img { max-width:220px; object-fit:contain; }
        .subtitle { font-size:0.845rem; color:#6b7590; margin-bottom:28px; }
        .invite-info { background:#1a1e28; border:1px solid #252936; border-radius:8px; padding:14px 16px; margin-bottom:24px; }
        .invite-info .invite-email { font-weight:700; color:#c8cce0; }
        .invite-info .invite-roles { display:flex; flex-wrap:wrap; gap:4px; margin-top:8px; }
        .badge { display:inline-flex; align-items:center; padding:2px 10px; border-radius:20px; font-size:0.7rem; font-weight:700; background:rgba(108,99,255,0.12); color:#a89fff; }
        .form-group { margin-bottom:16px; }
        .form-label { display:block; font-size:0.78rem; font-weight:600; color:#8b92a8; margin-bottom:5px; }
        .form-control { width:100%; padding:9px 12px; background:#1a1e28; border:1px solid #252936; border-radius:7px; color:#e8eaf0; font-size:0.875rem; outline:none; transition:border-color 0.12s; }
        .form-control:focus { border-color:#6c63ff; box-shadow:0 0 0 3px rgba(108,99,255,0.12); }
        .form-error { font-size:0.76rem; color:#f87171; margin-top:3px; }
        .btn { display:flex; align-items:center; justify-content:center; padding:10px 16px; border-radius:7px; font-size:0.875rem; font-weight:600; cursor:pointer; border:none; width:100%; background:#6c63ff; color:#fff; transition:background 0.12s; }
        .btn:hover { background:#7b73ff; }
        .pwd-req { font-size:0.71rem; color:#4a5068; transition:color 0.18s; line-height:1.7; }
        .pwd-req.met { color:#4ade80; }
        .password-wrap { position:relative; }
        .password-wrap input { padding-right:42px; }
        .password-toggle {
            position:absolute; top:50%; right:6px; transform:translateY(-50%);
            background:none; border:none; padding:6px; cursor:pointer; color:#6b7590;
            display:flex; align-items:center; justify-content:center; border-radius:6px;
        }
        .password-toggle:hover { color:#c8cce0; background:#252936; }
    </style>
</head>
<body>
<div class="box">
    <div class="logo">
        @if($logoUrl = app_logo_url())
            <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
        @else
            {{ config('app.name') }}
        @endif
    </div>
    <div class="subtitle">You've been invited to join the team!</div>

    <div class="invite-info">
        <div style="font-size:0.72rem;color:#4a5068;text-transform:uppercase;letter-spacing:0.06em;margin-bottom:4px;">Joining as</div>
        <div class="invite-email">{{ $invitation->email }}</div>
        <div class="invite-roles">
            @foreach($roles as $role)
                <span class="badge">{{ $role->name }}</span>
            @endforeach
        </div>
    </div>

    @if($errors->any())
    <div style="background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.2);border-radius:7px;padding:10px 14px;margin-bottom:16px;font-size:0.845rem;color:#f87171;">
        @foreach($errors->all() as $e)<div>{{ $e }}</div>@endforeach
    </div>
    @endif

    <form method="POST" action="{{ route('invitations.register', $invitation->token) }}">
        @csrf
        <div class="form-group">
            <label class="form-label">Your Full Name *</label>
            <input type="text" name="name" class="form-control" value="{{ old('name', $invitation->name) }}" placeholder="Jane Smith" required>
        </div>
        <div class="form-group">
            <label class="form-label">Password *</label>
            <div class="password-wrap">
                <input type="password" name="password" id="inv-password" class="form-control" placeholder="Create a strong password" required oninput="checkStrength(this.value)">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility(this)" tabindex="-1" aria-label="Show password">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
        </div>

        {{-- Live strength meter --}}
        <div style="margin:-8px 0 16px;">
            <div style="height:4px;background:#0d0f14;border-radius:2px;overflow:hidden;margin-bottom:9px;">
                <div id="strengthBar" style="height:100%;width:0;border-radius:2px;transition:all 0.25s;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:3px 16px;">
                <div id="req-len" class="pwd-req">✗ At least 8 characters</div>
                <div id="req-up"  class="pwd-req">✗ Uppercase letter (A–Z)</div>
                <div id="req-low" class="pwd-req">✗ Lowercase letter (a–z)</div>
                <div id="req-num" class="pwd-req">✗ Number (0–9)</div>
                <div id="req-sym" class="pwd-req">✗ Special character (!@#…)</div>
            </div>
        </div>

        <div class="form-group">
            <label class="form-label">Confirm Password *</label>
            <div class="password-wrap">
                <input type="password" name="password_confirmation" class="form-control" placeholder="Repeat password" required>
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility(this)" tabindex="-1" aria-label="Show password">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
        </div>
        <button type="submit" class="btn">Join {{ config('app.name') }}</button>
    </form>

    @if($invitation->expires_at)
    <div style="text-align:center;font-size:0.76rem;color:#4a5068;margin-top:14px;">
        This invitation expires {{ $invitation->expires_at->diffForHumans() }}
    </div>
    @endif
</div>

<script>
function checkStrength(val) {
    const rules = {
        'req-len': val.length >= 8,
        'req-up':  /[A-Z]/.test(val),
        'req-low': /[a-z]/.test(val),
        'req-num': /[0-9]/.test(val),
        'req-sym': /[^A-Za-z0-9]/.test(val),
    };
    let met = 0;
    for (const [id, pass] of Object.entries(rules)) {
        const el = document.getElementById(id);
        const label = el.textContent.slice(2);
        if (pass) { el.classList.add('met');    el.textContent = '✓ ' + label; met++; }
        else       { el.classList.remove('met'); el.textContent = '✗ ' + label; }
    }
    const bar = document.getElementById('strengthBar');
    const colors = ['', '#f87171', '#fb923c', '#fbbf24', '#a3e635', '#4ade80'];
    bar.style.width      = (met / 5 * 100) + '%';
    bar.style.background = colors[met] || '';
}

function togglePasswordVisibility(btn) {
    const input = btn.previousElementSibling;
    const showing = input.type === 'text';
    input.type = showing ? 'password' : 'text';
    btn.querySelector('.eye-open').style.display = showing ? 'block' : 'none';
    btn.querySelector('.eye-closed').style.display = showing ? 'none' : 'block';
    btn.setAttribute('aria-label', showing ? 'Show password' : 'Hide password');
}
</script>
</body>
</html>
