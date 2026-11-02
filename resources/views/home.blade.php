@extends('layouts.app')

@section('title', $page['seoTitle'] ?? 'KarnaRide')
@section('meta', $page['seoDescription'] ?? ($page['lede'] ?? ''))

@section('content')
    @php
        $catalog = $cms['catalog'] ?? [];
        $services = $catalog['services'] ?? [];
        if ($services === []) {
            $services = $catalog['rideTypes'] ?? [];
        }
        $vehicleTypes = $catalog['vehicleTypes'] ?? [];
        $banners = array_values(array_filter($cms['banners'] ?? [], fn ($banner) => ! empty($banner['imageUrl'])));
        $sections = $page['body']['sections'] ?? [];
        $how = collect($sections)->first(fn ($row) => !empty($row['steps']));
        $offers = is_array($cms['offers'] ?? null) ? $cms['offers'] : [];
        $firstRide = ($offers['customerFirstRideFreeEnabled'] ?? false);
        $driverGift = ($offers['driverWelcomeBonusEnabled'] ?? true);
        $driverGiftRupees = (int) ($offers['driverWelcomeBonusRupees'] ?? 100);
        $coverage = $cms['coverage'] ?? ['states' => 0, 'districts' => 0];
        $heads = $cms['heads'] ?? [];
        $lines = [
            'LOCAL_CAB' => ['शहर के अंदर छोटी यात्रा', 'Short trips inside the city'],
            'ONE_WAY' => ['एक तरफ़ आउटस्टेशन', 'Outstation, one direction'],
            'ROUND_WAY' => ['जाना और वापस आना', 'Go and return on one booking'],
            'AIRPORT' => ['एयरपोर्ट पिकअप और ड्रॉप', 'Airport pickup and drop'],
            'RAILWAY' => ['स्टेशन पिकअप और ड्रॉप', 'Railway station pickup and drop'],
            'PARCEL' => ['पार्सल भेजें', 'Send a package in the city'],
            'MULTI_STOP' => ['कई स्टॉप, एक किराया', 'Several stops, one fare'],
            'RENTAL' => ['घंटों के लिए कैब', 'Hourly cab packages'],
            'TRAVEL' => ['यात्रा पैकेज', 'Published holiday packages'],
            'BULK' => ['एक साथ कई कैब', 'Many cabs for one event'],
            'CORPORATE' => ['कंपनी का खाता', 'Company travel on account'],
            'SCHEDULE' => ['बाद के समय के लिए', 'Book a pickup for later'],
        ];
        $pages = [
            'LOCAL_CAB' => 'local-cab',
            'ONE_WAY' => 'one-way',
            'ROUND_WAY' => 'round-way',
            'AIRPORT' => 'airport',
            'RAILWAY' => 'railway',
            'PARCEL' => 'parcel',
            'MULTI_STOP' => 'multi-stop',
            'RENTAL' => 'rental',
            'TRAVEL' => 'travel',
            'BULK' => 'bulk-booking',
            'CORPORATE' => 'corporate',
            'SCHEDULE' => 'schedule',
        ];
    @endphp

    <section class="hero hero-present">
        <div class="wrap hero-grid">
            <div>
                <div class="eyebrow">{{ $page['eyebrow'] ?? 'KarnaRide' }} · Your Journey, Our Priority.</div>
                <h1>Move Smarter. Travel Better. Go Further.</h1>
                <p class="lede">From everyday rides and airport transfers to outstation journeys, parcel delivery and corporate transportation, KarnaRide brings every journey together in one reliable platform.</p>
                <div class="hero-actions">
                    <a class="btn" href="{{ route('book') }}">Book Your Ride</a>
                    <a class="btn ghost" href="#services">Explore Our Services</a>
                    <a class="btn ghost" href="{{ route('download') }}">Download App</a>
                </div>
                <div class="trust-strip">
                    <span>Verified captains</span>
                    <span>Live tracking</span>
                    <span>Wallet &amp; invoices</span>
                    <span>Same cars in the app</span>
                </div>
            </div>
            <aside class="ride-stage" aria-hidden="true">
                <div class="float-card left">
                    <strong>Live tracking</strong>
                    <span>Driver on the road route</span>
                </div>
                <div class="phone">
                    <div class="phone-bar"><span>9:41</span><span>KarnaRide</span></div>
                    <svg class="phone-map" viewBox="0 0 280 360" role="img">
                        <rect width="280" height="360" fill="#eef3f8"/>
                        <path d="M20 40h240M20 90h240M20 140h240M20 190h240M20 240h240M20 290h240M40 20v320M90 20v320M140 20v320M190 20v320M240 20v320" stroke="#d5e0ec" stroke-width="10"/>
                        <path class="route-line" d="M70 280 C90 220, 120 210, 150 170 S210 120, 220 70" fill="none" stroke="#1b7a32" stroke-width="7" stroke-linecap="round"/>
                        <circle cx="70" cy="280" r="9" fill="#1b7a32"/>
                        <circle cx="220" cy="70" r="9" fill="#e10600"/>
                    </svg>
                    <div class="phone-sheet">
                        <strong>Pickup confirmed</strong>
                        <span>Track the assigned vehicle in the app</span>
                    </div>
                </div>
                <div class="float-card right">
                    <strong>Every service</strong>
                    <span>Rides, parcel, travel, corporate</span>
                </div>
            </aside>
        </div>
    </section>

    @if ($banners !== [])
        <section class="section alt">
            <div class="wrap banner-row">
                @foreach ($banners as $banner)
                    @if (!empty($banner['href']))
                        <a href="{{ $banner['href'] }}" rel="noopener"><img src="{{ $banner['imageUrl'] }}" alt="{{ $banner['title'] }}"></a>
                    @else
                        <img src="{{ $banner['imageUrl'] }}" alt="{{ $banner['title'] }}">
                    @endif
                @endforeach
            </div>
        </section>
    @endif

    <section class="section" id="services">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">12 services · बारह सेवाएं</div>
                <h2>One Platform. Every Journey.</h2>
                <p class="muted lede">Each tile is its own product. Open it to read more, then book in the KarnaRide app.</p>
            </div>
            <div class="service-grid">
                @forelse ($services as $service)
                    @php
                        $key = strtoupper((string) ($service['key'] ?? ''));
                        $slug = $pages[$key] ?? null;
                        $href = $slug && \Illuminate\Support\Facades\Route::has($slug) ? route($slug) : route('download');
                        $pair = $lines[$key] ?? [($service['subtitle'] ?? 'KarnaRide'), ($service['subtitle'] ?? 'Book in the app')];
                        $vehicles = $service['vehicles'] ?? [];
                    @endphp
                    <article class="tile service-tile">
                        @if (!empty($service['imageUrl']))
                            <img class="service-photo" src="{{ $service['imageUrl'] }}" alt="">
                        @endif
                        <h3>{{ $service['title'] ?? 'Service' }}</h3>
                        <p class="hi">{{ $pair[0] }}</p>
                        <p class="muted">{{ $pair[1] }}</p>
                        @if ($vehicles !== [])
                            <p class="vehicle-pills">{{ implode(' · ', array_map(fn ($v) => str_replace('_', ' ', (string) $v), $vehicles)) }}</p>
                        @endif
                        <p class="tile-actions">
                            <a href="{{ $href }}">Open</a>
                            <a href="{{ route('download') }}">Book in app</a>
                        </p>
                    </article>
                @empty
                    <article class="tile">
                        <h3>Rides</h3>
                        <p class="muted">The catalog appears when the KarnaRide database is reachable.</p>
                        <p><a href="{{ route('rides') }}">Explore rides</a></p>
                    </article>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section alt" id="vehicles">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">Vehicles</div>
                <h2>Same cars on the website and in the apps.</h2>
                <p class="muted lede">Bike, Auto, e-rickshaw, mini, sedan, SUV and traveller. Cab photos come from the admin catalog.</p>
            </div>
            <div class="vehicle-grid">
                @forelse ($vehicleTypes as $vehicle)
                    <article class="tile vehicle-tile">
                        @if (!empty($vehicle['imageUrl']))
                            <img src="{{ $vehicle['imageUrl'] }}" alt="{{ $vehicle['title'] ?? 'Vehicle' }}">
                        @endif
                        @php
                            $rawVehicle = strtoupper(str_replace('_', ' ', (string) ($vehicle['title'] ?? $vehicle['key'] ?? 'Vehicle')));
                            $vehicleTitle = [
                                'SUV' => 'SUV',
                                'E RICKSHAW' => 'E-rickshaw',
                            ][$rawVehicle] ?? ucwords(strtolower($rawVehicle));
                        @endphp
                        <h3>{{ $vehicleTitle }}</h3>
                    </article>
                @empty
                    <article class="tile"><h3>Auto</h3></article>
                    <article class="tile"><h3>Cab</h3></article>
                @endforelse
            </div>
        </div>
    </section>

    <section class="section" id="about-karnaride">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">What is KarnaRide</div>
                <h2>Bihar’s ride, parcel and travel platform.</h2>
            </div>
            <div class="grid-3">
                <article class="tile">
                    <h3>For riders</h3>
                    <p class="muted">Compare fares, then book with OTP, live tracking, wallet and invoices in the customer app.</p>
                    <p><a href="{{ route('about') }}">Read about us</a></p>
                </article>
                <article class="tile">
                    <h3>For captains</h3>
                    <p class="muted">Onboard with KYC, go online in your district, and get paid through the driver wallet.</p>
                    <p><a href="{{ route('drive') }}">Drive with us</a></p>
                </article>
                <article class="tile">
                    <h3>For partners</h3>
                    <p class="muted">Fleet owners, district heads and corporate accounts run from the operator console.</p>
                    <p><a href="{{ route('operators') }}">Operator console</a></p>
                </article>
            </div>
        </div>
    </section>

    <section class="section alt">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">How it works</div>
                <h2>{{ $how['heading'] ?? 'Plan here. Book in the app.' }}</h2>
            </div>
            <div class="grid-3">
                @if ($how)
                    @foreach ($how['steps'] as $index => $step)
                        <article class="tile">
                            <div class="step-num">{{ $index + 1 }}</div>
                            <h3>{{ $step['title'] }}</h3>
                            <p class="muted">{{ $step['text'] }}</p>
                        </article>
                    @endforeach
                @else
                    <article class="tile"><div class="step-num">1</div><h3>Choose a service</h3><p class="muted">City, one way, parcel, travel, bulk or corporate.</p></article>
                    <article class="tile"><div class="step-num">2</div><h3>Install the app</h3><p class="muted">Customer app to book. Driver app to go online.</p></article>
                    <article class="tile"><div class="step-num">3</div><h3>Ride with OTP</h3><p class="muted">Live tracking, wallet and invoice stay on the phone.</p></article>
                @endif
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap grid-3">
            <article class="tile">
                <h3>Coverage</h3>
                <p><strong>{{ $coverage['states'] ?: '—' }}</strong> states · <strong>{{ $coverage['districts'] ?: '—' }}</strong> districts</p>
                <p class="muted">Territory heads see only their state or district in the operator console.</p>
                <p><a href="{{ route('cities') }}">Cities</a></p>
            </article>
            <article class="tile">
                <h3>Support · सहायता</h3>
                @forelse ($heads as $head)
                    <p>
                        <strong>{{ $head['role'] === 'STATE_HEAD' ? 'State Head' : 'District Head' }}</strong><br>
                        @if (!empty($head['phone']))<a href="tel:{{ $head['phone'] }}">{{ $head['phone'] }}</a>@endif
                        @if (!empty($head['email'])) · <a href="mailto:{{ $head['email'] }}">{{ $head['email'] }}</a>@endif
                    </p>
                @empty
                    <p class="muted">KarnaRide support answers from the app and the contact page.</p>
                @endforelse
                <p><a href="{{ route('support') }}">Contact support</a></p>
            </article>
            <article class="tile">
                <h3>Offers</h3>
                @if ($firstRide)<p class="muted">New riders: first ride free.</p>@endif
                @if ($driverGift)<p class="muted">New captains: ₹{{ number_format($driverGiftRupees) }} after KYC and the first completed ride.</p>@endif
                @if (! $firstRide && ! $driverGift)<p class="muted">Offers are switched on from Ride Settings.</p>@endif
            </article>
        </div>
    </section>

    <section class="section alt" id="check-fare">
        <div class="wrap">
            <div class="section-head">
                <div class="eyebrow">Fare check</div>
                <h2>See a route before you install.</h2>
                <p class="muted lede">The form only previews a path. Booking, payment and tracking happen in the app.</p>
            </div>
            <div class="fare-panel">
                @include('partials.route-form', ['idPrefix' => 'home-'])
            </div>
        </div>
    </section>

    <section class="section">
        <div class="wrap">
            @include('partials.app-download')
        </div>
    </section>
    <script>
        document.querySelectorAll('[data-banner-carousel]').forEach(function (root) {
            var slides = Array.prototype.slice.call(root.querySelectorAll('.banner-slide'));
            if (slides.length < 2) return;
            var index = 0;
            function show(next) {
                slides[index].classList.remove('is-on');
                index = (next + slides.length) % slides.length;
                slides[index].classList.add('is-on');
            }
            var prev = root.querySelector('[data-banner-prev]');
            var next = root.querySelector('[data-banner-next]');
            if (prev) prev.addEventListener('click', function () { show(index - 1); });
            if (next) next.addEventListener('click', function () { show(index + 1); });
            setInterval(function () { show(index + 1); }, 5000);
        });
    </script>
@endsection
