<section class="section">
    <div class="wrap">
        @include('partials.cms-sections')
        @include('partials.app-download', [
            'heading' => 'Download KarnaCab',
            'lede' => session('status') ?: 'Please download the customer app to book a trip, or the driver app to go online.',
        ])
    </div>
</section>
