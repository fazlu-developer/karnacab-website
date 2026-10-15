@if (!empty($quotes))
    <div class="vehicle-quotes" id="vehicles">
        <h2>Vehicles and fares</h2>
        <p class="muted">Pick a vehicle to see the estimated fare. Booking is completed in the KarnaRide customer app.</p>
        <div class="grid-3">
            @foreach ($quotes as $row)
                <article class="tile">
                    <h3>{{ $row['title'] ?? $row['key'] }}</h3>
                    @if (!empty($row['totalPaise']))
                        <p class="quote-price">₹{{ number_format(((int) $row['totalPaise']) / 100, 0) }}</p>
                    @else
                        <p class="quote-price muted">Fare in the app</p>
                    @endif
                    @if (!empty($row['billedKm']))
                        <p class="muted">{{ number_format((float) $row['billedKm'], 1) }} km billed</p>
                    @endif
                    <form method="POST" action="{{ route('book.continue') }}">
                        @csrf
                        <input type="hidden" name="category" value="{{ $row['key'] }}">
                        <button class="btn" type="submit">Book {{ $row['title'] ?? $row['key'] }}</button>
                    </form>
                </article>
            @endforeach
        </div>
    </div>
@endif
