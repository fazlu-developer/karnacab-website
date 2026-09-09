@php
    $site = $cms['site'] ?? [];
    $nav = collect($cms['nav'] ?? []);
    $primary = $nav->firstWhere('group', 'primary')['items'] ?? [];
    $ridesNav = $nav->firstWhere('group', 'rides')['items'] ?? [];
    $servicesNav = $nav->firstWhere('group', 'services')['items'] ?? [];
    $companyNav = $nav->firstWhere('group', 'company')['items'] ?? [];
    $legalNav = $nav->firstWhere('group', 'legal')['items'] ?? [];
    $seoTitle = trim($__env->yieldContent('title', $site['defaultSeoTitle'] ?? 'KarnaCab'));
    $seoDescription = trim($__env->yieldContent('meta', $site['defaultSeoDescription'] ?? 'KarnaCab'));
    $canonical = $site['canonicalHost'] ? rtrim($site['canonicalHost'], '/').request()->getPathInfo() : url()->current();
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    @if (!empty($site['ogImage']))
        <meta property="og:image" content="{{ $site['ogImage'] }}">
    @endif
    <title>{{ $seoTitle }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/karnacab.css') }}">
    @php
        $jsonLd = [
            '@'.'context' => 'https://schema.org',
            '@'.'type' => 'Organization',
            'name' => $site['name'] ?? 'KarnaCab',
            'url' => url('/'),
            'description' => $site['defaultSeoDescription'] ?? '',
            'email' => ! empty($site['contactEmail']) ? $site['contactEmail'] : null,
            'telephone' => ! empty($site['contactPhone']) ? $site['contactPhone'] : null,
        ];
    @endphp
    <script type="application/ld+json">{!! json_encode($jsonLd, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}</script>
    @stack('head')
    @livewireStyles
</head>
<body>
    <a class="skip-link" href="#main">Skip to content</a>
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}">
            <span class="brand-mark" aria-hidden="true">K</span>
            Karna<span>Cab</span> {{ $brandSuffix ?? '' }}
        </a>
        <button class="nav-toggle" type="button" data-nav-toggle aria-expanded="false" aria-label="Open menu">☰</button>
        <nav class="site-nav" data-site-nav>
            <a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'is-active' : '' }}">Home</a>
            @forelse ($primary as $item)
                <a href="{{ $item['path'] }}" class="{{ request()->is(ltrim($item['path'], '/')) ? 'is-active' : '' }}">{{ $item['label'] }}</a>
            @empty
                <a href="{{ route('rides') }}">Rides</a>
                <a href="{{ route('travel') }}">Travel</a>
                <a href="{{ route('parcel') }}">Parcel</a>
                <a href="{{ route('corporate') }}">Corporate</a>
                <a href="{{ route('franchise') }}">Franchise</a>
                <a href="{{ route('contact') }}">Contact</a>
            @endforelse
            <a href="{{ route('book') }}" class="{{ request()->routeIs('book') ? 'is-active' : '' }}">Book</a>
            <a href="{{ route('route') }}" class="{{ request()->routeIs('route') ? 'is-active' : '' }}">Get route</a>
            @auth
                <a href="{{ route('dashboard') }}" class="{{ request()->routeIs('dashboard') ? 'is-active' : '' }}">Account</a>
                <form class="nav-form" method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn" type="submit">Log out</button>
                </form>
            @else
                <a href="{{ route('login') }}" class="{{ request()->routeIs('login') ? 'is-active' : '' }}">Log in</a>
                <a class="btn" href="{{ route('register') }}">Create account</a>
            @endauth
        </nav>
    </header>
    <main id="main">
        @if (session('status'))
            <div class="flash">{{ session('status') }}</div>
        @endif
        @yield('content')
    </main>
    <footer class="site-footer">
        <div class="wrap footer-grid">
            <div>
                <a class="brand" href="{{ route('home') }}">
                    <span class="brand-mark" aria-hidden="true">K</span>
                    Karna<span>Cab</span>
                </a>
                <p>{{ $site['footerBlurb'] ?? $site['tagline'] ?? 'KarnaCab' }}</p>
                @if (!empty($site['contactPhone']))
                    <p>Phone: {{ $site['contactPhone'] }}</p>
                @endif
                @if (!empty($site['contactEmail']))
                    <p>Email: {{ $site['contactEmail'] }}</p>
                @endif
            </div>
            <div>
                <h3>Company</h3>
                <ul>
                    @forelse ($companyNav as $item)
                        <li><a href="{{ $item['path'] }}">{{ $item['label'] }}</a></li>
                    @empty
                        <li><a href="{{ route('about') }}">About us</a></li>
                        <li><a href="{{ route('drive') }}">Drive with us</a></li>
                        <li><a href="{{ route('contact') }}">Contact</a></li>
                    @endforelse
                </ul>
            </div>
            <div>
                <h3>Rides &amp; services</h3>
                <ul>
                    @foreach (array_merge($ridesNav, $servicesNav) as $item)
                        <li><a href="{{ $item['path'] }}">{{ $item['label'] }}</a></li>
                    @endforeach
                    <li><a href="{{ route('book') }}">Book a ride</a></li>
                    <li><a href="{{ route('route') }}">Get route</a></li>
                </ul>
            </div>
            <div>
                <h3>Legal</h3>
                <ul>
                    @forelse ($legalNav as $item)
                        <li><a href="{{ $item['path'] }}">{{ $item['label'] }}</a></li>
                    @empty
                        <li><a href="{{ route('privacy') }}">Privacy</a></li>
                        <li><a href="{{ route('terms') }}">Terms</a></li>
                    @endforelse
                    @auth
                        <li><a href="{{ route('dashboard') }}">Account</a></li>
                    @else
                        <li><a href="{{ route('login') }}">Log in</a></li>
                        <li><a href="{{ route('register') }}">Create account</a></li>
                    @endauth
                </ul>
            </div>
        </div>
        <div class="wrap legal">
            <span>© {{ date('Y') }} {{ $site['name'] ?? 'KarnaCab' }}</span>
            <span>Public website · catalog from KarnaCab API</span>
        </div>
    </footer>
    @stack('scripts')
    <script src="{{ asset('js/karnacab.js') }}"></script>
    @livewireScripts
</body>
</html>
