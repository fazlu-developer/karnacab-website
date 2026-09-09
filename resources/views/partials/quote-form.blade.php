@php
    $vehicles = $cms['catalog']['vehicleTypes'] ?? [];
    $rentals = $cms['catalog']['rentalPackages'] ?? [];
    $quote = session('quote');
    $product = $page['productKey'] ?? '';
@endphp
<h2>Fare quote</h2>
<p class="muted">Totals come from Laravel POST /api/v1/rides/quote (the same fare_rules engine as the apps). This is an estimate, not a confirmed booking.</p>
<form method="POST" action="{{ route('quote.preview') }}">
    @csrf
    <input type="hidden" name="product" value="{{ $product }}">
    <label for="category">Vehicle</label>
    <select id="category" name="category" required>
        @forelse ($vehicles as $vehicle)
            <option value="{{ $vehicle['key'] }}" @selected(old('category') === ($vehicle['key'] ?? ''))>
                {{ $vehicle['title'] ?? $vehicle['label'] ?? $vehicle['key'] }}
            </option>
        @empty
            <option value="SEDAN">SEDAN</option>
        @endforelse
    </select>
    <label for="distanceKm">Distance (km)</label>
    <input id="distanceKm" name="distanceKm" type="number" min="1" required value="{{ old('distanceKm', 10) }}">
    @if ($product === 'RENTAL')
        <label for="hours">Hours</label>
        <select id="hours" name="hours">
            @forelse ($rentals as $pkg)
                <option value="{{ $pkg['hours'] }}" @selected((string) old('hours') === (string) ($pkg['hours'] ?? ''))>
                    {{ $pkg['hours'] }} hours
                </option>
            @empty
                <option value="8">8</option>
            @endforelse
        </select>
    @endif
    @error('quote') <div class="error">{{ $message }}</div> @enderror
    <p><button class="btn ghost" type="submit">Get quote</button></p>
</form>
@if (is_array($quote))
    <div class="flash">
        Quote
        @if (isset($quote['totalPaise']))
            ₹{{ number_format(((int) $quote['totalPaise']) / 100, 2) }}
        @elseif (isset($quote['fare']['totalPaise']))
            ₹{{ number_format(((int) $quote['fare']['totalPaise']) / 100, 2) }}
        @elseif (isset($quote['totalRupees']))
            ₹{{ $quote['totalRupees'] }}
        @else
            received from API
        @endif
    </div>
@endif
