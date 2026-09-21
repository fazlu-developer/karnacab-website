@extends('layouts.app')

@section('title', 'Book a ride - KarnaCab')
@section('meta', 'Check pickup to destination fares for bike, auto, sedan and more. Complete booking in the KarnaCab customer app.')

@section('content')
    @php $trip = $trip ?? []; @endphp
    @include('partials.page-hero', [
        'eyebrow' => 'Check fares',
        'title' => 'From and to, then pick a vehicle',
        'lede' => 'Enter pickup and destination. We show vehicle types with estimated prices. To book, download the customer app.',
    ])
    <section class="section">
        <div class="wrap contact-layout">
            <section class="card">
                @error('quote') <div class="error">{{ $message }}</div> @enderror

                <form method="POST" action="{{ route('book.quote') }}" class="route-form" data-book-form>
                    @csrf
                    <label for="product">Service</label>
                    <select id="product" name="product" required>
                        @php $products = $catalog['products'] ?? $catalog['rideTypes'] ?? []; @endphp
                        @forelse ($products as $row)
                            <option value="{{ $row['key'] }}" @selected($product === ($row['key'] ?? ''))>{{ $row['title'] ?? $row['label'] ?? $row['key'] }}</option>
                        @empty
                            @foreach (['LOCAL_CAB'=>'Local Cab','ONE_WAY'=>'One Way','ROUND_WAY'=>'Round Way','RENTAL'=>'Rental','OUTSTATION'=>'Outstation','AIRPORT'=>'Airport'] as $key => $label)
                                <option value="{{ $key }}" @selected($product === $key)>{{ $label }}</option>
                            @endforeach
                        @endforelse
                    </select>

                    <label for="pickupText">Pickup</label>
                    <div class="place-field">
                        <input id="pickupText" name="pickupText" type="text" value="{{ old('pickupText', $trip['pickupText'] ?? '') }}" placeholder="Search pickup" required autocomplete="off" data-place-search data-place-lat="pickupLat" data-place-lng="pickupLng">
                        <ul class="place-suggest" hidden></ul>
                    </div>
                    <input type="hidden" name="pickupLat" id="pickupLat" value="{{ old('pickupLat', $trip['pickupLat'] ?? '') }}">
                    <input type="hidden" name="pickupLng" id="pickupLng" value="{{ old('pickupLng', $trip['pickupLng'] ?? '') }}">

                    <label for="dropText">Destination</label>
                    <div class="place-field">
                        <input id="dropText" name="dropText" type="text" value="{{ old('dropText', $trip['dropText'] ?? '') }}" placeholder="Search destination" required autocomplete="off" data-place-search data-place-lat="dropLat" data-place-lng="dropLng">
                        <ul class="place-suggest" hidden></ul>
                    </div>
                    <input type="hidden" name="dropLat" id="dropLat" value="{{ old('dropLat', $trip['dropLat'] ?? '') }}">
                    <input type="hidden" name="dropLng" id="dropLng" value="{{ old('dropLng', $trip['dropLng'] ?? '') }}">

                    <label for="hours">Rental hours (if rental)</label>
                    <select id="hours" name="hours">
                        <option value="">—</option>
                        @foreach ($catalog['rentalHours'] ?? [2,4,6,8,12] as $hours)
                            <option value="{{ $hours }}" @selected((string) old('hours') === (string) $hours)>{{ $hours }} hours</option>
                        @endforeach
                    </select>
                    <p><button class="btn" type="submit">Check fares</button></p>
                </form>

                @include('partials.vehicle-quotes', ['quotes' => $quotes ?? []])
            </section>
            <aside class="tile">
                <h3>Booking is in the app</h3>
                <p class="muted">This website shows the route and fare list. OTP, driver matching, live tracking and payment stay in the customer app.</p>
                @include('partials.app-download', ['heading' => 'Download to book', 'lede' => 'Please download the customer app to continue.'])
            </aside>
        </div>
    </section>
@endsection
