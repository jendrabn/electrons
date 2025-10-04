@if (isset($name))
    <span {{ $attributes->merge([
        'class' => 'badge rounded-pill position-absolute top-0 start-0 m-2 z-1 fw-medium',
    ]) }}
          @if (isset($color)) style="background-color: {{ $color }}; color: #fff;" @endif
          aria-label="Kategori: {{ $name }}">
        {{ $name }}
    </span>
@endif
