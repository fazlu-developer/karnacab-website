@php
    $site = $cms['site'] ?? [];
    $customerStore = $site['customerPlayStoreUrl'] ?? $site['playStoreUrl'] ?? config('karnacab.apps.customer');
    $driverStore = $site['driverPlayStoreUrl'] ?? config('karnacab.apps.driver');
    $customerApk = \App\Services\AppRelease::customer();
    $driverApk = \App\Services\AppRelease::driver();
    $customerLogo = asset('branding/karnaride-logo.png');
    $driverLogo = asset('branding/karnaride-partner-logo.png');
@endphp
<section class="app-download {{ !empty($compact) ? 'is-compact' : '' }}" id="apps">
    <div class="section-head">
        <div class="eyebrow">Get the apps</div>
        <h2>{{ $heading ?? 'Book and drive with KarnaRide' }}</h2>
        <p class="muted lede">{{ $lede ?? 'Download the customer app to finish a booking, or the driver app to go online.' }}</p>
    </div>
    <div class="app-download-grid">
        <article class="tile app-card">
            <img src="{{ $customerLogo }}" alt="KarnaRide customer app">
            <h3>Customer app</h3>
            <p class="muted">Bike, auto, cab, parcel and travel. OTP login, live tracking, wallet and invoices.</p>
            @if ($customerApk)
                <a class="btn" href="{{ route('apps.customer') }}">Download customer APK</a>
            @endif
            @if (is_string($customerStore) && $customerStore !== '')
                <p><a class="btn ghost" href="{{ $customerStore }}" rel="noopener">Get on Play Store</a></p>
            @endif
        </article>
        <article class="tile app-card">
            <img src="{{ $driverLogo }}" alt="KarnaRide driver app">
            <h3>Driver app</h3>
            <p class="muted">Go online, accept trips, complete KYC and manage your wallet.</p>
            @if ($driverApk)
                <a class="btn" href="{{ route('apps.driver') }}">Download driver APK</a>
            @endif
            @if (is_string($driverStore) && $driverStore !== '')
                <p><a class="btn ghost" href="{{ $driverStore }}" rel="noopener">Get on Play Store</a></p>
            @endif
        </article>
    </div>
</section>
