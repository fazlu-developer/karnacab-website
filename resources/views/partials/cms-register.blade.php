@php
    $kind = $page['registerKind'] ?? '';
    $districts = $cms['catalog']['districts'] ?? [];
@endphp
<section class="section">
    <div class="wrap contact-layout">
        <section class="card">
            @include('partials.cms-sections')
            @if ($kind === 'driver')
                <h2>Driver registration</h2>
                <form method="POST" action="{{ route('partners.register') }}">
                    @csrf
                    <input type="hidden" name="kind" value="driver">
                    <label for="name">Name</label>
                    <input id="name" name="name" required value="{{ old('name') }}">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}">
                    <label for="licenseNo">Licence number</label>
                    <input id="licenseNo" name="licenseNo" required value="{{ old('licenseNo') }}">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                    @error('api') <div class="error">{{ $message }}</div> @enderror
                    <p><button class="btn" type="submit">Create driver account</button></p>
                </form>
            @elseif ($kind === 'advertiser')
                <h2>Advertiser registration</h2>
                <form method="POST" action="{{ route('partners.register') }}">
                    @csrf
                    <input type="hidden" name="kind" value="advertiser">
                    <label for="name">Name</label>
                    <input id="name" name="name" required value="{{ old('name') }}">
                    <label for="email">Email</label>
                    <input id="email" name="email" type="email" required value="{{ old('email') }}">
                    <label for="phone">Phone</label>
                    <input id="phone" name="phone" value="{{ old('phone') }}">
                    <label for="password">Password</label>
                    <input id="password" name="password" type="password" required>
                    <label for="password_confirmation">Confirm password</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required>
                    @error('api') <div class="error">{{ $message }}</div> @enderror
                    <p><button class="btn" type="submit">Create advertiser account</button></p>
                </form>
                @if (!empty($page['leadType']))
                    <hr>
                    <h2>Or send an enquiry</h2>
                    @include('partials.lead-form', ['leadType' => $page['leadType']])
                @endif
            @endif
        </section>
        <aside class="tile">
            <h3>After signup</h3>
            <p class="muted">KYC document types, ad rates and franchise rules are Admin settings. They are not listed as prices on this page.</p>
        </aside>
    </div>
</section>
