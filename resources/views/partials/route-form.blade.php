@php
    $pickupValue = $pickup ?? request('pickup');
    $dropValue = $drop ?? request('drop');
    $vehicleValue = $vehicle ?? request('vehicle', 'cab');
    $prefix = $idPrefix ?? '';
@endphp
<form class="route-form" method="GET" action="{{ route('route') }}">
    <div class="vehicle-pills" role="radiogroup" aria-label="Vehicle">
        @foreach (['bike' => 'Bike', 'auto' => 'Auto', 'cab' => 'Cab'] as $value => $label)
            <label class="pill {{ $vehicleValue === $value ? 'active' : '' }}">
                <input type="radio" name="vehicle" value="{{ $value }}" {{ $vehicleValue === $value ? 'checked' : '' }}>
                {{ $label }}
            </label>
        @endforeach
    </div>
    <label for="{{ $prefix }}pickup">Pickup</label>
    <div class="place-field">
        <input id="{{ $prefix }}pickup" name="pickup" type="text" value="{{ old('pickup', $pickupValue) }}" placeholder="Search pickup" required autocomplete="off" data-place-search data-place-lat="{{ $prefix }}pickup_lat" data-place-lng="{{ $prefix }}pickup_lng">
        <ul class="place-suggest" hidden></ul>
    </div>
    <input type="hidden" name="pickup_lat" id="{{ $prefix }}pickup_lat" value="{{ old('pickup_lat', request('pickup_lat')) }}">
    <input type="hidden" name="pickup_lng" id="{{ $prefix }}pickup_lng" value="{{ old('pickup_lng', request('pickup_lng')) }}">
    @error('pickup') <div class="error">{{ $message }}</div> @enderror

    <label for="{{ $prefix }}drop">Destination</label>
    <div class="place-field">
        <input id="{{ $prefix }}drop" name="drop" type="text" value="{{ old('drop', $dropValue) }}" placeholder="Search destination" required autocomplete="off" data-place-search data-place-lat="{{ $prefix }}drop_lat" data-place-lng="{{ $prefix }}drop_lng">
        <ul class="place-suggest" hidden></ul>
    </div>
    <input type="hidden" name="drop_lat" id="{{ $prefix }}drop_lat" value="{{ old('drop_lat', request('drop_lat')) }}">
    <input type="hidden" name="drop_lng" id="{{ $prefix }}drop_lng" value="{{ old('drop_lng', request('drop_lng')) }}">
    @error('drop') <div class="error">{{ $message }}</div> @enderror

    <button class="btn wide" type="submit">Get route</button>
    <p class="soon-note">Destination search uses Google Places through Laravel. Book a ride to get a fare from the same APIs as the apps.</p>
    <p><a href="{{ route('book') }}">Book a ride</a></p>
</form>
