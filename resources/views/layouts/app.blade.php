@php
    $site = $cms['site'] ?? [];
    $nav = collect($cms['nav'] ?? []);
    $primary = $nav->firstWhere('group', 'primary')['items'] ?? [];
    $ridesNav = $nav->firstWhere('group', 'rides')['items'] ?? [];
    $servicesNav = $nav->firstWhere('group', 'services')['items'] ?? [];
    $companyNav = $nav->firstWhere('group', 'company')['items'] ?? [];
    $legalNav = $nav->firstWhere('group', 'legal')['items'] ?? [];
    $brandName = $site['name'] ?? 'KarnaRide';
    $seoTitle = trim(html_entity_decode($__env->yieldContent('title', $site['defaultSeoTitle'] ?? 'KarnaRide — rides, parcel and travel'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $seoDescription = trim(html_entity_decode($__env->yieldContent('meta', $site['defaultSeoDescription'] ?? 'KarnaRide rides, parcel and travel in Bihar.'), ENT_QUOTES | ENT_HTML5, 'UTF-8'));
    $canonicalHost = rtrim((string) ($site['canonicalHost'] ?? 'https://karnaride.in'), '/');
    $canonical = $canonicalHost !== '' ? $canonicalHost.request()->getPathInfo() : url()->current();
    $favicon = $site['faviconUrl'] ?: asset('favicon-32.png');
    $ogImage = $site['ogImage'] ?: ($site['logoUrl'] ?: $favicon);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#1B3A2F">
    <meta name="application-name" content="{{ $brandName }}">
    <meta name="description" content="{{ $seoDescription }}">
    <link rel="canonical" href="{{ $canonical }}">
    <meta property="og:site_name" content="{{ $brandName }}">
    <meta property="og:title" content="{{ $seoTitle }}">
    <meta property="og:description" content="{{ $seoDescription }}">
    <meta property="og:type" content="website">
    <meta property="og:url" content="{{ $canonical }}">
    <meta property="og:image" content="{{ $ogImage }}">
    <meta name="twitter:card" content="summary_large_image">
    <meta name="twitter:title" content="{{ $seoTitle }}">
    <meta name="twitter:description" content="{{ $seoDescription }}">
    <meta name="twitter:image" content="{{ $ogImage }}">
    <title>{{ $seoTitle }}</title>
    <link rel="icon" href="{{ $site['faviconUrl'] ?: asset('favicon.ico') }}" sizes="any">
    <link rel="icon" type="image/png" href="{{ $favicon }}">
    <link rel="apple-touch-icon" href="{{ $favicon }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/karnacab.css') }}">
    @php
        $jsonLd = [
            '@'.'context' => 'https://schema.org',
            '@'.'type' => 'Organization',
            'name' => $brandName,
            'url' => $canonicalHost !== '' ? $canonicalHost : url('/'),
            'logo' => $site['logoUrl'] ?: $favicon,
            'image' => $ogImage,
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
            <img class="brand-lockup-img" src="{{ $site['logoUrl'] ?: asset('branding/karnacab-logo-full.png') }}" alt="{{ $site['name'] ?? 'KarnaRide' }}">
            @if (!empty($brandSuffix))
                <span class="brand-suffix">{{ $brandSuffix }}</span>
            @endif
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
            <a href="{{ route('about') }}" class="{{ request()->routeIs('about') ? 'is-active' : '' }}">About</a>
            <a href="{{ route('book') }}" class="{{ request()->routeIs('book') ? 'is-active' : '' }}">Check fares</a>
            <a href="{{ route('cities') }}" class="{{ request()->routeIs('cities') ? 'is-active' : '' }}">Cities</a>
            <a href="{{ route('route') }}" class="{{ request()->routeIs('route') ? 'is-active' : '' }}">Get route</a>
            <a class="btn" href="{{ route('download') }}">Get the apps</a>
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
                    <img class="brand-lockup-img" src="{{ $site['logoUrl'] ?: asset('branding/karnacab-logo-full.png') }}" alt="{{ $site['name'] ?? 'KarnaRide' }}">
                </a>
                <p>{{ $site['footerBlurb'] ?? $site['tagline'] ?? 'KarnaRide' }}</p>
                @if (!empty($site['address']))
                    <p>{{ $site['address'] }}</p>
                @endif
                @if (!empty($site['contactPhone']))
                    <p>Phone: {{ $site['contactPhone'] }}</p>
                @endif
                @if (!empty($site['contactEmail']))
                    <p>Email: {{ $site['contactEmail'] }}</p>
                @endif
                @if (!empty($site['whatsappUrl']))
                    <p><a href="{{ $site['whatsappUrl'] }}" rel="noopener">WhatsApp</a></p>
                @endif
                @if (!empty($site['facebookUrl']) || !empty($site['instagramUrl']) || !empty($site['youtubeUrl']))
                    <p>
                        @if (!empty($site['facebookUrl']))<a href="{{ $site['facebookUrl'] }}" rel="noopener">Facebook</a> @endif
                        @if (!empty($site['instagramUrl']))<a href="{{ $site['instagramUrl'] }}" rel="noopener">Instagram</a> @endif
                        @if (!empty($site['youtubeUrl']))<a href="{{ $site['youtubeUrl'] }}" rel="noopener">YouTube</a> @endif
                    </p>
                @endif
                @if (!empty($site['mapEmbed']))
                    <div class="footer-map">{!! $site['mapEmbed'] !!}</div>
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
                    <li><a href="{{ route('book') }}">Check fares</a></li>
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
                    <li><a href="{{ url('/privacy-policy') }}">Privacy Policy</a></li>
                    <li><a href="{{ route('delete-account') }}">Delete Account</a></li>
                    <li><a href="{{ route('download') }}">Get the apps</a></li>
                    <li><a href="{{ config('karnacab.admin_url') }}/login" rel="noopener">Operator login</a></li>
                </ul>
            </div>
        </div>
        <div class="wrap legal">
            <span>© {{ date('Y') }} {{ $site['name'] ?? 'KarnaRide' }}</span>
            <span>Public website · catalog from KarnaRide API</span>
        </div>
    </footer>
    @stack('scripts')
    <script src="{{ asset('js/karnacab.js') }}"></script>
    @livewireScripts
</body>
</html>
