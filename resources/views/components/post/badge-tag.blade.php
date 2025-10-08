@props([
    'tag' => null,
    'withIcon' => true,
    'size' => 'md',
    'small' => null,
])

@if (empty($tag))
    @php return; @endphp
@endif

@php
    /** Reusable Tag Badge (now dark-mode adaptive) */
    $smallAttribute = !is_null($small) ? filter_var($small, FILTER_VALIDATE_BOOL, FILTER_NULL_ON_FAILURE) : null;

    if ($smallAttribute === null && !is_null($small)) {
        $smallAttribute = (bool) $small;
    }

    $normalizedSize = strtolower((string) $size);
    $isSmall = $smallAttribute ?? in_array($normalizedSize, ['sm', 'small'], true);

    $baseClasses = implode(
        ' ',
        array_filter([
            'badge',
            'bg-body-tertiary', // ✅ adaptif: terang di light mode, gelap di dark mode
            'text-body-secondary', // ✅ adaptif: teks berubah sesuai tema
            'border',
            'rounded-pill',
            'shadow-sm',
            'd-inline-flex',
            'align-items-center',
            'gap-1',
            'text-decoration-none',
            $isSmall ? 'px-2 py-1' : 'px-3 py-2',
            $isSmall ? 'small' : null,
        ]),
    );

    $iconClasses = implode(' ', array_filter(['bi', 'bi-tag', $isSmall ? 'fs-6' : null]));
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

@push('styles')
    <style>
        /* Hover adaptif mengikuti tema */
        .badge:hover {
            background-color: var(--bs-body-secondary-bg);
            color: var(--bs-body-emphasis-color);
            text-decoration: none;
            transition: background-color .2s ease, color .2s ease;
        }
    </style>
@endpush
