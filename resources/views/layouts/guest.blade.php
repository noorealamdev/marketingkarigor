<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet">
    <script src="{{ asset('build/assets/app.js') }}" defer></script>
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
        /* Loading-state feedback (progress bar + button spinner) driven by
           app.js — kept local here rather than pulling in app.css, since
           this layout intentionally doesn't use Tailwind. */
        #kg-progress { position: fixed; top: 0; left: 0; height: 3px; width: 0; background: #6c63ff; box-shadow: 0 0 8px rgba(108,99,255,.55); z-index: 9999; opacity: 0; pointer-events: none; transition: width .2s ease-out, opacity .2s ease-out; }
        .kg-spinner { display: inline-block; width: 13px; height: 13px; margin-right: 7px; vertical-align: -2px; border: 2px solid rgba(255,255,255,.35); border-top-color: currentColor; border-radius: 50%; animation: kg-spin .7s linear infinite; }
        @keyframes kg-spin { to { transform: rotate(360deg); } }
        .kg-btn-loading, .kg-btn-disabled { opacity: .72; cursor: default; }
        body {
            font-family: 'Figtree', system-ui, -apple-system, sans-serif;
            background: #0a0c12;
            color: #c8cce0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .auth-wrap {
            width: 100%;
            max-width: 420px;
            padding: 24px 16px;
        }
        .auth-brand {
            text-align: center;
            margin-bottom: 32px;
        }
        .auth-brand h1 {
            font-size: 1.9rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            color: #fff;
        }
        .auth-brand h1 span { color: #6c63ff; }
        .auth-brand img { max-width: 260px; object-fit: contain; }
        .auth-brand p {
            font-size: 0.8rem;
            color: #4a5068;
            margin-top: 4px;
            text-transform: uppercase;
            letter-spacing: 0.08em;
        }
        .auth-card {
            background: #13161d;
            border: 1px solid #1e2232;
            border-radius: 14px;
            padding: 32px 28px;
        }
        .auth-desc {
            font-size: 0.875rem;
            color: #6b7590;
            line-height: 1.6;
            margin-bottom: 22px;
        }
        label {
            display: block;
            font-size: 0.8rem;
            font-weight: 600;
            color: #8a91b0;
            margin-bottom: 6px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        input[type="email"],
        input[type="password"],
        input[type="text"] {
            width: 100%;
            background: #0d0f16;
            border: 1px solid #252936;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.9rem;
            color: #c8cce0;
            outline: none;
            transition: border-color 0.15s;
            font-family: inherit;
        }
        input:focus { border-color: #6c63ff; box-shadow: 0 0 0 3px rgba(108,99,255,0.12); }
        input::placeholder { color: #3a4060; }
        .field { margin-bottom: 16px; }
        .invalid-feedback {
            color: #f87171;
            font-size: 0.78rem;
            margin-top: 5px;
        }
        .btn-primary {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            width: 100%;
            padding: 11px 20px;
            background: #6c63ff;
            color: #fff;
            font-weight: 700;
            font-size: 0.875rem;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.15s, transform 0.1s;
            font-family: inherit;
            letter-spacing: 0.01em;
        }
        .btn-primary:hover { background: #7c74ff; }
        .btn-primary:active { transform: scale(0.98); }
        .auth-links {
            text-align: center;
            margin-top: 18px;
            font-size: 0.82rem;
            color: #4a5068;
        }
        .auth-links a {
            color: #6c63ff;
            text-decoration: none;
            font-weight: 600;
        }
        .auth-links a:hover { color: #9d97ff; }
        .alert-success {
            background: rgba(74,222,128,0.08);
            border: 1px solid rgba(74,222,128,0.2);
            color: #4ade80;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.845rem;
            margin-bottom: 18px;
            line-height: 1.5;
        }
        .alert-error {
            background: rgba(248,113,113,0.08);
            border: 1px solid rgba(248,113,113,0.2);
            color: #f87171;
            border-radius: 8px;
            padding: 10px 14px;
            font-size: 0.845rem;
            margin-bottom: 18px;
        }
        .divider { border: none; border-top: 1px solid #1e2232; margin: 20px 0; }
        .remember-row { display: flex; align-items: center; gap: 8px; margin-bottom: 16px; }
        .remember-row input[type="checkbox"] { width: 15px; height: 15px; accent-color: #6c63ff; cursor: pointer; }
        .remember-row label { margin: 0; font-size: 0.845rem; text-transform: none; letter-spacing: 0; color: #6b7590; cursor: pointer; }
        .password-wrap { position: relative; }
        .password-wrap input[type="password"], .password-wrap input[type="text"] { padding-right: 42px; }
        .password-toggle {
            position: absolute; top: 50%; right: 6px; transform: translateY(-50%);
            background: none; border: none; padding: 6px; cursor: pointer; color: #6b7590;
            display: flex; align-items: center; justify-content: center; border-radius: 6px;
        }
        .password-toggle:hover { color: #c8cce0; background: #1a1e28; }
    </style>
</head>
<body>
<div class="auth-wrap">
    <div class="auth-brand">
        <a href="/" style="text-decoration:none;">
            @if($logoUrl = app_logo_url())
                <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
            @else
                <h1>{{ config('app.name') }}</h1>
            @endif
        </a>
    </div>
    <div class="auth-card">
        {{ $slot }}
    </div>
</div>
<script>
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
