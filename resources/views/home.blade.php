@extends('layouts.app')

@section('title', $page['seoTitle'] ?? 'KarnaCab')
@section('meta', $page['seoDescription'] ?? ($page['lede'] ?? ''))

@section('content')
    @php
        $catalog = $cms['catalog'] ?? [];
        $promo = $cms['promo'] ?? [];
        $rideTypes = $catalog['rideTypes'] ?? [];
        $vehicleTypes = $catalog['vehicleTypes'] ?? [];
        $packages = $catalog['packages'] ?? [];
        $sections = $page['body']['sections'] ?? [];
        $cta = collect($sections)->first(fn ($row) => !empty($row['ctaHref']));
        $how = collect($sections)->first(fn ($row) => !empty($row['steps']));
    @endphp
    <section class="hero">
        <div class="wrap hero-grid">
            <div>
                <div class="eyebrow">{{ $page['eyebrow'] ?? ($cms['site']['tagline'] ?? 'KarnaCab') }}</div>
                <h1>{{ $page['title'] }}</h1>
                <p class="lede">{{ $page['lede'] }}</p>
                <div class="hero-actions">
                    <a class="btn" href="{{ route('book') }}">Book a ride</a>
                    <a class="btn ghost" href="{{ route('rides') }}">See ride types</a>
                </div>
                @if (!empty($promo['title']))
                    <p class="muted"><a href="{{ $promo['href'] ?? route('railway') }}">{{ $promo['title'] }} — {{ $promo['cta'] ?? 'Learn more' }}</a></p>
                @endif
                <div class="hero-meta">
                    <div><strong>{{ count($rideTypes) ?: '—' }}</strong> ride products</div>
                    <div><strong>{{ count($vehicleTypes) ?: '—' }}</strong> vehicle types</div>
                    <div><strong>{{ count($packages) }}</strong> travel packages</div>
                </div>
            </div>
            <aside class="hero-panel" aria-label="Get route">
                @include('partials.route-form', ['idPrefix' => 'home-'])
            </aside>
        </div>
    </section>

    <section class="section" id="rides">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">Ride options</div>
                <h2>Choose how you move.</h2>
                <p class="muted lede">Products below come from the live ride catalog. Fares are never printed here.</p>
            </div>
            <div class="grid-3">
                @forelse ($rideTypes as $type)
                    @php
                        $productKey = is_array($type) ? ($type['key'] ?? $type['title'] ?? '') : '';
                        $slug = strtolower(str_replace('_', '-', (string) $productKey));
                        $href = \Illuminate\Support\Facades\Route::has($slug) ? route($slug) : route('rides');
                        $label = is_array($type) ? ($type['title'] ?? $type['label'] ?? $type['key'] ?? 'Ride') : 'Ride';
                    @endphp
                    <article class="tile">
                        <h3>{{ $label }}</h3>
                        <p class="muted">Request this product or preview a map route.</p>
                        <p><a href="{{ $href }}">Open {{ $label }}</a></p>
                    </article>
                @empty
                    <article class="tile">
                        <h3>Rides</h3>
                        <p class="muted">Catalog will appear when the KarnaCab API is reachable.</p>
                        <p><a href="{{ route('rides') }}">Explore rides</a></p>
                    </article>
                @endforelse
            </div>
        </div>
    </section>

    @if ($how)
        <section class="section alt">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">How it works</div>
                    <h2>{{ $how['heading'] }}</h2>
                    <p class="muted lede">{{ $how['text'] ?? '' }}</p>
                </div>
                <div class="grid-3">
                    @foreach ($how['steps'] as $index => $step)
                        <article class="tile">
                            <div class="step-num">{{ $index + 1 }}</div>
                            <h3>{{ $step['title'] }}</h3>
                            <p class="muted">{{ $step['text'] }}</p>
                        </article>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    @if ($cta)
        <div class="wrap">
            <section class="cta-band">
                <div>
                    <h2>{{ $cta['heading'] }}</h2>
                    <p>{{ $cta['text'] ?? '' }}</p>
                </div>
                <a class="btn" href="{{ $cta['ctaHref'] }}">{{ $cta['ctaLabel'] ?? 'Continue' }}</a>
            </section>
        </div>
    @endif
@endsection
