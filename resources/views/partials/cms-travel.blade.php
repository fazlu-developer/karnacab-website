@php
    $packages = $cms['catalog']['packages'] ?? [];
    $categories = $cms['catalog']['travelCategories'] ?? [];
@endphp
<section class="section">
    <div class="wrap">
        @include('partials.cms-sections')
        @if (count($categories))
            <p class="muted">Categories:
                {{ collect($categories)->map(function ($row) {
                    if (is_array($row)) {
                        return $row['label'] ?? $row['key'] ?? '';
                    }
                    return (string) $row;
                })->filter()->join(', ') }}
            </p>
        @endif
        <div class="grid-3">
            @forelse ($packages as $package)
                <article class="tile">
                    <div class="eyebrow">{{ $package['categoryLabel'] ?? $package['category'] ?? 'Package' }}</div>
                    <h3>{{ $package['title'] }}</h3>
                    <p class="muted">{{ $package['destination'] ?? '' }} · {{ $package['durationLabel'] ?? '' }}</p>
                    @if (isset($package['priceRupees']))
                        <p><strong>₹{{ number_format((float) $package['priceRupees'], 0) }}</strong> <span class="muted">from package record</span></p>
                    @endif
                    <p class="muted">{{ $package['places'] ?? '' }}</p>
                </article>
            @empty
                <article class="tile">
                    <h3>No published packages</h3>
                    <p class="muted">Admin publishes KarnaTravel packages in operations. They will appear here automatically.</p>
                </article>
            @endforelse
        </div>
        <div class="card" style="margin-top: 28px;">
            @include('partials.lead-form', ['leadType' => $page['leadType'] ?? 'TRAVEL'])
        </div>
    </div>
</section>
