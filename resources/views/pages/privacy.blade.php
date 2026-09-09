@extends('layouts.app')

@section('title', 'Privacy - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Legal',
        'title' => 'Privacy',
        'lede' => 'How the public website handles the information you type.',
    ])
    <section class="section">
        <div class="wrap prose">
            <p>Account registration stores your name, email, and password hash in the website database for session login.</p>
            <p>Pickup and drop text is sent to OpenStreetMap Nominatim and OSRM so we can draw a route. We do not store those searches as trip bookings.</p>
            <p>Contact messages are written to application logs so the team can reply. Do not send payment card numbers.</p>
            <p>This page will grow when live trips, wallets, and the mobile apps start collecting location in production.</p>
        </div>
    </section>
@endsection
