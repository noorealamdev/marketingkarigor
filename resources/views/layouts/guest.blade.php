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
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }
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
</body>
</html>
