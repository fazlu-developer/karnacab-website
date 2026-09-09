<section class="section">
    <div class="wrap contact-layout">
        <section class="card">
            @include('partials.cms-sections')
            <form method="POST" action="{{ route('contact.store') }}">
                @csrf
                <label for="name">Name</label>
                <input id="name" name="name" type="text" value="{{ old('name') }}" required>
                @error('name') <div class="error">{{ $message }}</div> @enderror

                <label for="phone">Phone</label>
                <input id="phone" name="phone" value="{{ old('phone') }}" required>
                @error('phone') <div class="error">{{ $message }}</div> @enderror

                <label for="email">Email</label>
                <input id="email" name="email" type="email" value="{{ old('email') }}" required>
                @error('email') <div class="error">{{ $message }}</div> @enderror

                @include('partials.district-select')

                <label for="message">Message</label>
                <textarea id="message" name="message" rows="6" required>{{ old('message') }}</textarea>
                @error('message') <div class="error">{{ $message }}</div> @enderror
                @error('api') <div class="error">{{ $message }}</div> @enderror

                <p><button class="btn" type="submit">Send message</button></p>
            </form>
        </section>
        <aside class="tile">
            <h3>Also useful</h3>
            <p class="muted"><a href="{{ route('support') }}">Support FAQs</a> · <a href="{{ route('route') }}">Get route</a></p>
            <p class="muted">Account: <a href="{{ route('register') }}">create an account</a> or <a href="{{ route('login') }}">log in</a>.</p>
        </aside>
    </div>
</section>
