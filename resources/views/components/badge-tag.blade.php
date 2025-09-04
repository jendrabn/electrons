@if (isset($tag))
    <a {{ $attributes->merge(['class' => 'badge bg-secondary text-decoration-none fw-normal rounded-0']) }}
       href="{{ route('posts.tag', $tag->slug) }}">{{ $tag->name }}</a>
@endif
