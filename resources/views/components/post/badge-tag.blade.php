@if (isset($tag))
    @php
        /** Reusable Tag Badge (matches sidebar style) */
        $withIcon = $withIcon ?? true;
        $baseClasses =
            'badge bg-light text-dark border rounded-pill px-3 py-2 shadow-sm d-inline-flex align-items-center gap-1 text-decoration-none';
    @endphp

    <a {{ $attributes->merge(['class' => $baseClasses]) }}
       href="{{ route('posts.tag', $tag->slug) }}">
        @if ($withIcon)
            <i class="bi bi-tag"></i>
        @endif
        <span>{{ $tag->name }}</span>
    </a>
@endif
