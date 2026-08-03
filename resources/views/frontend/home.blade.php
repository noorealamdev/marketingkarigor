@extends('layouts.frontend')

@section('title', 'আপনার ব্যবসার Marketing পার্টনার')
@section('description', 'পরিকল্পিত কনটেন্ট, নিয়মিত পোস্টিং এবং সাপ্তাহিক রিপোর্ট — মার্কেটিং কারিগর আপনার ব্যবসার Facebook Page-কে প্রফেশনালভাবে গড়ে তোলে।')

@section('nav-links')
    <a href="#workflow">কর্মপ্রণালী</a>
    <a href="#services">সেবাসমূহ</a>
    <a href="#why-us">কেন আমরা</a>
@endsection

@section('styles')
    /* ── Hero ── */
    .hero {
        padding: 56px 20px 56px;
        text-align: center;
        position: relative;
        background: radial-gradient(ellipse 700px 380px at 50% -10%, rgba(242,183,5,0.18), transparent);
    }
    .eyebrow {
        display: inline-block; padding: 6px 16px; border-radius: 999px;
        background: rgba(242,183,5,0.12); border: 1px solid rgba(242,183,5,0.25);
        color: var(--accent-light); font-size: 0.78rem; font-weight: 600; margin-bottom: 20px;
    }
    .hero h1 { font-size: clamp(1.7rem, 6vw, 3.2rem); font-weight: 700; letter-spacing: -0.02em; margin-bottom: 16px; }
    .hero .sub { font-size: 1rem; color: var(--text-muted); max-width: 560px; margin: 0 auto 20px; }
    .hero .quote {
        font-size: 1rem; color: var(--accent-light); font-weight: 600; max-width: 560px; margin: 0 auto 30px;
        font-style: italic; line-height: 1.6;
    }
    .hero-ctas { display: flex; flex-direction: column; gap: 12px; max-width: 340px; margin: 0 auto; }
    .trust-bar { display: flex; flex-direction: column; align-items: center; gap: 8px; margin-top: 28px; }
    .trust-bar span { display: inline-flex; align-items: center; gap: 6px; color: var(--text-muted); font-size: 0.82rem; }
    .trust-bar svg { color: #4ade80; flex-shrink: 0; }

    /* ── Problem / pain points ── */
    .pain-grid { display: grid; grid-template-columns: 1fr; gap: 14px; max-width: 720px; margin: 0 auto; }
    .pain-card {
        display: flex; align-items: flex-start; gap: 14px; background: var(--card); border: 1px solid var(--border);
        border-radius: 12px; padding: 18px 20px;
    }
    .pain-icon {
        flex-shrink: 0; width: 34px; height: 34px; border-radius: 9px;
        background: rgba(248,113,113,0.12); color: #f87171;
        display: flex; align-items: center; justify-content: center;
    }
    .pain-card p { font-size: 1rem; color: var(--text); font-weight: 500; }
    .pain-transition { text-align: center; max-width: 560px; margin: 30px auto 0; color: var(--text-muted); font-size: 1rem; }
    .pain-transition strong { color: var(--accent-light); }

    /* ── Workflow steps ── */
    .steps { display: flex; flex-direction: column; gap: 16px; max-width: 780px; margin: 0 auto; }
    .step {
        display: flex; gap: 18px; background: var(--card); border: 1px solid var(--border);
        border-radius: 16px; padding: 22px; transition: border-color 0.2s;
    }
    .step:hover { border-color: rgba(242,183,5,0.4); }
    .step-num {
        flex-shrink: 0; width: 42px; height: 42px; border-radius: 11px;
        background: rgba(242,183,5,0.12); border: 1px solid rgba(242,183,5,0.3);
        display: flex; align-items: center; justify-content: center;
        font-size: 1.05rem; font-weight: 800; color: var(--accent-light);
    }
    .step-body h3 { font-size: 1.05rem; font-weight: 700; margin-bottom: 6px; }
    .step-body p { color: var(--text-muted); font-size: 1rem; margin-bottom: 10px; }
    .step-tags { display: flex; flex-wrap: wrap; gap: 7px; }
    .step-tags span {
        background: rgba(255,255,255,0.04); border: 1px solid var(--border); color: var(--text-muted);
        font-size: 0.74rem; padding: 4px 11px; border-radius: 999px;
    }

    /* ── Services grid ── */
    .services-grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
    .service-card {
        display: flex; align-items: center; gap: 14px; background: var(--card); border: 1px solid var(--border);
        border-radius: 12px; padding: 18px 20px; transition: transform 0.15s, border-color 0.15s;
    }
    .service-card:hover { transform: translateY(-2px); border-color: rgba(242,183,5,0.4); }
    .service-icon {
        flex-shrink: 0; width: 36px; height: 36px; border-radius: 10px;
        background: rgba(74,222,128,0.12); color: #4ade80;
        display: flex; align-items: center; justify-content: center;
    }
    .service-card span { font-weight: 600; font-size: 0.92rem; }

    /* ── Why us ── */
    .why-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
    .why-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 24px; }
    .why-icon {
        width: 42px; height: 42px; border-radius: 10px; background: rgba(242,183,5,0.12); color: var(--accent-light);
        display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
    }
    .why-card h3 { font-size: 1rem; font-weight: 700; margin-bottom: 6px; }
    .why-card p { font-size: 1rem; color: var(--text-muted); line-height: 1.6; }
    .new-agency-note {
        max-width: 640px; margin: 32px auto 0; text-align: center; background: rgba(242,183,5,0.06);
        border: 1px dashed rgba(242,183,5,0.3); border-radius: 14px; padding: 20px 22px;
        font-size: 1rem; color: var(--text-muted);
    }
    .new-agency-note strong { color: var(--accent-light); }

    /* ── Trust badges ── */
    .trust-badges-grid { display: grid; grid-template-columns: 1fr; gap: 14px; }
    .trust-badge { display: flex; align-items: flex-start; gap: 14px; background: var(--card); border: 1px solid var(--border); border-radius: 12px; padding: 18px 20px; }
    .trust-badge-icon {
        flex-shrink: 0; width: 36px; height: 36px; border-radius: 9px;
        background: rgba(74,222,128,0.12); color: #4ade80;
        display: flex; align-items: center; justify-content: center;
    }
    .trust-badge h3 { font-size: 0.92rem; font-weight: 700; margin-bottom: 4px; }
    .trust-badge p { font-size: 0.85rem; color: var(--text-muted); line-height: 1.55; }
    .trust-links { text-align: center; margin-top: 26px; font-size: 0.85rem; color: var(--text-muted); }
    .trust-links a { color: var(--accent-light); }

    /* ── FAQ ── */
    .faq-list { max-width: 720px; margin: 0 auto; display: flex; flex-direction: column; gap: 10px; }
    .faq-item { background: var(--card); border: 1px solid var(--border); border-radius: 12px; overflow: hidden; }
    .faq-item summary {
        list-style: none; cursor: pointer; padding: 16px 20px; display: flex; align-items: center;
        justify-content: space-between; gap: 12px; font-weight: 600; font-size: 1rem;
    }
    .faq-item summary::-webkit-details-marker { display: none; }
    .faq-item summary .chevron { flex-shrink: 0; transition: transform 0.2s; color: var(--text-muted); }
    .faq-item[open] summary .chevron { transform: rotate(180deg); }
    .faq-item .faq-answer { padding: 0 20px 18px; color: var(--text-muted); font-size: 1rem; line-height: 1.7; }

    /* ── Principles ── */
    .principles-grid { display: grid; grid-template-columns: 1fr; gap: 16px; }
    .principle-card { background: var(--card); border: 1px solid var(--border); border-radius: 14px; padding: 22px; }
    .principle-icon {
        width: 40px; height: 40px; border-radius: 10px; background: rgba(242,183,5,0.12); color: var(--accent-light);
        display: flex; align-items: center; justify-content: center; margin-bottom: 14px;
    }
    .principle-card p { font-size: 1rem; color: var(--text); font-weight: 500; line-height: 1.6; }

    /* ── Goal ── */
    .goal-box {
        max-width: 720px; margin: 0 auto; text-align: center; background: linear-gradient(145deg, rgba(242,183,5,0.1), rgba(242,183,5,0.02));
        border: 1px solid rgba(242,183,5,0.25); border-radius: 20px; padding: 32px 22px;
    }
    .goal-box p { font-size: 1rem; line-height: 1.85; color: var(--text); }

    /* ── CTA banner ── */
    .cta-banner {
        max-width: 880px; margin: 0 auto; text-align: center; padding: 40px 24px; border-radius: 22px;
        background: linear-gradient(135deg, #c9910d, #8a5f09);
    }
    .cta-banner h2 { color: #fff; font-size: clamp(1.35rem, 5vw, 2rem); font-weight: 700; margin-bottom: 12px; }
    .cta-banner p { color: rgba(255,255,255,0.85); margin-bottom: 24px; font-size: 0.95rem; }
    .cta-banner .btn-whatsapp { background: #fff; color: #128c3e; max-width: 320px; margin: 0 auto; }
    .cta-banner .btn-whatsapp:hover { background: #f0fff5; }

    @media (min-width: 700px) {
        .hero { padding: 90px 24px 80px; }
        .hero .sub, .hero .quote { font-size: 1.1rem; max-width: 620px; }
        .hero-ctas { flex-direction: row; justify-content: center; max-width: none; }
        .hero-ctas .btn { width: auto; }
        .trust-bar { flex-direction: row; flex-wrap: wrap; justify-content: center; gap: 20px; }

        .pain-grid { grid-template-columns: repeat(2, 1fr); }
        .steps { gap: 20px; }
        .step { padding: 28px 30px; }
        .services-grid { grid-template-columns: repeat(auto-fit, minmax(230px, 1fr)); }
        .why-grid { grid-template-columns: repeat(3, 1fr); }
        .trust-badges-grid { grid-template-columns: repeat(2, 1fr); }
        .principles-grid { grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); }
        .goal-box { padding: 48px 40px; }
        .cta-banner { padding: 60px 40px; }
        .cta-banner .btn-whatsapp { width: auto; }
    }
@endsection

@section('content')

    @php
        $wa = whatsapp_link('আসসালামু আলাইকুম, আমার ব্যবসার জন্য Facebook Marketing সম্পর্কে জানতে চাই।');
    @endphp

    <!-- Hero -->
    <section class="hero">
        <div class="wrap">
            <span class="eyebrow">নতুন এজেন্সি, নিবেদিত সেবা</span>
            <h1>আপনার ব্যবসার Facebook Page-কে দিন প্রফেশনাল রূপ</h1>
            <p class="sub">পরিকল্পনাহীন পোস্টিং নয় — একটি সুসংগঠিত কনটেন্ট সিস্টেমের মাধ্যমে আমরা আপনার ব্র্যান্ডকে গ্রাহকের কাছে বিশ্বাসযোগ্য করে তুলি।</p>
            <p class="quote">"সুন্দর ডিজাইন নয়, পরিকল্পিত কনটেন্টই ব্যবসাকে বড় করে।"</p>
            <div class="hero-ctas">
                @if($wa)
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.29-1.39a9.9 9.9 0 004.75 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.8-4.17-4.94-4.36-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.65.5.24.58.83 2.01.9 2.16.07.15.11.32.02.51-.09.19-.14.31-.27.48-.14.17-.29.37-.41.5-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.61-.07.16-.19.7-.81.89-1.09.19-.28.37-.23.63-.14.26.09 1.68.79 1.97.93.28.14.47.21.54.33.07.12.07.68-.17 1.36z"/></svg>
                    WhatsApp-এ কথা বলুন
                </a>
                @endif
                <a href="#workflow" class="btn btn-outline btn-lg">আমাদের কাজের ধাপ দেখুন</a>
            </div>
            <div class="trust-bar">
                <span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    সরাসরি WhatsApp-এ কথা বলুন
                </span>
                <span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    সাপ্তাহিক পারফরম্যান্স রিপোর্ট
                </span>
                <span>
                    <svg width="15" height="15" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><polyline points="20 6 9 17 4 12"/></svg>
                    পোস্টের আগে আপনার অনুমোদন
                </span>
            </div>
        </div>
    </section>

    <!-- Problem -->
    <section class="alt-bg">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">সমস্যা</div>
                <h2>আপনার Facebook Page-এ কি এই সমস্যাগুলো হচ্ছে?</h2>
            </div>
            @php
                $pains = [
                    'নিয়মিত পোস্ট করছেন, কিন্তু কোনো এনগেজমেন্ট বাড়ছে না।',
                    'কনটেন্টে কোনো পরিকল্পনা বা ধারাবাহিকতা নেই।',
                    'কী পোস্ট করবেন, কবে করবেন — বুঝতে পারছেন না।',
                    'প্রতিযোগীরা Facebook-এ এগিয়ে যাচ্ছে।',
                    'রেজাল্ট ট্র্যাক করার কোনো নির্দিষ্ট উপায় নেই।',
                    'ডিজাইন বা ভিডিও তৈরির জন্য আলাদা মানুষ খুঁজতে হচ্ছে।',
                ];
            @endphp
            <div class="pain-grid">
                @foreach($pains as $pain)
                <div class="pain-card">
                    <div class="pain-icon">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                    </div>
                    <p>{{ $pain }}</p>
                </div>
                @endforeach
            </div>
            <p class="pain-transition">আমরা এই সমস্যাগুলোর সমাধান নিয়ে এসেছি — <strong>একটি পরিকল্পিত, ধাপে-ধাপে কনটেন্ট সিস্টেমের মাধ্যমে।</strong></p>
        </div>
    </section>

    <!-- Workflow -->
    <section id="workflow">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">আমাদের সিস্টেম</div>
                <h2>আমাদের কাজের ৮টি ধাপ</h2>
                <p>প্রতিটি প্রজেক্ট একটি সুনির্দিষ্ট, পরিকল্পিত প্রক্রিয়া অনুসরণ করে সম্পন্ন হয়।</p>
            </div>

            <div class="steps">
                <div class="step">
                    <div class="step-num">১</div>
                    <div class="step-body">
                        <h3>ব্যবসা সম্পর্কে জানা</h3>
                        <p>কাজ শুরু করার আগে আমরা আপনার ব্যবসা সম্পর্কে বিস্তারিত জানি।</p>
                        <div class="step-tags">
                            <span>ব্যবসার ধরন</span><span>লক্ষ্য গ্রাহক</span><span>প্রতিযোগী</span><span>সেবা বা পণ্য</span><span>ব্র্যান্ডের লক্ষ্য</span>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">২</div>
                    <div class="step-body">
                        <h3>কনটেন্ট স্ট্রাটেজি</h3>
                        <p>এরপর আমরা একটি মাসিক কনটেন্ট পরিকল্পনা তৈরি করি।</p>
                        <div class="step-tags">
                            <span>অফার পোস্ট</span><span>শিক্ষামূলক পোস্ট</span><span>গ্রাহকের সমস্যা ও সমাধান</span><span>ভিডিও কনটেন্ট</span><span class="en">Before & After</span><span>ব্র্যান্ডিং পোস্ট</span><span>বিশ্বাস তৈরির কনটেন্ট</span>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">৩</div>
                    <div class="step-body">
                        <h3>ডিজাইন ও ভিডিও তৈরি</h3>
                        <p>প্রতিটি কনটেন্ট আপনার ব্র্যান্ডের রঙ, স্টাইল এবং পরিচয় অনুযায়ী তৈরি করা হয়।</p>
                        <div class="step-tags">
                            <span class="en">Facebook Image Post</span><span class="en">Promotional Video</span><span class="en">Reels</span><span class="en">Motion Graphics</span><span class="en">Carousel Design</span><span class="en">Cover Photo</span><span>লোগো ও Branding</span>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">৪</div>
                    <div class="step-body">
                        <h3>কনটেন্ট অনুমোদন</h3>
                        <p>সব কনটেন্ট প্রথমে আপনার কাছে পাঠানো হয়। আপনার মতামত অনুযায়ী প্রয়োজনীয় পরিবর্তন করে চূড়ান্ত অনুমোদন নেওয়া হয়।</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">৫</div>
                    <div class="step-body">
                        <h3>নিয়মিত পোস্টিং</h3>
                        <p>নির্ধারিত সময়সূচি অনুযায়ী আমরা Facebook Page-এ নিয়মিত কনটেন্ট প্রকাশ করি। এর মাধ্যমে পেজে ধারাবাহিকতা বজায় থাকে এবং ব্র্যান্ডের উপস্থিতি শক্তিশালী হয়।</p>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">৬</div>
                    <div class="step-body">
                        <h3 class="en">Page Management</h3>
                        <p>আমরা নিয়মিত পেজ পরিচালনা করি।</p>
                        <div class="step-tags">
                            <span>পোস্ট শিডিউল করি</span><span>ক্যাপশন লিখি</span><span>হ্যাশট্যাগ ব্যবহার করি</span><span>কনটেন্ট সংগঠিত রাখি</span>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">৭</div>
                    <div class="step-body">
                        <h3>সাপ্তাহিক রিপোর্ট</h3>
                        <p>প্রতি সপ্তাহে একটি Performance Report প্রদান করা হয়।</p>
                        <div class="step-tags">
                            <span class="en">Reach</span><span class="en">Engagement</span><span class="en">Video Views</span><span class="en">Best Performing Post</span><span>পরবর্তী সপ্তাহের পরিকল্পনা</span>
                        </div>
                    </div>
                </div>

                <div class="step">
                    <div class="step-num">৮</div>
                    <div class="step-body">
                        <h3>ধারাবাহিক উন্নয়ন</h3>
                        <p>প্রতিটি রিপোর্ট বিশ্লেষণ করে আমরা পরবর্তী কনটেন্ট আরও উন্নত করি। আমাদের লক্ষ্য শুধুমাত্র পোস্ট করা নয়, বরং প্রতিটি মাসে আগের মাসের চেয়ে ভালো ফল পাওয়া।</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services -->
    <section id="services" class="alt-bg">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">Services</div>
                <h2>আমরা যা করি</h2>
            </div>
            @php
                $services = [
                    ['Facebook Content Design', '<rect x="3" y="3" width="18" height="18" rx="2"/><circle cx="8.5" cy="8.5" r="1.5"/><path d="M21 15l-5-5L5 21"/>'],
                    ['Promotional Video', '<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>'],
                    ['Reels Editing', '<path d="M4 4h16v16H4z"/><path d="M4 9h16M4 15h16M9 4v16M15 4v16"/>'],
                    ['Facebook Page Management', '<circle cx="12" cy="12" r="10"/><path d="M12 6v6l4 2"/>'],
                    ['Branding', '<path d="M12 2l3 7h7l-5.5 4.5L18 21l-6-4-6 4 1.5-7.5L2 9h7z"/>'],
                    ['Cover Photo Design', '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M3 16l5-5 4 4 5-6 4 4"/>'],
                    ['Logo Design', '<circle cx="12" cy="12" r="9"/><circle cx="12" cy="12" r="4"/>'],
                    ['Marketing Strategy', '<path d="M3 3v18h18"/><path d="M7 14l4-4 4 4 5-7"/>'],
                ];
            @endphp
            <div class="services-grid">
                @foreach($services as [$label, $icon])
                <div class="service-card">
                    <div class="service-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                    </div>
                    <span class="en">{{ $label }}</span>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Why us -->
    <section id="why-us">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">কেন আমরা</div>
                <h2>কেন আমাদের সাথে কাজ করবেন</h2>
            </div>
            @php
                $whys = [
                    ['সরাসরি যোগাযোগ', 'কোনো কল সেন্টার নয় — আপনার কাজ যিনি সরাসরি সামলাচ্ছেন, তার সাথেই WhatsApp-এ কথা বলবেন।', '<path d="M21 11.5a8.38 8.38 0 01-.9 3.8 8.5 8.5 0 01-7.6 4.7 8.38 8.38 0 01-3.8-.9L3 21l1.9-5.7a8.38 8.38 0 01-.9-3.8 8.5 8.5 0 014.7-7.6 8.38 8.38 0 013.8-.9h.5a8.48 8.48 0 018 8v.5z"/>'],
                    ['প্রতিটি ক্লায়েন্টের জন্য বিশেষ মনোযোগ', 'আমরা সীমিত সংখ্যক ক্লায়েন্ট নিয়ে কাজ করি, যাতে প্রতিটি ব্র্যান্ডকে যথেষ্ট সময় ও মনোযোগ দেওয়া যায়।', '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.5-7 8-7s8 3 8 7"/>'],
                    ['সম্পূর্ণ স্বচ্ছতা', 'প্রতিটি পোস্ট প্রকাশের আগে আপনার অনুমোদন নেওয়া হয়, এবং প্রতি সপ্তাহে ফলাফলের রিপোর্ট দেওয়া হয়।', '<path d="M2 12s3.5-7 10-7 10 7 10 7-3.5 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/>'],
                ];
            @endphp
            <div class="why-grid">
                @foreach($whys as [$title, $desc, $icon])
                <div class="why-card">
                    <div class="why-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                    </div>
                    <h3>{{ $title }}</h3>
                    <p>{{ $desc }}</p>
                </div>
                @endforeach
            </div>
            <div class="new-agency-note">
                <strong>মার্কেটিং কারিগর একটি নতুন এজেন্সি</strong> — এবং এটিই আমাদের শক্তি। আমরা প্রতিটি ক্লায়েন্টকে প্রমাণ করে দেখাতে চাই, তাই শুরু থেকেই সততা, পরিশ্রম আর সরাসরি যোগাযোগে বিশ্বাসী।
            </div>
        </div>
    </section>

    <!-- Trust -->
    <section id="trust" class="alt-bg">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">বিশ্বাসযোগ্যতা</div>
                <h2>আপনার নিরাপত্তা ও স্বচ্ছতার নিশ্চয়তা</h2>
            </div>
            @php
                $trustBadges = [
                    ['ইনভয়েসভিত্তিক লেনদেন', 'প্রতিটি পেমেন্টের বিপরীতে সুনির্দিষ্ট ইনভয়েস দেওয়া হয় — কোনো লুকানো খরচ নেই।', '<path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="9" y1="13" x2="15" y2="13"/><line x1="9" y1="17" x2="13" y2="17"/>'],
                    ['পোস্টের আগে অনুমোদন', 'আপনার সম্মতি ছাড়া কোনো কনটেন্ট কখনো পাবলিশ করা হয় না।', '<path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/>'],
                    ['ডেটা ও Page অ্যাক্সেস সুরক্ষিত', 'আপনার ব্যবসার তথ্য ও Facebook Page অ্যাক্সেস কখনো তৃতীয় পক্ষের সাথে শেয়ার করা হয় না।', '<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0110 0v4"/>'],
                    ['নমনীয় চুক্তি', 'প্রয়োজন অনুযায়ী মাসিক ভিত্তিতে অথবা নির্দিষ্ট মেয়াদে — দুইভাবেই কাজ করার সুযোগ আছে।', '<rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/>'],
                ];
            @endphp
            <div class="trust-badges-grid">
                @foreach($trustBadges as [$title, $desc, $icon])
                <div class="trust-badge">
                    <div class="trust-badge-icon">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">{!! $icon !!}</svg>
                    </div>
                    <div>
                        <h3>{{ $title }}</h3>
                        <p>{{ $desc }}</p>
                    </div>
                </div>
                @endforeach
            </div>
            <p class="trust-links">বিস্তারিত জানুন আমাদের <a href="{{ route('privacy-policy') }}">গোপনীয়তা নীতি</a> ও <a href="{{ route('terms-and-conditions') }}">শর্তাবলী</a> পাতায়।</p>
        </div>
    </section>

    <!-- Principles -->
    <section>
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">Principles</div>
                <h2>আমাদের কাজের মূলনীতি</h2>
            </div>
            @php
                $principles = [
                    'পরিকল্পনা ছাড়া পোস্ট নয়।',
                    'প্রতিটি কনটেন্টের একটি উদ্দেশ্য থাকবে।',
                    'ব্র্যান্ডের পরিচয় সবসময় একই থাকবে।',
                    'নিয়মিত রিপোর্টের মাধ্যমে অগ্রগতি দেখানো হবে।',
                    'দীর্ঘমেয়াদে ব্যবসার বিশ্বাসযোগ্যতা গড়ে তোলা হবে।',
                ];
            @endphp
            <div class="principles-grid">
                @foreach($principles as $principle)
                <div class="principle-card">
                    <div class="principle-icon">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>
                    </div>
                    <p>{{ $principle }}</p>
                </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- Goal -->
    <section class="alt-bg">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">Our Goal</div>
                <h2>আমাদের লক্ষ্য</h2>
            </div>
            <div class="goal-box">
                <p>আমরা শুধু পোস্ট ডিজাইন করি না — আমরা এমন একটি ধারাবাহিক কনটেন্ট সিস্টেম তৈরি করি, যা আপনার ব্যবসাকে আরও প্রফেশনালভাবে উপস্থাপন করে, সম্ভাব্য গ্রাহকদের কাছে বিশ্বাস তৈরি করে এবং দীর্ঘমেয়াদে আপনার ব্র্যান্ডকে শক্তিশালী করে।</p>
            </div>
        </div>
    </section>

    <!-- FAQ -->
    <section id="faq">
        <div class="wrap">
            <div class="section-hd">
                <div class="section-label">প্রশ্নোত্তর</div>
                <h2>সচরাচর জিজ্ঞাসিত প্রশ্ন</h2>
            </div>
            @php
                $faqs = [
                    ['টাকা দেওয়ার পর যদি কাজ পছন্দ না হয়?', 'প্রতিটি কনটেন্ট প্রকাশের আগে আপনার অনুমোদন নেওয়া হয় — তাই অপছন্দের কনটেন্ট কখনো পাবলিশ হবে না। প্রয়োজনে নির্ধারিত রিভিশনের সুযোগও থাকে।'],
                    ['চুক্তি কীভাবে হয় — মাসিক নাকি নির্দিষ্ট মেয়াদী?', 'আপনার প্রয়োজন অনুযায়ী আমরা মাসিক ভিত্তিতে অথবা নির্দিষ্ট মেয়াদে — দুইভাবেই কাজ করি। শুরুতেই WhatsApp-এ আলোচনা করে যেটা আপনার জন্য উপযুক্ত, সেভাবে এগোনো হয়।'],
                    ['কাজ শুরু করতে কত সময় লাগে?', 'আপনার ব্যবসা সম্পর্কে জানার পর সাধারণত অল্প কয়েক দিনের মধ্যেই প্রথম কনটেন্ট পরিকল্পনা প্রস্তুত করা হয়।'],
                    ['পেমেন্ট কীভাবে করব?', 'প্রতিটি কাজের বিপরীতে একটি সুনির্দিষ্ট ইনভয়েস দেওয়া হয়, যেখানে পরিমাণ ও পরিশোধের তারিখ স্পষ্টভাবে উল্লেখ থাকে — কোনো লুকানো খরচ নেই।'],
                    ['Facebook Page-এর এডমিন অ্যাক্সেস দেওয়া কি নিরাপদ?', 'হ্যাঁ। শুধুমাত্র কাজের জন্য প্রয়োজনীয় অ্যাক্সেস নেওয়া হয়, এবং চাইলে যেকোনো সময় তা প্রত্যাহার করে নিতে পারবেন। বিস্তারিত আমাদের গোপনীয়তা নীতিতে দেওয়া আছে।'],
                    ['নির্দিষ্ট ফলাফল বা গ্যারান্টি দেওয়া হয় কি?', 'সততার সাথে বলছি — Facebook-এর অ্যালগরিদম ও বাজারের প্রতিযোগিতার কারণে কেউই ১০০% নির্দিষ্ট ফলাফলের নিশ্চয়তা দিতে পারে না। আমরা যা নিশ্চিত করি তা হলো পরিকল্পিত কাজ, স্বচ্ছ রিপোর্টিং এবং প্রতিনিয়ত উন্নতির চেষ্টা।'],
                ];
            @endphp
            <div class="faq-list">
                @foreach($faqs as [$q, $a])
                <details class="faq-item">
                    <summary>
                        <span>{{ $q }}</span>
                        <svg class="chevron" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">{{ $a }}</div>
                </details>
                @endforeach
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section>
        <div class="wrap">
            <div class="cta-banner">
                <h2>আপনার ব্যবসাকে এগিয়ে নিতে প্রস্তুত?</h2>
                <p>আজই WhatsApp-এ আমাদের সাথে যোগাযোগ করুন এবং একটি পরিকল্পিত কনটেন্ট সিস্টেম শুরু করুন।</p>
                @if($wa)
                <a href="{{ $wa }}" target="_blank" rel="noopener" class="btn btn-whatsapp btn-lg">
                    <svg width="18" height="18" viewBox="0 0 24 24" fill="currentColor"><path d="M12.04 2C6.58 2 2.13 6.45 2.13 11.91c0 1.75.46 3.46 1.32 4.96L2 22l5.29-1.39a9.9 9.9 0 004.75 1.21h.01c5.46 0 9.91-4.45 9.91-9.91C21.96 6.45 17.5 2 12.04 2zm5.8 14.02c-.24.68-1.4 1.3-1.93 1.38-.5.08-1.12.11-1.8-.11-.42-.13-.96-.31-1.65-.6-2.9-1.25-4.8-4.17-4.94-4.36-.14-.19-1.18-1.57-1.18-3 0-1.42.75-2.12 1.01-2.41.27-.29.58-.36.78-.36.19 0 .39 0 .56.01.18.01.42-.07.65.5.24.58.83 2.01.9 2.16.07.15.11.32.02.51-.09.19-.14.31-.27.48-.14.17-.29.37-.41.5-.14.14-.28.29-.12.57.16.28.71 1.17 1.52 1.9 1.05.94 1.93 1.23 2.21 1.37.28.14.44.12.61-.07.16-.19.7-.81.89-1.09.19-.28.37-.23.63-.14.26.09 1.68.79 1.97.93.28.14.47.21.54.33.07.12.07.68-.17 1.36z"/></svg>
                    WhatsApp-এ মেসেজ করুন
                </a>
                @endif
            </div>
        </div>
    </section>

@endsection
