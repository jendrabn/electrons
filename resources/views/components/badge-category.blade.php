@if (isset($name))
    <span
          {{ $attributes->merge([
              'class' => 'badge bg-warning rounded-0 position-absolute top-0 start-0 m-2 z-1 fw-normal d-none d-lg-block',
          ]) }}>
        {{ $name }}
    </span>
@endif
