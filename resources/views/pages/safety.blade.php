@extends('layouts.app')

@section('title', 'Safety - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Trust',
        'title' => 'Safety on every trip',
        'lede' => 'KarnaCab is being built so trip data, driver records, and support live in one platform.',
    ])
    <section class="section">
        <div class="wrap grid-4">
            <article class="tile">
                <div class="icon">🛡</div>
                <h3>Verified drivers</h3>
                <p class="muted">Driver checks will run from the operations console before matching is switched on.</p>
            </article>
            <article class="tile">
                <div class="icon">📍</div>
                <h3>Shareable route</h3>
                <p class="muted">You can already see pickup, drop, and the road path on the public website.</p>
            </article>
            <article class="tile">
                <div class="icon">💬</div>
                <h3>Support</h3>
                <p class="muted">Use Contact for website help. In-trip support will follow live bookings.</p>
            </article>
            <article class="tile">
                <div class="icon">🔒</div>
                <h3>Account</h3>
                <p class="muted">Create a website login to keep your customer shell in one place.</p>
            </article>
        </div>
    </section>
@endsection
