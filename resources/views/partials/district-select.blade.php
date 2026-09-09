@php
    $districts = $cms['catalog']['districts'] ?? [];
    $selected = old('district');
@endphp
<label for="district">District</label>
@if (count($districts))
    <select id="district" name="district">
        <option value="">Select district</option>
        @foreach ($districts as $district)
            @php $name = $district['name'] ?? $district; @endphp
            <option value="{{ $name }}" @selected($selected === $name)>{{ $name }}</option>
        @endforeach
    </select>
@else
    <input id="district" name="district" value="{{ old('district') }}" placeholder="District">
@endif
