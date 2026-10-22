@extends('layouts.app')

@section('bodyClass', 'is-home')
@section('title', $page['seoTitle'] ?? 'KarnaRide')
@section('meta', $page['seoDescription'] ?? ($page['lede'] ?? ''))

@section('content')
    @php
        $catalog = $cms['catalog'] ?? [];
        $promo = $cms['promo'] ?? [];
        $rideTypes = $catalog['rideTypes'] ?? [];
        $sections = $page['body']['sections'] ?? [];
        $ways = collect($sections)->first(fn ($row) => !empty($row['cards']) || (empty($row['steps']) && empty($row['ctaHref']) && !empty($row['heading'])));
        $cta = collect($sections)->first(fn ($row) => !empty($row['ctaHref']));
        $how = collect($sections)->first(fn ($row) => !empty($row['steps']));
        $offers = is_array($cms['offers'] ?? null) ? $cms['offers'] : [];
        $firstRide = ($offers['customerFirstRideFreeEnabled'] ?? false);
        $driverGift = ($offers['driverWelcomeBonusEnabled'] ?? true);
        $driverGiftRupees = (int) ($offers['driverWelcomeBonusRupees'] ?? 100);
        $pages = collect($cms['pages'] ?? []);
        $cardSlots = is_array($ways) && ! empty($ways['cards']) ? $ways['cards'] : [
            ['slug' => 'rides', 'label' => 'Ride'],
            ['slug' => 'travel', 'label' => 'Travel'],
            ['slug' => 'parcel', 'label' => 'Parcel'],
        ];
        $cards = [];
        foreach ($cardSlots as $slot) {
            if (! is_array($slot)) {
                continue;
            }
            $slug = (string) ($slot['slug'] ?? '');
            $source = $slug !== '' ? $pages->first(fn ($row) => ($row['slug'] ?? '') === $slug) : null;
            $label = $slot['label'] ?? ($source['navLabel'] ?? ($source['title'] ?? 'Service'));
            $image = trim((string) ($slot['image'] ?? ($source['imageUrl'] ?? '')));
            if ($image === '') {
                $image = asset('images/home/'.($slug !== '' ? $slug : 'rides').'.svg');
            } elseif (! str_starts_with($image, 'http') && ! str_starts_with($image, '//')) {
                $image = str_starts_with($image, '/uploads/')
                    ? rtrim((string) config('karnacab.admin_url'), '/').$image
                    : asset(ltrim($image, '/'));
            }
            $cards[] = [
                'title' => $label,
                'text' => $slot['text'] ?? ($source['lede'] ?? ''),
                'href' => $slot['href'] ?? ($source['path'] ?? ($slug !== '' ? '/'.$slug : route('rides'))),
                'image' => $image,
            ];
        }
        $headline = trim((string) ($page['title'] ?? 'A little closer to everywhere.'));
        $headlineLead = $headline;
        $headlineGold = '';
        if (preg_match('/^(.*?)\s+to\s+(everywhere\.?)$/i', $headline, $headlineParts)) {
            $headlineLead = trim($headlineParts[1]);
            $headlineGold = $headlineParts[2];
        }
        $waysHeading = is_array($ways) ? ($ways['heading'] ?? 'Your day has places to go. We have a way.') : 'Your day has places to go. We have a way.';
        $waysText = is_array($ways) ? ($ways['text'] ?? 'Ride, travel and parcel stay in sync with the live catalog.') : 'Ride, travel and parcel stay in sync with the live catalog.';
    @endphp

    <section class="home-hero">
        <div class="wrap">
            <div class="home-hero-panel">
                <div class="home-hero-copy">
                    <div class="home-kicker">{{ $page['eyebrow'] ?? 'For wherever life takes you' }}</div>
                    <h1>
                        {{ $headlineLead }}
                        @if ($headlineGold !== '')
                            <br>to <span>{{ $headlineGold }}</span>
                        @endif
                    </h1>
                    <p>{{ $page['lede'] }}</p>
                </div>
                <div class="home-hero-photo">
                    <img src="{{ asset('images/home/hero-road.svg') }}" alt="A KarnaRide car on an open road">
                    <div class="home-hero-pill">{{ count($rideTypes) ?: 'Live' }} ride products</div>
                </div>
                <div class="home-book">
                    @include('partials.route-form', ['idPrefix' => 'home-', 'pickupLabel' => 'From', 'dropLabel' => 'To'])
                </div>
            </div>
        </div>
    </section>

    <div class="home-gold" aria-hidden="true"></div>

    <section class="home-ways" id="rides">
        <div class="wrap">
            <div class="home-ways-head">
                <h2>{{ $waysHeading }}</h2>
                <p>{{ $waysText }}</p>
            </div>
            <div class="home-cards">
                @foreach ($cards as $card)
                    <article class="home-card">
                        <img src="{{ $card['image'] }}" alt="">
                        <h3>{{ $card['title'] }}</h3>
                        <p>{{ $card['text'] }}</p>
                        <a href="{{ $card['href'] }}">Open {{ $card['title'] }}</a>
                    </article>
                @endforeach
            </div>
            @if (count($rideTypes) > 0)
                <div class="home-products">
                    @foreach ($rideTypes as $type)
                        @php
                            $productKey = is_array($type) ? ($type['key'] ?? $type['title'] ?? '') : '';
                            $slug = strtolower(str_replace('_', '-', (string) $productKey));
                            $href = \Illuminate\Support\Facades\Route::has($slug) ? route($slug) : route('rides');
                            $label = is_array($type) ? ($type['title'] ?? $type['label'] ?? $type['key'] ?? 'Ride') : 'Ride';
                        @endphp
                        <a href="{{ $href }}">{{ $label }}</a>
                    @endforeach
                </div>
            @endif
        </div>
    </section>

    @if ($firstRide || $driverGift)
        <section class="section" id="launch-offers">
            <div class="wrap">
                <div class="section-head">
                    <div class="eyebrow">KarnaRide offers</div>
                    <h2>Launch benefits you can use today.</h2>
                </div>
                <div class="grid-3">
                    @if ($firstRide)
                        <article class="tile">
                            <h3>First ride free</h3>
                            <p class="muted">New customer registrations get their first completed ride at ₹0 when this offer is on in admin Ride Settings.</p>
                        </article>
                    @endif
                    @if ($driverGift)
                        <article class="tile">
                            <h3>₹{{ number_format($driverGiftRupees) }} driver joining credit</h3>
                            <p class="muted">Paid after KYC, vehicle verification, account approval and the first completed ride — not on registration.</p>
                        </article>
                    @endif
                </div>
            </div>
        </section>
    @endif

    <section class="section" id="about-karnaride">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">What is KarnaRide</div>
                <h2>Bihar’s ride, parcel and travel platform.</h2>
                <p class="muted lede">KarnaRide connects passengers with verified captains for bike, auto, mini, sedan, SUV and traveller trips, plus local parcel and published travel packages.</p>
            </div>
            @if (!empty($promo['title']))
                <p><a href="{{ $promo['href'] ?? route('railway') }}">{{ $promo['title'] }} — {{ $promo['cta'] ?? 'Learn more' }}</a></p>
            @endif
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

    <section class="section">
        <div class="wrap">
            @include('partials.app-download')
        </div>
    </section>
@endsection
