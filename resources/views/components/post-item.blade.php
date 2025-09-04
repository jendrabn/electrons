@props(['post', 'type' => 'vertical'])

{{-- Vertical Type --}}
@if ($type == 'vertical')
    <article class="post-item card border-0 h-100 w-100">
        <div class="row g-0">
            <div class="col-3 col-lg-12">
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

                        <x-badge-category :name="$post->category->name" />
                    </a>
                </div>
            </div>
            <div class="col-9 col-lg-12">
                <div class="card-body py-0 pe-0 px-lg-0">
                    <a class="text-decoration-none text-dark"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h5 class="card-title fs-6 fs-lg-5 fw-semibold mb-0 mb-lg-2 line-clamp-2">
                            {{ $post->title }}
                        </h5>
                    </a>
                    <p class="card-text d-none d-lg-block mb-1">{{ $post->excerpt }}</p>
                    <p class="card-text small text-muted">
                        <a class="text-decoration-none text-muted fw-semibold"
                           href="{{ route('posts.author', $post->user->id) }}">
                            {{ str()->words($post->user->name, 2, '') }}
                        </a>
                        <span class="mx-1">•</span>
                        <span>{{ $post->created_at->format('d M Y') }}</span>
                        <span class="mx-1 d-none d-lg-inline-block">•</span>
                        <span class="d-none d-lg-inline-block">{{ $post->min_read }} min
                            read</span>
                    </p>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Horizontal Type --}}
@if ($type == 'horizontal')
    <article class="post-item card border-0 h-100 w-100">
        <div class="row g-0">
            <div class="col-3">
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
                        <x-badge-category :name="$post->category->name" />
                    </a>
                </div>
            </div>
            <div class="col-9">
                <div class="card-body py-0 pe-0">
                    <a class="text-decoration-none"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h5 class="card-title text-dark fw-semibold line-clamp-2 fs-6 fs-lg-5">{{ $post->title }}</h5>
                    </a>
                    <p class="card-text text-muted d-none d-lg-block mb-1">{{ $post->excerpt }}</p>
                    <p class="card-text small text-muted">
                        <a class="text-decoration-none fw-semibold text-muted"
                           href="{{ route('posts.author', $post->user->id) }}">
                            {{ str()->words($post->user->name, 2, '') }}
                        </a>
                        <span class="mx-1">•</span>
                        <span>{{ $post->created_at->format('d M Y') }}</span>
                        <span class="mx-1 d-none d-lg-inline-block">•</span>
                        <span class="d-none d-lg-inline-block">{{ $post->min_read }} min read</span>
                    </p>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Minimalist Type --}}
@if ($type == 'mini')
    <article class="card border-0 h-100 w-100">
        <div class="row g-0">
            <div class="col-3">
                <figure class="w-100 bg-gray-200 ratio ratio-16x9 m-0 overflow-hidden rounded-2">
                    <picture>
                        <img alt="{{ $post->image_caption }}"
                             class="h-100 w-100 object-fit-cover"
                             loading="lazy"
                             src="{{ $post->image_url }}" />
                    </picture>
                </figure>
            </div>
            <div class="col-9">
                <div class="card-body py-0 pe-0">
                    <a class="text-decoration-none"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h6 class="text-dark fw-semibold mb-1 line-clamp-2">{{ $post->title }}</h6>
                    </a>
                    <div class="card-text small text-muted">
                        <span class="fw-semibold">{{ str()->words($post->user->name, 2, '') }}</span>
                        <span class="mx-1">•</span>
                        <span>{{ $post->created_at->diffForHumans() }}</span>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif
