<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @php
        $pageTitle = trim($__env->yieldContent('title', 'আপনার ব্যবসার মার্কেটিং পার্টনার'));
        $pageDescription = trim($__env->yieldContent('description', 'পরিকল্পিত কনটেন্ট, নিয়মিত পোস্টিং এবং সাপ্তাহিক রিপোর্ট — মার্কেটিং কারিগর আপনার ব্যবসার Facebook Page-কে প্রফেশনালভাবে গড়ে তোলে।'));
        $fullTitle = config('app.name') . ' — ' . $pageTitle;
        $ogImage = asset('og-image.png');
        $canonicalUrl = url()->current();
    @endphp
    <title>মার্কেটিং কারিগর - আপনার ব্যবসার মার্কেটিং পার্টনার</title>
    <link rel="icon" href="{{ asset('favicon.ico') }}" sizes="any">
    <meta name="description" content="{{ $pageDescription }}">
    <link rel="canonical" href="{{ $canonicalUrl }}">

    {{-- Open Graph (Facebook, WhatsApp, LinkedIn) --}}
    <meta property="og:type" content="website">
    <meta property="og:site_name" content="{{ config('app.name') }}">
    <meta property="og:locale" content="bn_BD">
    <meta property="og:title" content="{{ $fullTitle }}">
    <meta property="og:description" content="{{ $pageDescription }}">
    <meta property="og:url" content="{{ $canonicalUrl }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta property="og:image:width" content="1200">
    <meta property="og:image:height" content="630">

    {{-- Twitter / X Card --}}
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $fullTitle }}">
    <meta name="twitter:description" content="{{ $pageDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">

    {{-- Structured data (schema.org) — helps search engines and AI assistants identify the business as an entity --}}
    @php
        $structuredData = [
            '@context'    => 'https://schema.org',
            '@type'       => 'ProfessionalService',
            'name'        => config('app.name'),
            'description' => 'সোশ্যাল মিডিয়া কনটেন্ট, ডিজাইন ও Facebook Page ম্যানেজমেন্ট সেবা প্রদানকারী বাংলাদেশি মার্কেটিং এজেন্সি।',
            'url'         => url('/'),
            'image'       => $ogImage,
            'areaServed'  => ['@type' => 'Country', 'name' => 'Bangladesh'],
        ];
        if ($logoUrl = app_logo_url()) {
            $structuredData['logo'] = $logoUrl;
        }
        if ($waNumber = config('services.whatsapp.number')) {
            $structuredData['contactPoint'] = [
                '@type'             => 'ContactPoint',
                'contactType'       => 'customer service',
                'telephone'         => '+' . $waNumber,
                'areaServed'        => 'BD',
                'availableLanguage' => ['bn', 'en'],
            ];
        }
        $sameAs = array_values(array_filter([
            config('services.social.facebook'),
            config('services.social.youtube'),
        ]));
        if (!empty($sameAs)) {
            $structuredData['sameAs'] = $sameAs;
        }
    @endphp
    <script type="application/ld+json">{!! json_encode($structuredData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) !!}</script>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+Bengali:wght@400;500;600;700;800&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        :root {
            --accent: #f2b705;
            --accent-light: #f8d878;
            --whatsapp: #25d366;
            --whatsapp-dark: #1fb855;
            --bg: #0a0c12;
            --bg-alt: #10131c;
            --card: #13161f;
            --border: #212536;
            --text: #e8eaf0;
            --text-muted: #8890a6;
            --text-faint: #4a5068;
        }
        html { scroll-behavior: smooth; }
        body {
            font-family: 'Noto Sans Bengali', 'Inter', sans-serif;
            background: var(--bg);
            color: var(--text);
            line-height: 1.7;
            overflow-x: hidden;
            padding-bottom: 74px; /* room for sticky mobile WhatsApp bar */
        }
        .en { font-family: 'Inter', sans-serif; }
        .wrap { max-width: 1120px; margin: 0 auto; padding: 0 20px; }
        img, svg { display: block; }

        /* ── Header (mobile-first: compact, single row) ── */
        header {
            position: sticky; top: 0; z-index: 100;
            background: rgba(10,12,18,0.9);
            backdrop-filter: blur(10px);
            border-bottom: 1px solid var(--border);
        }
        .nav { display: flex; align-items: center; justify-content: space-between; padding: 12px 20px; gap: 12px; }
        .logo { font-size: 1.1rem; font-weight: 700; color: var(--text); text-decoration: none; letter-spacing: -0.02em; display: inline-flex; align-items: center; }
        .logo span { color: var(--accent); }
        .logo img { max-width: 150px; max-height: 32px; object-fit: contain; }
        .nav-links { display: none; }
        .nav-actions { display: flex; align-items: center; gap: 10px; }
        .nav-login { color: var(--text-muted); text-decoration: none; font-size: 0.82rem; font-weight: 500; }
        .nav-login:hover { color: var(--text); }
        .btn {
            display: inline-flex; align-items: center; justify-content: center; gap: 8px;
            padding: 10px 18px; border-radius: 8px; font-weight: 600; font-size: 0.88rem;
            text-decoration: none; transition: all 0.15s; border: 1px solid transparent; cursor: pointer;
            white-space: nowrap;
        }
        .btn-primary { background: var(--accent); color: #fff; }
        .btn-primary:hover { background: #5a52e0; transform: translateY(-1px); }
        .btn-outline { border-color: var(--border); color: var(--text); }
        .btn-outline:hover { border-color: var(--accent); color: var(--accent-light); }
        .btn-whatsapp { background: var(--whatsapp); color: #06210f; }
        .btn-whatsapp:hover { background: var(--whatsapp-dark); transform: translateY(-1px); }
        .btn-sm { padding: 8px 14px; font-size: 0.8rem; }
        .btn-lg { padding: 15px 26px; font-size: 1rem; width: 100%; }
        .btn svg { flex-shrink: 0; }

        /* ── Sticky mobile WhatsApp bar ── */
        .mobile-cta-bar {
            position: fixed; left: 0; right: 0; bottom: 0; z-index: 200;
            background: var(--bg-alt); border-top: 1px solid var(--border);
            padding: 10px 16px calc(10px + env(safe-area-inset-bottom));
        }
        .mobile-cta-bar .btn { width: 100%; padding: 13px; font-size: 0.95rem; }

        /* ── Section shell ── */
        section { padding: 56px 20px; }
        .section-hd { text-align: center; max-width: 600px; margin: 0 auto 40px; }
        .section-label { color: var(--accent); font-size: 1rem; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; margin-bottom: 10px; }
        .section-hd h2 { font-size: clamp(1.4rem, 5vw, 2.2rem); font-weight: 700; letter-spacing: -0.02em; margin-bottom: 12px; }
        .section-hd p { color: var(--text-muted); font-size: 1rem; }
        .alt-bg { background: var(--bg-alt); }

        /* ── Footer ── */
        footer { border-top: 1px solid var(--border); padding: 32px 20px; text-align: center; }
        footer .footer-contact { margin-bottom: 14px; display: flex; flex-direction: column; align-items: center; gap: 10px; }
        footer .footer-contact a.whatsapp-line { display: inline-flex; align-items: center; gap: 8px; color: #4ade80; text-decoration: none; font-weight: 600; font-size: 0.92rem; }
        .social-links { display: flex; align-items: center; justify-content: center; gap: 10px; margin-bottom: 14px; }
        .social-links a {
            display: inline-flex; align-items: center; justify-content: center; width: 34px; height: 34px;
            border-radius: 50%; background: var(--card); border: 1px solid var(--border); color: var(--text-muted);
            transition: all 0.15s;
        }
        .social-links a:hover { color: var(--accent-light); border-color: var(--accent); }
        footer .footer-links { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-bottom: 10px; }
        footer p { color: var(--text-faint); font-size: 0.8rem; }
        footer a { color: var(--text-muted); text-decoration: none; }
        footer a:hover { color: var(--accent-light); }

        /* ── Tablet and up ── */
        @media (min-width: 700px) {
            body { padding-bottom: 0; }
            .mobile-cta-bar { display: none; }
            .wrap { padding: 0 24px; }
            .nav { padding: 16px 24px; }
            .logo { font-size: 1.25rem; }
            .logo img { max-width: 200px; max-height: none; }
            .nav-links { display: flex; align-items: center; gap: 26px; }
            .nav-links a { color: var(--text-muted); text-decoration: none; font-size: 0.9rem; font-weight: 500; transition: color 0.15s; }
            .nav-links a:hover { color: var(--text); }

            section { padding: 84px 24px; }
            footer .footer-links { flex-direction: row; justify-content: center; gap: 24px; }
            footer .footer-contact { flex-direction: row; justify-content: center; gap: 24px; }
        }

        @yield('styles')
    </style>
</head>
<body>

    <header>
        <nav class="nav wrap">
            <a href="{{ route('home') }}" class="logo">
                @if($logoUrl = app_logo_url())
                    <img src="{{ $logoUrl }}" alt="{{ config('app.name') }}">
                @else
                    মার্কেটিং <span>কারিগর</span>
                @endif
            </a>
            <div class="nav-links">
                @yield('nav-links')
            </div>
            <div class="nav-actions">
                <a href="{{ route('login') }}" class="nav-login">লগইন</a>
                @if($wa = whatsapp_link('আসসালামু আলাইকুম, আমার ব্যবসার জন্য Facebook Marketing সম্পর্কে জানতে চাই।'))
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-sm">
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.29-1.39a9.9 9.9 0 004.75 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.8-4.17-4.94-4.36-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.65.5.24.58.83 2.01.9 2.16.07.15.11.32.02.51-.09.19-.14.31-.27.48-.14.17-.29.37-.41.5-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.61-.07.16-.19.7-.81.89-1.09.19-.28.37-.23.63-.14.26.09 1.68.79 1.97.93.28.14.47.21.54.33.07.12.07.68-.17 1.36z"/></svg>
                    WhatsApp
                </a>
                @endif
            </div>
        </nav>
    </header>

    @yield('content')

    <footer>
        <div class="footer-links">
            <a href="{{ route('home') }}">হোম</a>
            <a href="{{ route('privacy-policy') }}">গোপনীয়তা নীতি</a>
            <a href="{{ route('terms-and-conditions') }}">শর্তাবলী</a>
            <a href="{{ route('login') }}">লগইন</a>
        </div>
        <div class="footer-contact">
            @if($wa)
            <a href="{{ $wa }}" target="_blank" rel="noopener" class="whatsapp-line">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.29-1.39a9.9 9.9 0 004.75 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.8-4.17-4.94-4.36-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.65.5.24.58.83 2.01.9 2.16.07.15.11.32.02.51-.09.19-.14.31-.27.48-.14.17-.29.37-.41.5-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.61-.07.16-.19.7-.81.89-1.09.19-.28.37-.23.63-.14.26.09 1.68.79 1.97.93.28.14.47.21.54.33.07.12.07.68-.17 1.36z"/></svg>
                {{ whatsapp_display() }}
            </a>
            @endif
        </div>
        @if(config('services.social.facebook') || config('services.social.youtube'))
        <div class="social-links">
            @if($fb = config('services.social.facebook'))
            <a href="{{ $fb }}" target="_blank" rel="noopener" title="Facebook Page">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M22 12.06C22 6.5 17.52 2 12 2S2 6.5 2 12.06C2 17.08 5.66 21.24 10.44 22v-7.03H7.9v-2.91h2.54V9.85c0-2.5 1.49-3.89 3.77-3.89 1.09 0 2.23.2 2.23.2v2.46h-1.26c-1.24 0-1.63.77-1.63 1.56v1.88h2.78l-.44 2.91h-2.34V22C18.34 21.24 22 17.08 22 12.06z"/></svg>
            </a>
            @endif
            @if($yt = config('services.social.youtube'))
            <a href="{{ $yt }}" target="_blank" rel="noopener" title="YouTube Channel">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="currentColor"><path d="M23.5 6.19a3.02 3.02 0 00-2.12-2.14C19.5 3.5 12 3.5 12 3.5s-7.5 0-9.38.55A3.02 3.02 0 00.5 6.19 31.6 31.6 0 000 12a31.6 31.6 0 00.5 5.81 3.02 3.02 0 002.12 2.14c1.88.55 9.38.55 9.38.55s7.5 0 9.38-.55a3.02 3.02 0 002.12-2.14A31.6 31.6 0 0024 12a31.6 31.6 0 00-.5-5.81zM9.6 15.6V8.4l6.4 3.6-6.4 3.6z"/></svg>
            </a>
            @endif
        </div>
        @endif
        <p>&copy; {{ date('Y') }} মার্কেটিং কারিগর — সর্বস্বত্ব সংরক্ষিত।</p>
    </footer>

    @if($wa)
    <div class="mobile-cta-bar">
        <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.29-1.39a9.9 9.9 0 004.75 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.8-4.17-4.94-4.36-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.65.5.24.58.83 2.01.9 2.16.07.15.11.32.02.51-.09.19-.14.31-.27.48-.14.17-.29.37-.41.5-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.61-.07.16-.19.7-.81.89-1.09.19-.28.37-.23.63-.14.26.09 1.68.79 1.97.93.28.14.47.21.54.33.07.12.07.68-.17 1.36z"/></svg>
            WhatsApp-এ কথা বলুন
        </a>
    </div>
    @endif

</body>
</html>
