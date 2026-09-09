@php
    $vehicles = $cms['catalog']['vehicleTypes'] ?? [];
@endphp
<section class="section">
    <div class="wrap">
        @include('partials.cms-sections')
        <div class="grid-3">
            @forelse ($vehicles as $vehicle)
                <article class="tile">
                    <h3>{{ $vehicle['title'] ?? $vehicle['label'] ?? $vehicle['key'] }}</h3>
                    <p class="muted">Live catalog vehicle. Open a product page to request a trip.</p>
                    <p><a class="btn" href="{{ route('local-cab') }}">Request a ride</a></p>
                </article>
            @empty
                <article class="tile">
                    <h3>Vehicles</h3>
                    <p class="muted">Vehicle types load from the KarnaCab API.</p>
                </article>
            @endforelse
        </div>
    </div>
</section>
