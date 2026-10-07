<section class="section">
    <div class="wrap prose">
        @include('partials.cms-sections')
        @php
            $html = $page['bodyHtml'] ?? ($page['body']['html'] ?? $page['body']['text'] ?? '');
        @endphp
        @if (is_string($html) && trim($html) !== '')
            <div class="legal-copy">{!! nl2br(e($html)) !!}</div>
        @endif
    </div>
</section>
