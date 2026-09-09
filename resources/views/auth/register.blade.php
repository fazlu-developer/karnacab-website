@extends('layouts.app')

@section('title', 'Create account - KarnaCab')

@section('content')
    <div class="auth-shell">
        <aside class="auth-visual">
            <div class="eyebrow">Get started</div>
            <h2>One account for bike, auto, and cab.</h2>
            <p>Register on the website now. Matching, wallets, and payments arrive in later phases.</p>
        </aside>
        <div class="auth-form-wrap">
            <section class="auth-card">
                <h1>Create account</h1>
                <p class="muted">Customer shell for the KarnaCab public site.</p>
                <form method="POST" action="{{ route('register') }}">
                    @csrf
                    <label for="name">Name</label>
                    <input id="name" name="name" type="text" value="{{ old('name') }}" required autocomplete="name">
                    @error('name') <div class="error">{{ $message }}</div> @enderror

                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email') }}" required autocomplete="email">
                    @error('email') <div class="error">{{ $message }}</div> @enderror

                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password">
                    @error('password') <div class="error">{{ $message }}</div> @enderror

                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password">

                    <p><button class="btn wide" type="submit">Create account</button></p>
                </form>
                <p class="muted">Already have an account? <a href="{{ route('login') }}">Log in</a></p>
            </section>
        </div>
    </div>
@endsection
