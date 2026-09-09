@extends('layouts.app')

@section('title', 'Rides - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Services',
        'title' => 'Bike, auto, and cab',
        'lede' => 'The same KarnaCab website covers every ride type. Choose how you want to travel, then preview pickup to drop.',
    ])
    <section class="section">
        <div class="wrap grid-3">
            <article class="tile">
                <div class="icon">🏍</div>
                <h3>Bike taxi</h3>
                <p class="muted">Solo trips through traffic. Best when you are travelling light and need to move quickly.</p>
                <p><a class="btn" href="{{ route('route', ['vehicle' => 'bike']) }}">Get a bike route</a></p>
            </article>
            <article class="tile">
                <div class="icon">🛺</div>
                <h3>Auto</h3>
                <p class="muted">Short and mid-range city hops. A familiar three-wheeler for everyday errands.</p>
                <p><a class="btn" href="{{ route('route', ['vehicle' => 'auto']) }}">Get an auto route</a></p>
            </article>
            <article class="tile">
                <div class="icon">🚗</div>
                <h3>Cab</h3>
                <p class="muted">Car trips for families, luggage, late nights, or longer city routes.</p>
                <p><a class="btn" href="{{ route('route', ['vehicle' => 'cab']) }}">Get a cab route</a></p>
            </article>
        </div>
    </section>
@endsection
