@extends('layouts.portal')
@section('title', 'Dashboard')

@section('content')
<div class="page-hd">
    <h2>Welcome, {{ Auth::user()->name }}</h2>
    <p>Here's an overview of your account with {{ config('app.name') }}.</p>
</div>

<div class="g2">
    <div class="card">
        <div class="card-hd"><h3>Content for Review</h3></div>
        <div class="card-bd">
            <p class="text-sm text-muted" style="margin-bottom:14px;">
                Review content the team has finished, approve it, or request changes.
            </p>
            <a href="{{ route('client.content.index') }}" class="btn btn-primary btn-sm">View Content →</a>
        </div>
    </div>

    <div class="card">
        <div class="card-hd"><h3>Performance Reports</h3></div>
        <div class="card-bd">
            <p class="text-sm text-muted" style="margin-bottom:14px;">
                View the Reach, Engagement, and Video Views reports we share with you, along with our plan for the next period.
            </p>
            <a href="{{ route('client.reports.index') }}" class="btn btn-primary btn-sm">View Reports →</a>
        </div>
    </div>

    <div class="card">
        <div class="card-hd"><h3>Your Brand Kit</h3></div>
        <div class="card-bd">
            @php $client = Auth::user()->client; @endphp
            @if($client)
            <div class="detail-row">
                <span class="detail-label">Client ID</span>
                <span class="detail-value fw700">#{{ $client->id }}</span>
                <span class="detail-label">Package</span>
                <span class="detail-value">{{ $client->package ?: '—' }}</span>
                <span class="detail-label">Status</span>
                <span class="detail-value"><span class="badge badge-{{ $client->status }}">{{ $client->status }}</span></span>
            </div>
            <p class="text-faint text-xs" style="margin-top:12px;">Quote your Client ID when contacting us via WhatsApp so we can find your account right away.</p>
            @else
            <p class="text-muted text-sm">No client profile linked to your account yet.</p>
            @endif
        </div>
    </div>
</div>
@endsection
