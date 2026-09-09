@extends('layouts.app')

@section('title', 'Book a ride - KarnaCab')
@section('meta', 'Book a KarnaCab ride using pickup, destination, fare estimate, passenger details and payment.')

@section('content')
    @php $trip = $trip ?? []; @endphp
    @include('partials.page-hero', [
        'eyebrow' => 'Website booking',
        'title' => 'Book a ride',
        'lede' => 'Pickup and destination search Google Places. Fare, booking and payment go through the same Laravel APIs as the mobile apps.',
    ])
    <section class="section">
        <div class="wrap contact-layout">
            <section class="card">
                <ol class="book-steps">
                    <li class="{{ session('booking_quote') ? 'is-done' : 'is-active' }}">1. Trip</li>
                    <li class="{{ session('booking_quote') ? 'is-active' : '' }}">2. Fare</li>
                    <li class="{{ session('booking') ? 'is-active' : '' }}">3. Request</li>
                    <li class="{{ session('booking_payment') ? 'is-done' : '' }}">4. Pay</li>
                </ol>
                @error('quote') <div class="error">{{ $message }}</div> @enderror
                @error('booking') <div class="error">{{ $message }}</div> @enderror
                @error('payment') <div class="error">{{ $message }}</div> @enderror

                <form method="POST" action="{{ route('book.quote') }}" class="route-form" data-book-form>
                    @csrf
                    <label for="product">Service</label>
                    <select id="product" name="product" required>
                        @php $products = $catalog['products'] ?? []; @endphp
                        @forelse ($products as $row)
                            <option value="{{ $row['key'] }}" @selected($product === ($row['key'] ?? ''))>{{ $row['title'] ?? $row['key'] }}</option>
                        @empty
                            @foreach (['LOCAL_CAB'=>'Local Cab','ONE_WAY'=>'One Way','ROUND_WAY'=>'Round Way','RENTAL'=>'Rental','SCHEDULE'=>'Schedule','OUTSTATION'=>'Outstation','AIRPORT'=>'Airport','RAILWAY'=>'Railway'] as $key => $label)
                                <option value="{{ $key }}" @selected($product === $key)>{{ $label }}</option>
                            @endforeach
                        @endforelse
                    </select>

                    <label for="category">Vehicle</label>
                    <select id="category" name="category" required>
                        @php $vehicles = $catalog['vehicles'] ?? []; @endphp
                        @forelse ($vehicles as $row)
                            <option value="{{ $row['key'] }}" @selected($category === ($row['key'] ?? ''))>{{ $row['title'] ?? $row['key'] }}</option>
                        @empty
                            <option value="SEDAN">Sedan</option>
                            <option value="MINI">Mini</option>
                            <option value="SUV">SUV</option>
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
                    <p><button class="btn" type="submit">Get fare estimate</button></p>
                </form>

                @if (is_array($quote))
                    <div class="flash">
                        Estimate ₹{{ number_format(((int) ($quote['totalPaise'] ?? 0)) / 100, 2) }}
                        @if (!empty($quote['billedKm'])) — {{ $quote['billedKm'] }} km billed @endif
                    </div>
                @endif

                @auth
                    <form method="POST" action="{{ route('book.store') }}" class="route-form">
                        @csrf
                        @foreach (['product','category','pickupText','dropText','pickupLat','pickupLng','dropLat','dropLng','hours'] as $field)
                            <input type="hidden" name="{{ $field }}" value="{{ old($field, $trip[$field] ?? '') }}">
                        @endforeach
                        <label for="passengerName">Passenger name</label>
                        <input id="passengerName" name="passengerName" type="text" value="{{ old('passengerName', auth()->user()->name) }}" required>

                        <label for="passengerPhone">Passenger phone</label>
                        <input id="passengerPhone" name="passengerPhone" type="tel" value="{{ old('passengerPhone') }}" required>

                        <label for="scheduledAt">Pickup date and time</label>
                        <input id="scheduledAt" name="scheduledAt" type="datetime-local" value="{{ old('scheduledAt') }}">

                        <label for="returnAt">Return date and time (round way)</label>
                        <input id="returnAt" name="returnAt" type="datetime-local" value="{{ old('returnAt') }}">

                        <label for="flightNumber">Flight number (airport)</label>
                        <input id="flightNumber" name="flightNumber" type="text" value="{{ old('flightNumber') }}">

                        <label for="trainNumber">Train number (railway)</label>
                        <input id="trainNumber" name="trainNumber" type="text" value="{{ old('trainNumber') }}">

                        <label for="instructions">Notes</label>
                        <input id="instructions" name="instructions" type="text" value="{{ old('instructions') }}">
                        <p><button class="btn" type="submit">Send booking request</button></p>
                    </form>
                @else
                    <p class="muted">Log in to send the booking request and pay. <a href="{{ route('login') }}">Log in</a></p>
                @endauth

                @if (is_array($booking) && !empty($booking['id']))
                    <form method="POST" action="{{ route('book.pay') }}" class="route-form">
                        @csrf
                        <label for="method">Payment</label>
                        <select id="method" name="method" required>
                            <option value="upi">UPI</option>
                            <option value="card">Card</option>
                            <option value="wallet">Wallet</option>
                            <option value="cash">Cash</option>
                        </select>
                        <p class="muted">UPI and card stay pending until a signed gateway webhook. The website never marks payment successful.</p>
                        <p><button class="btn" type="submit">Pay for booking {{ $booking['publicRef'] ?? $booking['id'] }}</button></p>
                    </form>
                @endif
            </section>
            <aside class="tile">
                <h3>Same booking engine</h3>
                <p class="muted">This page does not calculate fares locally. Quotes and bookings are created by Laravel <code>/api/v1/rides</code>.</p>
                <p><a href="{{ route('route') }}">Map preview</a></p>
            </aside>
        </div>
    </section>
@endsection
