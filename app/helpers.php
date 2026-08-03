<?php

if (!function_exists('format_currency')) {
    /**
     * Format an amount with the app's currency symbol, no decimals.
     * Returns safe HTML — use {!! format_currency($amount) !!} in Blade.
     */
    function format_currency(float|int|string|null $amount): string
    {
        return '<span class="currency-symbol">৳</span>' . number_format((float) ($amount ?? 0), 0);
    }
}

if (!function_exists('app_logo_url')) {
    /**
     * URL of the uploaded app logo, or null if none is set — use in regular HTML views.
     */
    function app_logo_url(): ?string
    {
        $media = \App\Models\Setting::instance()->getFirstMedia('logo');
        return $media ? rebase_media_url($media->getUrl()) : null;
    }
}

if (!function_exists('app_logo_path')) {
    /**
     * Local filesystem path of the uploaded app logo, or null if none is set.
     * Use this (not app_logo_url) inside PDF views — dompdf can't fetch remote
     * URLs unless enable_remote is turned on, but it can embed local files directly.
     */
    function app_logo_path(): ?string
    {
        $media = \App\Models\Setting::instance()->getFirstMedia('logo');
        return $media?->getPath();
    }
}

if (!function_exists('amount_in_words')) {
    /**
     * Spell out a Taka amount using Bangladeshi numbering (Crore/Lakh/Thousand),
     * e.g. 125000.50 -> "One Lakh Twenty-Five Thousand Taka and 50 Paisa Only".
     * Standard trust cue on BD financial documents (invoices, cheques).
     */
    function amount_in_words(float|int|string $amount): string
    {
        $amount = round((float) $amount, 2);
        $taka = (int) floor($amount);
        $paisa = (int) round(($amount - $taka) * 100);

        $words = $taka > 0 ? convert_number_to_bd_words($taka) : 'Zero';

        $result = "{$words} Taka";
        if ($paisa > 0) {
            $result .= ' and ' . convert_number_to_bd_words($paisa) . ' Paisa';
        }

        return $result . ' Only';
    }
}

if (!function_exists('convert_number_to_bd_words')) {
    function convert_number_to_bd_words(int $number): string
    {
        $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine',
            'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        $belowThousand = function (int $n) use ($ones, $tens): string {
            $parts = [];
            if ($n >= 100) {
                $parts[] = $ones[intdiv($n, 100)] . ' Hundred';
                $n %= 100;
            }
            if ($n >= 20) {
                $tensWord = $tens[intdiv($n, 10)];
                $n %= 10;
                $parts[] = $n > 0 ? "{$tensWord}-{$ones[$n]}" : $tensWord;
            } elseif ($n > 0) {
                $parts[] = $ones[$n];
            }
            return implode(' ', $parts);
        };

        if ($number === 0) {
            return 'Zero';
        }

        $crore = intdiv($number, 10000000); $number %= 10000000;
        $lakh = intdiv($number, 100000); $number %= 100000;
        $thousand = intdiv($number, 1000); $number %= 1000;
        $rest = $number;

        $segments = [];
        if ($crore > 0) $segments[] = $belowThousand($crore) . ' Crore';
        if ($lakh > 0) $segments[] = $belowThousand($lakh) . ' Lakh';
        if ($thousand > 0) $segments[] = $belowThousand($thousand) . ' Thousand';
        if ($rest > 0) $segments[] = $belowThousand($rest);

        return implode(' ', $segments);
    }
}

if (!function_exists('whatsapp_link')) {
    /**
     * wa.me link to the configured business WhatsApp number, with an optional
     * prefilled message. Returns null if no number is configured.
     */
    function whatsapp_link(?string $message = null): ?string
    {
        $number = config('services.whatsapp.number');
        if (empty($number)) return null;

        $url = 'https://wa.me/' . $number;
        if ($message) {
            $url .= '?text=' . rawurlencode($message);
        }
        return $url;
    }
}

if (!function_exists('whatsapp_display')) {
    /**
     * Human-readable local format of the configured WhatsApp number, e.g. "01885-981040".
     * Derives from the same config value as whatsapp_link() so the two never drift —
     * changing WHATSAPP_NUMBER in .env updates both the link and every displayed digit.
     */
    function whatsapp_display(): ?string
    {
        $number = config('services.whatsapp.number');
        if (empty($number)) return null;

        $local = str_starts_with($number, '880') ? '0' . substr($number, 3) : $number;

        if (preg_match('/^0\d{10}$/', $local)) {
            return substr($local, 0, 5) . '-' . substr($local, 5);
        }

        return $number;
    }
}

if (!function_exists('rebase_media_url')) {
    /**
     * Rebuild a Spatie MediaLibrary URL using the current request host via asset().
     * This prevents hardcoded APP_URL from breaking images when the app runs on a
     * different host or port (local dev, Replit, production, etc.).
     */
    function rebase_media_url(string $url): string
    {
        if (empty($url)) return $url;
        $path = ltrim(parse_url($url, PHP_URL_PATH) ?? '', '/');
        return asset($path);
    }
}

if (!function_exists('format_comment')) {
    /**
     * Format comment body: render file/image tokens as links, auto-link plain URLs.
     * Tokens: __IMG__URL and 📎 name\nURL — both rendered as plain file links.
     * Input is plain text; output is safe HTML.
     *
     * ORDER IS CRITICAL: auto-link runs BEFORE placeholders are restored to HTML,
     * so the regex never sees URLs inside href="…" attributes (which would cause
     * double-wrapping and broken tag output).
     */
    function format_comment(string $text): string
    {
        $fileMap = [];

        // Collapse both __IMG__ tokens and 📎 name\nURL tokens into the same
        // file-link map so they all render identically as a plain clickable link.
        $text = preg_replace_callback('/__IMG__(https?:\/\/\S+)/', function ($m) use (&$fileMap) {
            $url  = rebase_media_url($m[1]);
            $name = basename(parse_url($url, PHP_URL_PATH) ?: $url);
            $placeholder = '@@NOORFILE:' . count($fileMap) . ':@@';
            $fileMap[$placeholder] = ['name' => $name, 'url' => $url];
            return $placeholder;
        }, $text);

        $text = preg_replace_callback('/📎 ([^\n@@]+)\n(https?:\/\/\S+)/', function ($m) use (&$fileMap) {
            $placeholder = '@@NOORFILE:' . count($fileMap) . ':@@';
            $fileMap[$placeholder] = ['name' => trim($m[1]), 'url' => rebase_media_url($m[2])];
            return $placeholder;
        }, $text);

        // Escape and format plain text (placeholders survive htmlspecialchars unchanged).
        $safe = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safe = nl2br($safe);

        // Auto-link plain URLs NOW — before any HTML is injected — so the regex
        // cannot accidentally match URLs inside href="…" attributes added below.
        $safe = preg_replace_callback('#(https?://\S+)#i', function ($m) {
            $url   = rebase_media_url(htmlspecialchars_decode($m[1], ENT_QUOTES));
            $safeU = htmlspecialchars($url, ENT_QUOTES, 'UTF-8');
            return '<a href="' . $safeU . '" target="_blank" rel="noopener" class="link">' . $safeU . '</a>';
        }, $safe);

        // Restore file placeholders as icon + filename link.
        // No URLs appear in the display text, so auto-link (already done) cannot touch them.
        $clipIcon = '<svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="flex-shrink:0;color:#6c63ff"><path d="M21.44 11.05l-9.19 9.19a6 6 0 01-8.49-8.49l9.19-9.19a4 4 0 015.66 5.66l-9.2 9.19a2 2 0 01-2.83-2.83l8.49-8.48"/></svg>';
        foreach ($fileMap as $placeholder => $f) {
            $safeName = htmlspecialchars($f['name'], ENT_QUOTES, 'UTF-8');
            $safeUrl  = htmlspecialchars($f['url'],  ENT_QUOTES, 'UTF-8');
            $safe = str_replace(
                $placeholder,
                '<span class="comment-file-row">' . $clipIcon
                    . '<a href="' . $safeUrl . '" target="_blank" rel="noopener" class="link">' . $safeName . '</a>'
                    . '</span>',
                $safe
            );
        }

        return $safe;
    }
}

if (!function_exists('user_avatar')) {
    /**
     * Render a user avatar: photo if uploaded, otherwise styled initial div.
     * Returns safe HTML — use {!! user_avatar($user) !!} in Blade.
     */
    function user_avatar(\App\Models\User $user, int $size = 32, string $fallbackClass = 'avatar', string $extraStyle = ''): string
    {
        $media = $user->getFirstMedia('avatar');
        if ($media) {
            $rawUrl = $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $media->getUrl();
            $url    = rebase_media_url($rawUrl);
            return '<img src="' . htmlspecialchars($url, ENT_QUOTES, 'UTF-8') . '"'
                . ' alt="' . htmlspecialchars($user->name, ENT_QUOTES, 'UTF-8') . '"'
                . ' style="width:' . $size . 'px;height:' . $size . 'px;border-radius:50%;object-fit:cover;flex-shrink:0;' . $extraStyle . '">';
        }
        $initial = htmlspecialchars(strtoupper(substr($user->name, 0, 1)), ENT_QUOTES, 'UTF-8');
        $fs = max(9, (int)round($size * 0.42));
        return '<div class="' . htmlspecialchars($fallbackClass, ENT_QUOTES, 'UTF-8') . '"'
            . ' style="width:' . $size . 'px;height:' . $size . 'px;font-size:' . $fs . 'px;flex-shrink:0;' . $extraStyle . '">'
            . $initial . '</div>';
    }
}

if (!function_exists('format_description')) {
    /**
     * Format description / notes fields: escape HTML, preserve newlines, make URLs clickable.
     * Input is plain text; output is safe HTML.
     */
    function format_description(?string $text): string
    {
        if (empty($text)) {
            return '';
        }
        $safe = htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
        $safe = nl2br($safe);
        $urlPattern = '#(https?://[^\s"<\]]+)#i';
        $safe = preg_replace($urlPattern, '<a href="$1" target="_blank" rel="noopener" class="desc-link">$1</a>', $safe);
        return $safe;
    }
}
