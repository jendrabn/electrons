@props(['category', 'inline' => false])

@if (empty($category))
    {{-- No category provided — render nothing to avoid errors when called with null --}}
    @php
        return;
    @endphp
@endif

@php
    // Expecting $category to always have name, color and slug per project conventions
    $name = $category->name;
    $color = $category->color ?? '#6c757d';
    $slug = $category->slug;

    // Normalize hex (support short form like #fff)
    $hex = ltrim($color, '#');
    if (strlen($hex) === 3) {
        $hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
    }
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));
    $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
    $textColor = $brightness > 160 ? '#212529' : '#ffffff';
@endphp

@if ($inline)
    <a {{ $attributes->merge(['class' => 'text-decoration-none']) }}
       href="{{ route('posts.category', $slug) }}"
       rel="tag"
       title="Kategori: {{ $name }}">
        <span class="text-uppercase category"
              style="color: {{ $color }}">{{ $name }}</span>
    </a>
@else
    <span {{ $attributes->merge([
        'class' => 'badge rounded-pill position-absolute top-0 start-0 m-2 z-1 fw-medium',
    ]) }}
          style="background-color: {{ $color }}; color: {{ $textColor }};">
        {{ $name }}
    </span>
@endif
