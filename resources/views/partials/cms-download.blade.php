<section class="section">
    <div class="wrap">
        @include('partials.cms-sections')
        @if (session('status') || request()->filled('category'))
            <div class="flash" style="margin-bottom:18px">
                Complete this booking to download the KarnaRide app. Install the customer APK, then confirm pickup, vehicle and payment on your phone.
            </div>
        @endif
        @include('partials.app-download', [
            'heading' => 'Download KarnaRide',
            'lede' => session('status') ?: 'Download the latest customer APK to finish a booking, or the driver APK to go online.',
        ])
    </div>
</section>
