@php
    $districts = $cms['catalog']['districts'] ?? [];
    $rentals = $cms['catalog']['rentalPackages'] ?? [];
    $quote = session('quote');
@endphp
<section class="section">
    <div class="wrap contact-layout">
        <section class="card">
            @include('partials.cms-sections')
            @if (($page['productKey'] ?? null) === 'RENTAL' && count($rentals))
                <h2>Rental packages</h2>
                <p class="muted">Hours and included KM from fare rules.</p>
                <ul>
                    @foreach ($rentals as $pkg)
                        <li>{{ $pkg['hours'] ?? $pkg['label'] ?? 'Package' }} hours
                            @if (!empty($pkg['includedKm'])) — {{ $pkg['includedKm'] }} km included @endif
                        </li>
                    @endforeach
                </ul>
            @endif
            @if (!empty($page['productKey']))
                @include('partials.quote-form')
            @endif
            @if (!empty($page['leadType']))
                @include('partials.lead-form', ['leadType' => $page['leadType']])
            @endif
        </section>
        <aside class="tile">
            <h3>Continue in the app</h3>
            <p class="muted">Check fares on the website. Confirm the trip in the customer app.</p>
            <p><a class="btn dark" href="{{ route('book', array_filter(['product' => $page['productKey'] ?? null])) }}">Check fares</a></p>
            <p><a href="{{ route('download') }}">Download apps</a></p>
        </aside>
    </div>
</section>
