@extends('layouts.app')

@section('title', 'Account - KarnaCab')

@section('content')
    <div class="page">
        <div class="wrap">
            <section class="dash-hero">
                <div class="eyebrow">Signed in</div>
                <h1>Welcome, {{ auth()->user()->name }}</h1>
                <p>Book a ride with pickup, destination and payment. Quotes and bookings use the Laravel APIs — the same fare_rules engine as the apps.</p>
                <p><a class="btn" href="{{ route('book') }}">Book a ride</a></p>
            </section>

            <div class="stats">
                <article class="tile">
                    <h3>Trips</h3>
                    <p class="muted">@if (is_array($booking ?? null)) Last request {{ $booking['publicRef'] ?? $booking['id'] }} @else Open Book a ride to send a request. @endif</p>
                </article>
                <article class="tile">
                    <h3>Wallet</h3>
                    <p class="muted">Pay from the booking page. Capture is server-side (webhook, wallet, or cash confirm).</p>
                </article>
                <article class="tile">
                    <h3>API</h3>
                    <p class="muted">Connected to <code>{{ $apiBaseUrl }}</code></p>
                </article>
            </div>

            <div style="height: 22px;"></div>
            @livewire('api-health-badge')
        </div>
    </div>
@endsection
