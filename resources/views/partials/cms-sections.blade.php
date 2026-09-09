@php
    $sections = $page['body']['sections'] ?? [];
@endphp
@foreach ($sections as $section)
    @if (!empty($section['heading']) || !empty($section['text']))
        <h2>{{ $section['heading'] ?? '' }}</h2>
        @if (!empty($section['text']))
            <p>{{ $section['text'] }}</p>
        @endif
        @if (!empty($section['steps']))
            <div class="grid-3">
                @foreach ($section['steps'] as $index => $step)
                    <article class="tile">
                        <div class="step-num">{{ $index + 1 }}</div>
                        <h3>{{ $step['title'] }}</h3>
                        <p class="muted">{{ $step['text'] }}</p>
                    </article>
                @endforeach
            </div>
        @endif
    @endif
@endforeach
