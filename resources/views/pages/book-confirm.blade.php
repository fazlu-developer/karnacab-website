@extends('layouts.app')

@section('title', 'Booking confirmation - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Confirmation',
        'title' => 'Booking received',
        'lede' => 'Your request is stored on the KarnaCab platform. Payment capture is server-side.',
    ])
    <section class="section">
        <div class="wrap">
            <article class="card">
                @if (!empty($booking['publicRef']))
                    <h2>{{ $booking['publicRef'] }}</h2>
                    <p>Status: {{ $booking['status'] ?? 'REQUESTED' }}</p>
                    <p>{{ $booking['pickupText'] ?? '' }} → {{ $booking['dropText'] ?? '' }}</p>
                    <p>Passenger: {{ $booking['passengerName'] ?? '' }} {{ $booking['passengerPhone'] ?? '' }}</p>
                    <p>Payable: ₹{{ number_format(((int) ($booking['quotePaise'] ?? 0)) / 100, 2) }}</p>
                @else
                    <p>We could not load that booking. Check your account or try booking again.</p>
                @endif
                @if (is_array($payment))
                    <div class="flash">
                        Payment {{ $payment['status'] ?? '' }}
                        @if (!empty($payment['paymentReference'])) — {{ $payment['paymentReference'] }} @endif
                    </div>
                    <p class="muted">Frontend success is ignored. UPI/card wait for a signed webhook.</p>
                @endif
                <p><a class="btn" href="{{ route('book') }}">Book another ride</a></p>
            </article>
        </div>
    </section>
@endsection
