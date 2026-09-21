@if (!empty($quotes))
    <div class="vehicle-quotes">
        <h2>Vehicles and fares</h2>
        <p class="muted">Estimates use the same fare rules as the apps. Confirm and pay in the customer app.</p>
        <div class="grid-3">
            @foreach ($quotes as $row)
                <article class="tile">
                    <h3>{{ $row['title'] ?? $row['key'] }}</h3>
                    @if (!empty($row['totalPaise']))
                        <p class="quote-price">₹{{ number_format(((int) $row['totalPaise']) / 100, 0) }}</p>
                    @else
                        <p class="quote-price muted">See price in app</p>
                    @endif
                    @if (!empty($row['billedKm']))
                        <p class="muted">{{ $row['billedKm'] }} km billed</p>
                    @endif
                    <form method="POST" action="{{ route('book.continue') }}">
                        @csrf
                        <input type="hidden" name="category" value="{{ $row['key'] }}">
                        <button class="btn" type="submit">Proceed to book</button>
                    </form>
                </article>
            @endforeach
        </div>
    </div>
@endif
