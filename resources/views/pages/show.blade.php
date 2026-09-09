@extends('layouts.app')

@section('title', $page['seoTitle'] ?? ($page['title'].' | KarnaCab'))
@section('meta', $page['seoDescription'] ?? ($page['lede'] ?? ''))

@section('content')
    @include('partials.page-hero', [
        'eyebrow' => $page['eyebrow'] ?? '',
        'title' => $page['title'],
        'lede' => $page['lede'],
    ])

    @php $template = $page['template'] ?? 'service'; @endphp

    @if ($template === 'rides')
        @include('partials.cms-rides')
    @elseif ($template === 'travel')
        @include('partials.cms-travel')
    @elseif ($template === 'support')
        @include('partials.cms-support')
    @elseif ($template === 'register')
        @include('partials.cms-register')
    @elseif ($template === 'contact')
        @include('partials.cms-contact')
    @elseif ($template === 'legal')
        @include('partials.cms-legal')
    @else
        @include('partials.cms-service')
    @endif
@endsection
