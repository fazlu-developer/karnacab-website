@extends('layouts.app')

@section('title', 'About us - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Company',
        'title' => 'About KarnaCab',
        'lede' => 'KarnaCab is a ride-hailing platform for bike taxi, auto, and cab trips. This website is the public home for customers.',
    ])
    <section class="section">
        <div class="wrap prose">
            <h2>What we are building</h2>
            <p>Customers will request a ride, drivers will accept nearby trips, and operators will run the city from the management console. All of that will sit on one NestJS API.</p>
            <p>Today the website lets you create an account, read how KarnaCab works, and preview a map route from pickup to drop. Booking a captain, wallets, and payments are not enabled yet.</p>
            <h2>Who it is for</h2>
            <p>People who need a reliable way across town, and drivers who want a clear place to work with KarnaCab in a later phase.</p>
            <p><a class="btn" href="{{ route('contact') }}">Talk to us</a></p>
        </div>
    </section>
@endsection
