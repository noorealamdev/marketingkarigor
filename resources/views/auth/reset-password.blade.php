<x-guest-layout>
    <p class="auth-desc" style="margin-bottom:22px;">Choose a new secure password for your account.</p>

    <form method="POST" action="{{ route('password.store') }}">
        @csrf
        <input type="hidden" name="token" value="{{ $request->route('token') }}">

        <div class="field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email', $request->email) }}" required autofocus autocomplete="username">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password">New Password</label>
            <div class="password-wrap">
                <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="{{ $isClient ? 'Min. 6 characters' : 'Min. 8 characters' }}" oninput="checkStrength(this.value)">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility(this)" tabindex="-1" aria-label="Show password">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div style="margin:-10px 0 16px;">
            <div style="height:4px;background:#1a1e28;border-radius:2px;overflow:hidden;margin-bottom:9px;">
                <div id="strengthBar" style="height:100%;width:0;border-radius:2px;transition:all 0.25s;"></div>
            </div>
            <div style="display:grid;grid-template-columns:1fr 1fr;gap:3px 16px;">
                @if($isClient)
                <div id="req-len" class="pwd-req">✗ At least 6 characters</div>
                @else
                <div id="req-len" class="pwd-req">✗ At least 8 characters</div>
                <div id="req-up"  class="pwd-req">✗ Uppercase letter (A–Z)</div>
                <div id="req-low" class="pwd-req">✗ Lowercase letter (a–z)</div>
                <div id="req-num" class="pwd-req">✗ Number (0–9)</div>
                <div id="req-sym" class="pwd-req">✗ Special character (!@#…)</div>
                @endif
            </div>
        </div>

        <div class="field">
            <label for="password_confirmation">Confirm New Password</label>
            <div class="password-wrap">
                <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
                <button type="button" class="password-toggle" onclick="togglePasswordVisibility(this)" tabindex="-1" aria-label="Show password">
                    <svg class="eye-open" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                    <svg class="eye-closed" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="display:none;"><path d="M17.94 17.94A10.94 10.94 0 0112 20c-7 0-11-8-11-8a18.5 18.5 0 015.06-5.94M9.9 4.24A9.12 9.12 0 0112 4c7 0 11 8 11 8a18.5 18.5 0 01-2.16 3.19m-6.72-1.07a3 3 0 11-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
                </button>
            </div>
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-primary">Reset Password</button>
    </form>

    <style>
        .pwd-req { font-size:0.71rem; color:#4a5068; transition:color 0.18s; line-height:1.7; }
        .pwd-req.met { color:#4ade80; }
    </style>
    <script>
    const CLIENT_MODE = @json($isClient);

    function checkStrength(val) {
        const rules = CLIENT_MODE ? {
            'req-len': val.length >= 6,
        } : {
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
        const total = Object.keys(rules).length;
        const bar = document.getElementById('strengthBar');
        const colors = CLIENT_MODE ? ['', '#4ade80'] : ['', '#f87171', '#fb923c', '#fbbf24', '#a3e635', '#4ade80'];
        bar.style.width      = (met / total * 100) + '%';
        bar.style.background = colors[met] || '';
    }
    </script>
</x-guest-layout>
