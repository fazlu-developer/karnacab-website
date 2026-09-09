@extends('layouts.app')

@section('title', $title.' - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => $eyebrow,
        'title' => $title,
        'lede' => $lede,
    ])
    <section class="section">
        <div class="wrap contact-layout">
            <section class="card">
                <h2>Send a request</h2>
                <p class="muted">This creates a lead in the shared KarnaCab database. It does not dispatch a driver.</p>
                <form method="POST" action="{{ route('lead.store') }}">
                    @csrf
                    <input type="hidden" name="type" value="{{ $lead_type }}">
                    <label for="name">Name</label>
                    <input id="name" name="name" required value="{{ old('name') }}">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" required value="{{ old('phone') }}">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}">
                    <label for="district">District</label>
                    <input id="district" name="district" value="{{ old('district', 'Patna') }}">
                    <label for="message">Requirement</label>
                    <textarea id="message" name="message" rows="5" required>{{ old('message') }}</textarea>
                    @error('api') <div class="error">{{ $message }}</div> @enderror
                    <p><button class="btn" type="submit">Submit request</button></p>
                </form>
            </section>
            <aside class="tile">
                <h3>Need a map path?</h3>
                <p class="muted">Use Get route for pickup and drop preview.</p>
                <p><a class="btn dark" href="{{ route('route') }}">Get route</a></p>
            </aside>
        </div>
    </section>
@endsection
