@props([
    'tag' => null,
    'withIcon' => true,
    'size' => 'md',
    'small' => null,
])

@if (empty($tag))
    {{-- No tag provided - render nothing to avoid errors when called with null --}}
    @php
        return;
    @endphp
@endif

@php
    /** Reusable Tag Badge (matches sidebar style) */
    $smallAttribute = ! is_null($small)
        ? filter_var($small, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE)
        : null;

    if ($smallAttribute === null && ! is_null($small)) {
        $smallAttribute = (bool) $small;
    }

    $normalizedSize = strtolower((string) $size);
    $isSmall = $smallAttribute ?? in_array($normalizedSize, ['sm', 'small'], true);

    $baseClasses = implode(' ', array_filter([
        'badge',
        'bg-light',
        'text-dark',
        'border',
        'rounded-pill',
        'shadow-sm',
        'd-inline-flex',
        'align-items-center',
        'gap-1',
        'text-decoration-none',
        $isSmall ? 'px-2 py-1' : 'px-3 py-2',
        $isSmall ? 'small' : null,
    ]));

    $iconClasses = implode(' ', array_filter([
        'bi',
        'bi-tag',
        $isSmall ? 'fs-6' : null,
    ]));
@endphp

<a {{ $attributes->merge(['class' => $baseClasses]) }}
   href="{{ route('posts.tag', $tag->slug) }}"
   rel="tag"
   title="Tag: {{ $tag->name }}">
    @if ($withIcon)
        <i class="{{ $iconClasses }}"></i>
    @endif
    <span @if ($isSmall) class="small" @endif>{{ $tag->name }}</span>
</a>
