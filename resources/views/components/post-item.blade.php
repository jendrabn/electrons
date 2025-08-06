@props(['post', 'type' => 'vertical'])

@if ($type == 'vertical')
    <article class="post-item post-item-vertical card border-0 h-100 w-100">
        <div class="position-relative">
            <a href="{{ route('posts.show', $post->slug) }}">
                <figure class="post-item-image bg-gray-200 rounded-3 overflow-hidden w-100 ratio ratio-16x9">
                    <picture>
                        <img alt="{{ $post->image_caption }}"
                             class="h-100 w-100 object-fit-cover"
                             loading="lazy"
                             src="{{ $post->image_url }}" />
                    </picture>
                </figure>

                <span class="badge bg-warning rounded-0 position-absolute top-0 start-0 m-2 z-1 fw-normal">
                    {{ $post->category->name ?? '-' }}
                </span>
            </a>
        </div>
        <div class="card-body px-0 py-1">
            <a class="text-decoration-none text-dark"
               href="{{ route('posts.show', $post->slug) }}">
                <h5 class="card-title fw-semibold">{{ $post->title }}</h5>
            </a>
            <p class="card-text d-none d-lg-block mb-1">{{ $post->excerpt }}</p>
            <p class="card-text text-muted">
                <a class="small fw-semibold text-decoration-none text-dark"
                   href="{{ route('posts.author', $post->user->id) }}">
                    {{ str()->words($post->user->name, 2, '') }}
                </a>
                <span class="mx-1">•</span>
                <small>{{ $post->created_at->format('d M Y') }}</small>
                <span class="mx-1">•</span>
                <small>{{ $post->min_read }} min read</small>
            </p>
        </div>
    </article>
@endif

@if ($type == 'horizontal')
    <article class="post-item post-item-horizontal card border-0 h-100 w-100">
        <div class="row g-0">
            <div class="col-md-3">
                <div class="position-relative">
                    <a href="{{ route('posts.show', $post->slug) }}">
                        <figure class="post-item-image bg-gray-200 rounded-3 overflow-hidden w-100 ratio ratio-16x9">
                            <picture>
                                <img alt="{{ $post->image_caption }}"
                                     class="h-100 w-100 object-fit-cover"
                                     loading="lazy"
                                     src="{{ $post->image_url }}" />
                            </picture>
                        </figure>
                        <span class="badge bg-warning rounded-0 position-absolute top-0 start-0 m-2 z-1 fw-normal">
                            {{ $post->category->name ?? '-' }}
                        </span>
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card-body py-0">
                    <a class="text-decoration-none text-dark"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h5 class="card-title fw-semibold">{{ $post->title }}</h5>
                    </a>
                    <p class="card-text text-muted d-none d-lg-block mb-1">{{ $post->excerpt }}</p>
                    <p class="card-text text-muted">
                        <a class="small fw-semibold text-decoration-none text-dark"
                           href="{{ route('posts.author', $post->user->id) }}">
                            {{ str()->words($post->user->name, 2, '') }}
                        </a>
                        <span class="mx-1">•</span>
                        <small>{{ $post->created_at->format('d M Y') }}</small>
                        <span class="mx-1">•</span>
                        <small>{{ $post->min_read }} min read</small>
                    </p>
                </div>
            </div>
        </div>
    </article>
@endif

@if ($type == 'mini')
    <article class="card border-0 h-100 w-100">
        <div class="row g-0">
            <div class="col-3">
                <figure class="w-100 bg-gray-200 ratio ratio-16x9 m-0 overflow-hidden rounded-2">
                    <picture>
                        <img alt="{{ $post->image_caption }}"
                             class="h-100 w-100 object-fit-cover"
                             src="{{ $post->image_url }}" />
                    </picture>
                </figure>
            </div>
            <div class="col-9">
                <div class="card-body py-0">
                    <a class="text-decoration-none text-dark"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h6 class="fw-semibold fs-6 mb-1">{{ $post->title }}</h6>
                    </a>
                    <div class="card-text">
                        <small class="text-danger fw-normal">{{ $post->category->name }}</small>
                        <span class="mx-1">•</span>
                        <small> {{ $post->created_at->format('d M Y') }}</small>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif
