@extends('layouts.app')

@section('title', 'Get route - KarnaCab')

@push('head')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" integrity="sha256-p4NxAoJBhIIN+hmNHrzRCf9tD/miZyoHS5obTRR9BMY=" crossorigin="">
@endpush

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Pickup to drop',
        'title' => 'Get your route',
        'lede' => 'Enter pickup and drop. Destination search is Google Places via Laravel.',
    ])

    <section class="section">
        <div class="wrap route-layout">
            <div class="card">
                @include('partials.route-form', ['idPrefix' => 'route-'])
                @if ($error)
                    <p class="error">{{ $error }}</p>
                @endif
                @if ($distance_km)
                    <div class="route-summary">
                        <div><strong>{{ $distance_km }} km</strong><span class="muted">Distance</span></div>
                        <div><strong>{{ $duration_min }} min</strong><span class="muted">Typical time</span></div>
                        <div><strong>{{ ucfirst($vehicle) }}</strong><span class="muted">Vehicle</span></div>
                    </div>
                @endif
            </div>
            <div
                id="route-map"
                class="route-map"
                data-from='@json($from)'
                data-to='@json($to)'
                data-geometry='@json($geometry)'
            ></div>
        </div>
        @if ($from && $to)
            <div class="wrap" style="margin-top: 18px;">
                <p class="muted"><strong>Pickup:</strong> {{ $from['label'] }}</p>
                <p class="muted"><strong>Drop:</strong> {{ $to['label'] }}</p>
            </div>
        @endif
    </section>
@endsection

@push('scripts')
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js" integrity="sha256-20nQCchB9co0qIjJZRGuk2/Z9VM+kNiyxNV1lvTlZBo=" crossorigin=""></script>
@endpush
