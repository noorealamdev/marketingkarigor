<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 13px; color: #1a1a2e; background: #fff; }
    .page { padding: 48px 52px; position: relative; }

    /* Watermark */
    .watermark {
        position: absolute; top: 260px; left: 50%; width: 500px; margin-left: -250px;
        text-align: center; font-size: 72px; font-weight: 800; text-transform: uppercase;
        letter-spacing: 0.05em; opacity: 0.07; transform: rotate(-28deg); z-index: 0;
    }
    .watermark.paid { color: #16a34a; }
    .watermark.overdue { color: #dc2626; }

    /* Header */
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 32px; border-bottom: 3px solid #b8860b; padding-bottom: 22px; position: relative; z-index: 1; }
    .brand h1 { font-size: 26px; font-weight: 800; letter-spacing: -0.04em; color: #1a1a2e; }
    .brand h1 span { color: #b8860b; }
    .brand img { max-height: 46px; max-width: 220px; }
    .brand .issued-by { font-size: 10.5px; color: #9ca3af; margin-top: 6px; letter-spacing: 0.02em; }
    .invoice-label { text-align: right; }
    .invoice-label .doc-type { font-size: 11px; font-weight: 700; letter-spacing: 0.12em; text-transform: uppercase; color: #9ca3af; }
    .invoice-label .number { font-size: 22px; font-weight: 800; color: #b8860b; letter-spacing: -0.02em; margin-top: 2px; }
    .invoice-label .status-badge { display: inline-block; padding: 3px 12px; border-radius: 20px; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.06em; margin-top: 8px; }
    .status-paid     { background: #dcfce7; color: #166534; }
    .status-sent     { background: #dbeafe; color: #1e40af; }
    .status-draft    { background: #f3f4f6; color: #374151; }
    .status-overdue  { background: #fee2e2; color: #991b1b; }
    .status-cancelled{ background: #f3f4f6; color: #6b7280; }

    /* Meta grid — table layout: dompdf's flexbox support collapses 3-column flex:1 grids */
    .meta-grid { display: table; width: 100%; table-layout: fixed; margin-bottom: 32px; position: relative; z-index: 1; }
    .meta-col { display: table-cell; vertical-align: top; width: 33.33%; }
    .meta-col + .meta-col { border-left: 1px solid #e5e7eb; padding-left: 28px; }
    .meta-label { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.08em; color: #9ca3af; margin-bottom: 4px; }
    .meta-value { font-size: 13px; color: #1a1a2e; font-weight: 500; }
    .meta-value.big { font-size: 24px; font-weight: 800; color: #b8860b; letter-spacing: -0.03em; }

    /* Line items table */
    .table-wrap { margin-bottom: 22px; position: relative; z-index: 1; }
    table { width: 100%; border-collapse: collapse; }
    thead th { background: #fdf6e3; padding: 10px 14px; text-align: left; font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #92720c; border-bottom: 2px solid #f1e2b3; }
    tbody td { padding: 13px 14px; border-bottom: 1px solid #f3f4f6; font-size: 13px; color: #374151; vertical-align: top; }
    tbody tr:last-child td { border-bottom: none; }
    .text-right { text-align: right; }

    /* Total row */
    .total-row { margin-top: 8px; display: flex; justify-content: flex-end; position: relative; z-index: 1; }
    .total-box { border: 2px solid #b8860b; border-radius: 8px; padding: 14px 22px; min-width: 260px; }
    .total-line { display: flex; justify-content: space-between; align-items: center; padding: 4px 0; font-size: 15px; font-weight: 800; color: #1a1a2e; }
    .total-line .amount { color: #b8860b; }
    .amount-words { margin-top: 10px; text-align: right; font-size: 10.5px; color: #6b7280; font-style: italic; max-width: 420px; margin-left: auto; }

    /* Payment section */
    .payment-box { margin-top: 24px; border: 1px solid #e5e7eb; border-radius: 10px; padding: 18px 20px; position: relative; z-index: 1; }
    .payment-box h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #92720c; margin-bottom: 10px; }
    .coming-soon-badge {
        display: inline-flex; align-items: center; gap: 6px; background: #fdf6e3; color: #92720c;
        border: 1px solid #f1e2b3; border-radius: 20px; padding: 4px 12px; font-size: 10.5px; font-weight: 600; margin-bottom: 10px;
    }
    .payment-methods { font-size: 11px; color: #6b7280; margin-bottom: 12px; }
    .payment-methods span { display: inline-block; background: #f3f4f6; border-radius: 4px; padding: 2px 8px; margin-right: 4px; }
    .pay-btn {
        display: inline-block; background: #25d366; color: #06210f; text-decoration: none;
        font-weight: 700; font-size: 12px; padding: 9px 18px; border-radius: 6px;
    }
    .paid-banner {
        margin-top: 24px; background: #f0fdf4; border: 1px solid #bbf7d0; border-radius: 10px;
        padding: 14px 20px; font-size: 13px; font-weight: 700; color: #166534; position: relative; z-index: 1;
    }

    /* Notes */
    .notes-box { background: #fdf6e3; border-left: 4px solid #b8860b; padding: 14px 18px; border-radius: 0 6px 6px 0; margin-top: 22px; position: relative; z-index: 1; }
    .notes-box h4 { font-size: 11px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.07em; color: #92720c; margin-bottom: 6px; }
    .notes-box p { font-size: 12px; color: #374151; line-height: 1.6; }
    .notes-box p a.desc-link { color: #b8860b; }

    /* Footer */
    .footer { border-top: 1px solid #e5e7eb; padding-top: 16px; margin-top: 36px; position: relative; z-index: 1; }
    .footer-row { display: flex; justify-content: space-between; align-items: flex-end; }
    .footer-left { font-size: 11px; color: #9ca3af; line-height: 1.7; }
    .footer-right { font-size: 11px; color: #9ca3af; text-align: right; }
    .thank-you { font-size: 14px; font-weight: 700; color: #b8860b; }
    .legal-note { margin-top: 14px; font-size: 9.5px; color: #b0b4bd; line-height: 1.6; }
    .legal-note a { color: #b0b4bd; }

    @page { margin: 0; }
</style>
</head>
<body>
<div class="page">

    @if($invoice->status === 'paid')
        <div class="watermark paid">Paid</div>
    @elseif($invoice->status === 'sent' && $invoice->due_date?->isPast())
        <div class="watermark overdue">Overdue</div>
    @endif

    {{-- Header --}}
    <div class="header">
        <div class="brand">
            @if($logoPath = app_logo_path())
                <img src="{{ $logoPath }}" alt="{{ config('app.name') }}">
            @else
                <h1>{{ config('app.name') }}</h1>
            @endif
            @if($wa = whatsapp_link())
            <div class="issued-by">WhatsApp: {{ whatsapp_display() }} &middot; {{ preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')) }}</div>
            @endif
        </div>
        <div class="invoice-label">
            <div class="doc-type">Invoice</div>
            <div class="number">{{ $invoice->invoice_number }}</div>
            <div>
                <span class="status-badge status-{{ $invoice->status }}">{{ $invoice->status }}</span>
            </div>
        </div>
    </div>

    {{-- Meta info --}}
    <div class="meta-grid">
        <div class="meta-col">
            <div class="meta-label">Billed To</div>
            @if($invoice->client)
            <div class="meta-value" style="font-weight:700;font-size:14px;">{{ $invoice->client->name }}</div>
            @if($invoice->client->company)
            <div class="meta-value">{{ $invoice->client->company }}</div>
            @endif
            @if($invoice->client->email)
            <div class="meta-value" style="color:#6b7280;font-size:12px;">{{ $invoice->client->email }}</div>
            @endif
            @if($invoice->client->phone)
            <div class="meta-value" style="color:#6b7280;font-size:12px;">{{ $invoice->client->phone }}</div>
            @endif
            @else
            <div class="meta-value">—</div>
            @endif
        </div>
        <div class="meta-col">
            <div class="meta-label">Project</div>
            <div class="meta-value">{{ $invoice->project?->name ?? '—' }}</div>
            <div style="margin-top:14px;">
                <div class="meta-label">Issue Date</div>
                <div class="meta-value">{{ $invoice->issued_date?->format('F d, Y') ?? '—' }}</div>
            </div>
            <div style="margin-top:10px;">
                <div class="meta-label">Due Date</div>
                <div class="meta-value" style="{{ $invoice->due_date?->isPast() && $invoice->status=='sent' ? 'color:#dc2626;font-weight:700;' : '' }}">
                    {{ $invoice->due_date?->format('F d, Y') ?? '—' }}
                    @if($invoice->due_date?->isPast() && $invoice->status=='sent') (Overdue) @endif
                </div>
            </div>
        </div>
        <div class="meta-col" style="text-align:right;">
            <div class="meta-label">Total Amount</div>
            <div class="meta-value big">Tk {{ number_format($invoice->amount, 2) }}</div>
        </div>
    </div>

    {{-- Line Items --}}
    <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th style="width:60%;">Description</th>
                    <th class="text-right">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>
                        <div style="font-weight:600;color:#1a1a2e;">
                            {{ $invoice->project?->name ?? 'Professional Services' }}
                        </div>
                        @if($invoice->project?->description)
                        <div style="font-size:11px;color:#9ca3af;margin-top:3px;">{{ $invoice->project->description }}</div>
                        @endif
                    </td>
                    <td class="text-right" style="font-weight:700;color:#1a1a2e;">Tk {{ number_format($invoice->amount, 2) }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Total --}}
    <div class="total-row">
        <div>
            <div class="total-box">
                <div class="total-line">
                    <span>Total Due</span>
                    <span class="amount">Tk {{ number_format($invoice->amount, 2) }}</span>
                </div>
            </div>
            <div class="amount-words">In Words: {{ amount_in_words($invoice->amount) }}</div>
        </div>
    </div>

    {{-- Payment --}}
    @if($invoice->status === 'paid')
    <div class="paid-banner">✓ Payment Received — This invoice has been paid in full.</div>
    @else
    <div class="payment-box">
        <h4>Payment</h4>
        <div class="coming-soon-badge">Secure Online Payment via SSLCommerz — Coming Soon</div>
        <div class="payment-methods">Accepted soon: <span>bKash</span><span>Nagad</span><span>Rocket</span><span>Visa</span><span>Mastercard</span></div>
        @if($wa)
        <a href="{{ whatsapp_link('আসসালামু আলাইকুম, আমি ইনভয়েস #' . $invoice->invoice_number . ' এর পেমেন্ট সম্পন্ন করতে চাই।') }}" class="pay-btn">Confirm Payment via WhatsApp</a>
        @endif
    </div>
    @endif

    {{-- Notes --}}
    @if($invoice->notes)
    <div class="notes-box">
        <h4>Notes</h4>
        <p>{!! format_description($invoice->notes) !!}</p>
    </div>
    @endif

    {{-- Footer --}}
    <div class="footer">
        <div class="footer-row">
            <div class="footer-left">
                <div class="thank-you">Thank you for your business!</div>
                <div style="margin-top:4px;">Generated by {{ config('app.name') }} &middot; {{ now()->format('F d, Y') }}</div>
            </div>
            <div class="footer-right">
                <div>Questions? Contact us at</div>
                <div style="color:#b8860b;font-weight:600;">{{ preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')) }}</div>
            </div>
        </div>
        <div class="legal-note">
            This is a computer-generated invoice from {{ config('app.name') }} and is valid without a signature. Use of this invoice is subject to our Terms &amp; Conditions ({{ preg_replace('#^https?://#', '', rtrim(config('app.url'), '/')) }}/terms-and-conditions).
        </div>
    </div>

</div>
</body>
</html>
