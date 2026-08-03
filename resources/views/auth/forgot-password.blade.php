<x-guest-layout>
    @if (session('status'))
        <div class="alert-success">{{ session('status') }}</div>
    @endif

    <p class="auth-desc">Enter your email address and we'll send you a password reset link.</p>

    <form method="POST" action="{{ route('password.email') }}">
        @csrf
        <div class="field">
            <label for="email">Email Address</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus autocomplete="username" placeholder="you@example.com">
            @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
        </div>
        <button type="submit" class="btn-primary">Send Reset Link</button>
    </form>

    <div class="auth-links" style="margin-top:20px;">
        <a href="{{ route('login') }}">← Back to login</a>
    </div>
</x-guest-layout>
