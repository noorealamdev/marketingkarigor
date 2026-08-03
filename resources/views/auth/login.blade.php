<x-guest-layout>
    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <form method="POST" action="{{ route('login') }}">
        @csrf

        <div class="field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="field">
            <label for="password">Password</label>
            <input id="password" type="password" name="password" required autocomplete="current-password" placeholder="••••••••">
            @error('password')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>

        <div class="remember-row">
            <input id="remember_me" type="checkbox" name="remember">
            <label for="remember_me">Keep me signed in</label>
        </div>

        <button type="submit" class="btn-primary">Sign In</button>

        @if (Route::has('password.request'))
        <div class="auth-links" style="margin-top:16px;">
            <a href="{{ route('password.request') }}">Forgot your password?</a>
        </div>
        @endif
    </form>
</x-guest-layout>
