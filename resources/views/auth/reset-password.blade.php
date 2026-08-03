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
            <input id="password" type="password" name="password" required autocomplete="new-password" placeholder="Min. 8 characters" oninput="checkStrength(this.value)">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div style="margin:-10px 0 16px;">
            <div style="height:4px;background:#1a1e28;border-radius:2px;overflow:hidden;margin-bottom:9px;">
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

        <div class="field">
            <label for="password_confirmation">Confirm New Password</label>
            <input id="password_confirmation" type="password" name="password_confirmation" required autocomplete="new-password" placeholder="Repeat password">
            @error('password_confirmation')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <button type="submit" class="btn-primary">Reset Password</button>
    </form>

    <style>
        .pwd-req { font-size:0.71rem; color:#4a5068; transition:color 0.18s; line-height:1.7; }
        .pwd-req.met { color:#4ade80; }
    </style>
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
    </script>
</x-guest-layout>
