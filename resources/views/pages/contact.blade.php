@extends('layouts.app')

@section('title', 'Contact - KarnaCab')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Hello',
        'title' => 'Contact us',
        'lede' => 'Questions about the website, route preview, or a future driver account — write here.',
    ])
    <section class="section">
        <div class="wrap contact-layout">
            <section class="card">
                <form method="POST" action="{{ route('contact.store') }}">
                    @csrf
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                    @error('name') <div class="error">{{ $message }}</div> @enderror

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                    @error('email') <div class="error">{{ $message }}</div> @enderror

                    <label for="message">Message</label>
                    <textarea id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                    @error('message') <div class="error">{{ $message }}</div> @enderror

                    <p><button class="btn" type="submit">Send message</button></p>
                </form>
            </section>
            <aside class="tile">
                <h3>Also useful</h3>
                <p class="muted">Route problems: try a fuller landmark on Get route.</p>
                <p class="muted">Account: <a href="{{ route('register') }}">create an account</a> or <a href="{{ route('login') }}">log in</a>.</p>
                <p class="muted">Safety notes: <a href="{{ route('safety') }}">Safety</a>.</p>
            </aside>
        </div>
    </section>
@endsection
