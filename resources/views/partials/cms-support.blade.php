@php $faqs = $cms['faqs'] ?? []; @endphp
<section class="section">
    <div class="wrap">
        @include('partials.cms-sections')
        <div class="faq-list">
            @forelse ($faqs as $faq)
                <details>
                    <summary>{{ $faq['question'] ?? $faq['q'] ?? 'Question' }}</summary>
                    <p class="muted">{{ $faq['answer'] ?? $faq['a'] ?? '' }}</p>
                </details>
            @empty
                <p class="muted">FAQs load from GET /support/faqs when the API is available.</p>
            @endforelse
        </div>
        <div class="card" style="margin-top: 28px;">
            @include('partials.lead-form', ['leadType' => 'SUPPORT'])
        </div>
    </div>
</section>
