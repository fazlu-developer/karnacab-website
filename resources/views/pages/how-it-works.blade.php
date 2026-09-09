@extends('layouts.app')

@section('title', 'How it works - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Guide',
        'title' => 'How KarnaCab works',
        'lede' => 'Use the website to plan a path now. A live driver will be part of a later release.',
    ])
    <section class="section">
        <div class="wrap grid-3">
            <article class="tile">
                <div class="step-num">1</div>
                <h3>Enter pickup and drop</h3>
                <p class="muted">Use the Get route page or the form on Home. Add a landmark if the street name is common.</p>
            </article>
            <article class="tile">
                <div class="step-num">2</div>
                <h3>Pick bike, auto, or cab</h3>
                <p class="muted">Bike uses a cycling path when available. Auto and cab use the driving network.</p>
            </article>
            <article class="tile">
                <div class="step-num">3</div>
                <h3>Read the map</h3>
                <p class="muted">You get distance and typical time. No rupee estimate and no driver assignment yet.</p>
            </article>
        </div>
        <div class="wrap" style="margin-top: 28px;">
            <a class="btn" href="{{ route('route') }}">Try get route</a>
        </div>
    </section>
@endsection
