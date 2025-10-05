@props(['tag' => null, 'withIcon' => true])

@if (empty($tag))
    {{-- No tag provided — render nothing to avoid errors when called with null --}}
    @php
        return;
    @endphp
@endif

@php
    /** Reusable Tag Badge (matches sidebar style) */
    $baseClasses =
        'badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1 text-decoration-none';
@endphp

<a {{ $attributes->merge(['class' => $baseClasses]) }}
   href="{{ route('posts.tag', $tag->slug) }}"
   rel="tag"
   title="Tag: {{ $tag->name }}">
    @if ($withIcon)
        <i class="bi bi-tag"></i>
    @endif
    <span>{{ $tag->name }}</span>
</a>
