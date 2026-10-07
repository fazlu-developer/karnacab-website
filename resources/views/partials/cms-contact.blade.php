<section class="section">
    <div class="wrap contact-layout">
        <section class="card">
            @include('partials.cms-legal')
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
            <h3>KarnaRide office</h3>
            <p class="muted">KARNACAB TRANSPORT SERVICE PRIVATE LIMITED<br>Ward no. 6/41, C/O Baleshwar Prasad, Refugee Colony, Kahara, Saharsa, Bihar 852202</p>
            <p class="muted">Toll-free: <a href="tel:+911169270608">+91 1169 270 608</a><br>Support: <a href="tel:9523707084">95237 07084</a><br>Drivers: <a href="tel:9296343483">92963 43483</a></p>
            <p class="muted"><a href="mailto:karnaride@gmail.com">karnaride@gmail.com</a><br><a href="mailto:Karnacabservice@gmail.com">Karnacabservice@gmail.com</a></p>
            <p class="muted"><a href="{{ url('/privacy-policy') }}">Privacy Policy</a> · <a href="{{ route('delete-account') }}">Delete Account</a> · <a href="{{ route('support') }}">FAQs</a></p>
        </aside>
    </div>
</section>
