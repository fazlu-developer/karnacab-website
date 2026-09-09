@php $districts = $cms['catalog']['districts'] ?? []; @endphp
<h2>Send a request</h2>
<p class="muted">Creates a lead in the KarnaCab API. Confirming a paid trip happens in the customer app after sign-in.</p>
<form method="POST" action="{{ route('lead.store') }}">
    @csrf
    <input type="hidden" name="type" value="{{ $leadType }}">
    <label for="name">Name</label>
    <input id="name" name="name" required value="{{ old('name') }}">
    <label for="phone">Phone</label>
    <input id="phone" name="phone" required value="{{ old('phone') }}">
    <label for="email">Email</label>
    <input id="email" name="email" type="email" value="{{ old('email') }}">
    @include('partials.district-select')
    <label for="message">Requirement</label>
    <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
    @error('api') <div class="error">{{ $message }}</div> @enderror
    <p><button class="btn" type="submit">Submit request</button></p>
</form>
