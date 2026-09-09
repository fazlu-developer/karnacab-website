@extends('layouts.app')

@section('title', 'FAQ - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Help',
        'title' => 'Frequently asked questions',
        'lede' => 'Short answers for the public website and the pickup–drop route tool.',
    ])
    <section class="section">
        <div class="wrap faq-list">
            <details open>
                <summary>Can I book a ride on this website?</summary>
                <p class="muted">Not yet. You can preview a map route. Driver matching and payments are not enabled.</p>
            </details>
            <details>
                <summary>How does Get route work?</summary>
                <p class="muted">We look up pickup and drop with OpenStreetMap Nominatim, then draw the path with OSRM. You see distance and typical time, not a fare.</p>
            </details>
            <details>
                <summary>Why was a place not found?</summary>
                <p class="muted">Add the city name or a nearby landmark. Search is biased to India.</p>
            </details>
            <details>
                <summary>Do I need an account?</summary>
                <p class="muted">No for map routes. An account is the customer shell for later product features.</p>
            </details>
            <details>
                <summary>How do I become a driver?</summary>
                <p class="muted">Read Drive with us and send a Contact message. The driver app onboarding is not open yet.</p>
            </details>
        </div>
    </section>
@endsection
