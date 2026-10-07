@extends('layouts.app')

@section('title', 'Delete Account | KarnaRide')
@section('meta', 'Request deletion of your KarnaRide customer account and associated personal data.')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Legal',
        'title' => 'Delete Account',
        'lede' => 'Remove your KarnaRide customer account. This is a public page. You do not need to sign in.',
    ])
    <section class="section">
        <div class="wrap prose">
            <p>KarnaRide (Karnacab Transport Service Private Limited) lets you delete the customer account created with your mobile number. Use this page or Profile → Delete Account in the KarnaRide customer app.</p>
            <h2>What we delete</h2>
            <p>Your profile, phone login, saved places, device/push tokens and wallet records. You will not be able to sign in with that number unless you create a new account later.</p>
            <h2>What we may keep in anonymised form</h2>
            <p>Completed trip and invoice rows required for Indian tax, accounting or an open dispute. Those records are detached from your name and phone.</p>
            <h2>Before you continue</h2>
            <p>Finish or cancel any open trip. Driver and fleet accounts are not deleted here — use partner-app support.</p>
            @if (session('status'))
                <p class="muted">{{ session('status') }}</p>
            @endif
            <form method="POST" action="{{ route('delete-account.otp') }}" style="margin-bottom:1.5rem">
                @csrf
                <label for="phone">Registered mobile number</label>
                <input id="phone" name="phone" type="tel" inputmode="numeric" value="{{ old('phone') }}" required maxlength="15" placeholder="10-digit Indian mobile">
                @error('phone') <div class="error">{{ $message }}</div> @enderror
                <p><button class="btn" type="submit">Send OTP</button></p>
            </form>
            <form method="POST" action="{{ route('delete-account.destroy') }}">
                @csrf
                <input type="hidden" name="phone" value="{{ old('phone') }}">
                <label for="code">OTP</label>
                <input id="code" name="code" type="text" inputmode="numeric" value="{{ old('code') }}" required maxlength="6" autocomplete="one-time-code">
                @error('code') <div class="error">{{ $message }}</div> @enderror
                <p><button class="btn" type="submit">Permanently delete my account</button></p>
            </form>
            <p class="muted">Privacy Policy: <a href="{{ url('/privacy-policy') }}">/privacy-policy</a>. Questions: karnaride@gmail.com</p>
        </div>
    </section>
@endsection
