@extends('layouts.app')

@section('title', 'Terms - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Legal',
        'title' => 'Terms of use',
        'lede' => 'Using the KarnaCab website and the map route tool.',
    ])
    <section class="section">
        <div class="wrap prose">
            <p>The website is provided as a Phase 1 public site. Map routes are estimates from OpenStreetMap data. They are not a promise of a driver, a fare, or a travel time.</p>
            <p>You must not use the route tool to harass anyone or to overload the geocoding services.</p>
            <p>Website accounts can be closed by contacting us. Later product terms will cover trips, cancellations, and payments when those features ship.</p>
        </div>
    </section>
@endsection
