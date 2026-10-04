@extends('layouts.app')

@section('title', 'Drive with KarnaRide')

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => 'Captains',
        'title' => 'Drive with KarnaRide',
        'lede' => 'Bike, auto, and cab captains will join from the driver app. This page is the public story until onboarding opens.',
    ])
    <section class="section">
        <div class="wrap prose">
            <h2>What to expect</h2>
            <p>You will use the KarnaRide driver app, keep documents in your profile, and receive nearby trip requests from the API. That matching flow is not live yet.</p>
            <p>If you want to be notified when captain signup opens, send a message from Contact and mention “drive with us”.</p>
            <p><a class="btn" href="{{ route('contact') }}">Contact the team</a></p>
        </div>
    </section>
@endsection
