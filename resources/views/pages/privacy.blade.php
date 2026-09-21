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
            <p>Pickup and destination searches use Google Places so we can draw a route and estimate fares. We do not store those searches as trip bookings unless you enquire through a form.</p>
            <p>Contact and partner forms are stored as website enquiries so the team can reply. Do not send payment card numbers.</p>
            <p>Customer and driver accounts, location while on a trip, wallet and invoices are handled in the mobile apps, not by a website login.</p>
        </div>
    </section>
@endsection
