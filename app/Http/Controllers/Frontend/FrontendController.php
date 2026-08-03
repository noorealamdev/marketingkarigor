<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class FrontendController extends Controller
{
    public function home()
    {
        if (!auth()->check()) {
            return view('frontend.home');
        }

        if (auth()->user()->isAdmin()) {
            return redirect()->route('dashboard');
        }

        if (auth()->user()->hasRole('client')) {
            return redirect()->route('client.dashboard');
        }

        return redirect()->route('tasks.index');
    }

    public function privacyPolicy()
    {
        return view('frontend.privacy-policy');
    }

    public function termsAndConditions()
    {
        return view('frontend.terms-and-conditions');
    }

    public function robots()
    {
        $lines = [
            'User-agent: *',
            'Allow: /',
            'Allow: /privacy-policy',
            'Allow: /terms-and-conditions',
            'Disallow: /dashboard',
            'Disallow: /tasks',
            'Disallow: /projects',
            'Disallow: /clients',
            'Disallow: /invoices',
            'Disallow: /reports',
            'Disallow: /team',
            'Disallow: /finance',
            'Disallow: /salaries',
            'Disallow: /settings',
            'Disallow: /calendar',
            'Disallow: /portal',
            'Disallow: /notifications',
            'Disallow: /profile',
            'Disallow: /run-link',
            '',
            'Sitemap: ' . url('/sitemap.xml'),
        ];

        return response(implode("\n", $lines), 200, ['Content-Type' => 'text/plain']);
    }

    public function sitemap()
    {
        $urls = [
            ['loc' => url('/'), 'priority' => '1.0'],
            ['loc' => route('privacy-policy'), 'priority' => '0.3'],
            ['loc' => route('terms-and-conditions'), 'priority' => '0.3'],
        ];

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
        foreach ($urls as $u) {
            $xml .= '  <url>' . "\n";
            $xml .= '    <loc>' . htmlspecialchars($u['loc'], ENT_XML1) . '</loc>' . "\n";
            $xml .= '    <priority>' . $u['priority'] . '</priority>' . "\n";
            $xml .= '  </url>' . "\n";
        }
        $xml .= '</urlset>';

        return response($xml, 200, ['Content-Type' => 'application/xml']);
    }

    public function llmsTxt()
    {
        $appName = config('app.name');
        $wa      = config('services.whatsapp.number');

        $lines = [
            "# {$appName}",
            '',
            '> সোশ্যাল মিডিয়া কনটেন্ট, ডিজাইন ও Facebook Page ম্যানেজমেন্ট সেবা প্রদানকারী বাংলাদেশি মার্কেটিং এজেন্সি।',
            '',
            "{$appName} is a Bangladesh-based social media marketing agency offering content strategy, design, video/reels production, Facebook Page management, and weekly performance reporting for small and medium businesses in Bangladesh.",
            '',
            '## Pages',
            '',
            '- [Home](' . url('/') . '): Services, workflow, and pricing approach.',
            '- [Privacy Policy](' . route('privacy-policy') . ')',
            '- [Terms & Conditions](' . route('terms-and-conditions') . ')',
            '',
            '## Contact',
            '',
            $wa ? "- WhatsApp: +{$wa}" : null,
            '- Website: ' . url('/'),
        ];

        return response(implode("\n", array_filter($lines, fn($l) => $l !== null)), 200, ['Content-Type' => 'text/plain']);
    }
}
