@props(['post', 'variant' => 'vertical', 'showExcerpt' => false])

@php
    $variant = $variant ?? 'vertical';
    $showExcerpt = filter_var($showExcerpt, FILTER_VALIDATE_BOOLEAN);
@endphp

{{-- Vertical Variant --}}
@if ($variant === 'vertical')
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

                        <x-post.badge-category :category="$post->category" />
                    </a>
                </div>
            </div>
            <div class="col-9 col-lg-12">
                <div class="card-body py-0 pe-0 px-lg-0">
                    <a class="text-decoration-none text-dark"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h3 class="card-title fs-6 fs-lg-5 fw-bold lh-sm mb-0 mb-lg-2 line-clamp-2 hover-link">
                            {{ $post->title }}
                        </h3>
                    </a>
                    @if ($showExcerpt && !empty($post->excerpt))
                        <p class="text-muted mb-2 line-clamp-3">{!! $post->excerpt !!}</p>
                    @endif
                    <div class="d-flex align-items-center gap-2 mt-2">
                        <img alt="{{ $post->user->name }}"
                             class="rounded-circle object-fit-cover"
                             src="{{ $post->user->avatar_url }}"
                             style="width: 28px; height: 28px;" />
                        <a class="text-decoration-none text-muted fw-semibold"
                           href="{{ route('posts.author', $post->user->id) }}"
                           rel="author">
                            {{ str()->words($post->user->name, 2, '') }}
                        </a>
                        <span class="mx-1">•</span>
                        <time class="text-muted small"
                              datetime="{{ $post->created_at->toIso8601String() }}">
                            {{ $post->created_at->format('d M Y') }}
                        </time>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Horizontal Variant --}}
@if ($variant === 'horizontal')
    <article class="post-card bg-white rounded-4 p-2 p-md-3">
        <meta content="{{ route('posts.show', $post->slug) }}">
        <div class="row g-3 g-md-4 align-items-start">
            <div class="col-12 col-md-4">
                <a class="d-block overflow-hidden rounded-3"
                   href="{{ route('posts.show', $post->slug) }}"
                   title="{{ $post->title }}">
                    <figure class="m-0">
                        <img alt="{{ $post->image_caption ?? $post->title }}"
                             class="post-thumb"
                             decoding="async"
                             loading="lazy"
                             sizes="(min-width: 768px) 33vw, 100vw"
                             src="{{ $post->image_url }}">
                        @if (!empty($post->image_caption))
                            <figcaption class="visually-hidden">{{ $post->image_caption }}</figcaption>
                        @endif
                    </figure>
                </a>
            </div>

            <div class="col-12 col-md-8">
                <div class="d-flex align-items-center gap-2 mb-2">
                    <x-post.badge-category :category="$post->category"
                                           :inline="true" />
                    <meta content="{{ $post->category->name }}">
                </div>

                <h3 class="h4 fw-bold post-title mb-2">
                    <a class="text-decoration-none text-dark hover-link"
                       href="{{ route('posts.show', $post->slug) }}"
                       title="{{ $post->title }}">
                        {{ $post->title }}
                    </a>
                </h3>

                @if ($showExcerpt && !empty($post->excerpt))
                    <p class="text-muted mb-2">{!! $post->excerpt !!}</p>
                @endif

                <div class="d-flex align-items-center gap-2 mb-2">
                    <a class="text-decoration-none"
                       href="{{ route('authors.show', $post->user->username) }}"
                       rel="author"
                       title="Penulis: {{ $post->user->name }}">
                        <img alt="{{ $post->user->name }}"
                             class="rounded-circle"
                             decoding="async"
                             height="25"
                             loading="lazy"
                             src="{{ $post->user->avatar_url }}"
                             width="25">
                    </a>
                    <a class="text-decoration-none small text-muted fw-semibold"
                       href="{{ route('authors.show', $post->user->username) }}"
                       rel="author"
                       title="Profil Penulis">
                        <span>{{ str()->words($post->user->name, 2, '') }}</span>
                    </a>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-flex align-items-center gap-2">
                        <time class="text-muted small"
                              datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
                        @if ($post->updated_at && $post->updated_at->gt($post->created_at))
                            <time class="visually-hidden"
                                  datetime="{{ $post->updated_at->toIso8601String() }}"
                                  itemprop="dateModified">{{ $post->updated_at->toFormattedDateString() }}</time>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="text-muted small">
                            <i aria-hidden="true"
                               class="bi bi-heart me-1"></i>{{ number_format($post->likes_count ?? 0) }}
                        </div>
                        <div class="text-muted small">
                            <i aria-hidden="true"
                               class="bi bi-chat me-1"></i>{{ number_format($post->comments_count ?? 0) }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Compact Variant (uses sidebar layout as compact) --}}
@if ($variant === 'compact')
    <article class="card border-0">
        <div class="row g-2 align-items-center">
            <div class="col-3">
                <a class="d-block ratio ratio-16x9 rounded-2 overflow-hidden"
                   href="{{ route('posts.show', $post->slug) }}">
                    <img alt="{{ $post->image_caption ?? $post->title }}"
                         class="img-fluid object-fit-cover"
                         loading="lazy"
                         src="{{ $post->image_url }}">
                </a>
            </div>
            <div class="col-9">
                <div class="card-body py-0">
                    <a class="text-decoration-none text-dark d-block"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h6 class="fw-semibold mb-1 line-clamp-2">{{ $post->title }}</h6>
                    </a>
                    @if ($showExcerpt && !empty($post->excerpt))
                        <p class="small text-muted mb-1 line-clamp-2">{!! $post->excerpt !!}</p>
                    @endif
                    <div class="small text-muted">
                        <span class="fw-semibold">{{ str()->words($post->user->name, 2, '') }}</span>
                        <span class="mx-1">•</span>
                        <time
                              datetime="{{ $post->created_at->toIso8601String() }}">{{ $post->created_at->diffForHumans() }}</time>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif
