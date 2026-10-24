@php
    $site = $cms['site'] ?? [];
    $customerStore = $site['customerPlayStoreUrl'] ?? $site['playStoreUrl'] ?? config('karnacab.apps.customer');
    $driverStore = $site['driverPlayStoreUrl'] ?? config('karnacab.apps.driver');
    $customerApk = \App\Services\AppRelease::customer();
    $customerAab = \App\Services\AppRelease::customerAab();
    $driverApk = \App\Services\AppRelease::driver();
    $driverAab = \App\Services\AppRelease::driverAab();
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
            <div class="download-links">
                @if ($customerApk)
                    <a class="btn" href="{{ route('apps.customer') }}">Customer APK {{ $customerApk['version'] }}</a>
                @endif
                @if ($customerAab)
                    <a class="btn ghost" href="{{ route('apps.customer.aab') }}">Customer AAB {{ $customerAab['version'] }}</a>
                @endif
                @if (is_string($customerStore) && $customerStore !== '')
                    <a class="btn ghost" href="{{ $customerStore }}" rel="noopener">Play Store</a>
                @endif
            </div>
        </article>
        <article class="tile app-card">
            <img src="{{ $driverLogo }}" alt="KarnaRide driver app">
            <h3>Driver app</h3>
            <p class="muted">Go online, accept trips, complete KYC and manage your wallet.</p>
            <div class="download-links">
                @if ($driverApk)
                    <a class="btn" href="{{ route('apps.driver') }}">Driver APK {{ $driverApk['version'] }}</a>
                @endif
                @if ($driverAab)
                    <a class="btn ghost" href="{{ route('apps.driver.aab') }}">Driver AAB {{ $driverAab['version'] }}</a>
                @endif
                @if (is_string($driverStore) && $driverStore !== '')
                    <a class="btn ghost" href="{{ $driverStore }}" rel="noopener">Play Store</a>
                @endif
            </div>
        </article>
    </div>
</section>
