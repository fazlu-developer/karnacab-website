@php
    $site = $cms['site'] ?? [];
    $customerUrl = $site['playStoreUrl'] ?? config('karnacab.apps.customer');
    $driverUrl = $site['driverPlayStoreUrl'] ?? config('karnacab.apps.driver');
    if (! is_string($customerUrl) || $customerUrl === '') {
        $customerUrl = route('download');
    }
    if (! is_string($driverUrl) || $driverUrl === '') {
        $driverUrl = route('download');
    }
@endphp
<section class="app-download" id="apps">
    <div class="section-head">
        <div class="eyebrow">Get the apps</div>
        <h2>{{ $heading ?? 'Book and drive with KarnaCab' }}</h2>
        <p class="muted lede">{{ $lede ?? 'Rides are confirmed in the mobile apps. Download the customer app to book, or the driver app to go online.' }}</p>
    </div>
    <div class="app-download-grid">
        <article class="tile app-card">
            <img src="{{ asset('branding/customer-app.png') }}" alt="KarnaCab customer app" width="96" height="96">
            <h3>Customer app</h3>
            <p class="muted">Bike, auto, cab, parcel and travel from your phone. OTP login, live tracking, wallet and invoices.</p>
            <a class="btn" href="{{ $customerUrl }}" rel="noopener">Download customer app</a>
        </article>
        <article class="tile app-card">
            <img src="{{ asset('branding/driver-app.png') }}" alt="KarnaCab driver app" width="96" height="96">
            <h3>Driver app</h3>
            <p class="muted">Go online, accept trips, complete KYC and manage your wallet. Built for captains and fleet partners.</p>
            <a class="btn ghost" href="{{ $driverUrl }}" rel="noopener">Download driver app</a>
        </article>
    </div>
</section>
