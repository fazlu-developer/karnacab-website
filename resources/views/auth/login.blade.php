@extends('layouts.app')

@section('title', 'Log in - KarnaCab')

@section('content')
    <div class="auth-shell">
        <aside class="auth-visual">
            <div class="eyebrow">Customer account</div>
            <h2>Welcome back to KarnaCab.</h2>
            <p>Sign in to your Phase 1 website account. Trip booking is not enabled yet.</p>
        </aside>
        <div class="auth-form-wrap">
            <section class="auth-card">
                <h1>Log in</h1>
                <p class="muted">Use the email and password you registered with.</p>
                <form method="POST" action="{{ route('login') }}">
                    @csrf
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email') <div class="error">{{ $message }}</div> @enderror

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="current-password">
                    @error('password') <div class="error">{{ $message }}</div> @enderror

                    <p><button class="btn wide" type="submit">Log in</button></p>
                </form>
            </section>
        </div>
    </div>
@endsection
