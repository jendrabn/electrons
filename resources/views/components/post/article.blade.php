@props(['post', 'variant' => 'vertical', 'showExcerpt' => false])

@php
    $variant = $variant ?? 'vertical';
    $showExcerpt = filter_var($showExcerpt, FILTER_VALIDATE_BOOLEAN);
@endphp

{{-- Vertical Variant --}}
@if ($variant === 'vertical')
    <article class="card border-0 h-100 w-100 bg-transparent post-article">
        <div class="row g-0">
            <div class="col-3 col-md-12 mb-md-3">
                <div class="position-relative">
                    <a class="text-decoration-none"
                       href="{{ route('posts.show', $post->slug) }}">
                        <!-- Gunakan bg-body-secondary sebagai placeholder adaptif -->
                        <div class="bg-body-secondary rounded-3 overflow-hidden w-100 ratio ratio-16x9">
                            <img alt="{{ $post->image_caption }}"
                                 class="post-thumb"
                                 loading="lazy"
                                 src="{{ $post->image_url }}" />
                        </div>

                        <!-- Komponen badge kategori kamu yang sudah adaptif -->
                        <x-post.badge-category :category="$post->category"
                                               class="d-none d-md-block" />
                    </a>
                </div>
            </div>

            <div class="col-9 col-md-12">
                <div class="card-body py-0 pe-0 px-md-0">
                    <a class="text-decoration-none hover-link"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h3
                            class="card-title fs-6 fs-lg-5 fw-bold lh-sm mb-0 mb-lg-2 line-clamp-2 text-body-emphasis text-break text-wrap">
                            {{ $post->title }}
                        </h3>
                    </a>

                    @if ($showExcerpt && !empty($post->excerpt))
                        <p class="mb-2 line-clamp-3 text-body-secondary text-break text-wrap">{!! $post->excerpt !!}
                        </p>
                    @endif

                    <div class="d-flex align-items-center gap-2 mt-2">
                        <img alt="{{ $post->user->name }}"
                             class="rounded-circle object-fit-cover d-none d-md-block"
                             height="25"
                             src="{{ $post->user->avatar_url }}"
                             width="25" />
                        <a class="text-decoration-none fw-semibold text-body-secondary hover-link-muted"
                           href="{{ route('authors.show', $post->user->username) }}"
                           rel="author">
                            {{ str()->words($post->user->name, 2, '') }}
                        </a>
                        <span class="mx-1">•</span>
                        <time class="small text-body-secondary"
                              datetime="{{ $post->created_at->toIso8601String() }}">
                            {{ $post->created_at->diffForHumans() }}
                        </time>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Horizontal Variant --}}
@if ($variant === 'horizontal')
    <article class="post-card rounded-4 p-2 p-md-3 bg-body post-article">
        <div class="row g-3 g-md-4 align-items-center">
            <div class="col-4">
                <a href="{{ route('posts.show', $post->slug) }}"
                   title="{{ $post->title }}">
                    <!-- placeholder adaptif ganti bg-gray-300 -->
                    <div class="m-0 bg-body-secondary rounded-3 overflow-hidden w-100 ratio ratio-16x9">
                        <img alt="{{ $post->image_caption }}"
                             class="post-thumb"
                             loading="lazy"
                             src="{{ $post->image_url }}">
                    </div>
                </a>
            </div>

            <div class="col-8">
                {{-- Category Badge --}}
                <div class="d-flex align-items-center gap-2 mb-2">
                    <x-post.badge-category :category="$post->category"
                                           :inline="true" />
                </div>

                <h3 class="h4 fw-bold post-title mb-2">
                    <a class="text-decoration-none hover-link text-break text-wrap text-body-emphasis d-block"
                       href="{{ route('posts.show', $post->slug) }}"
                       title="{{ $post->title }}">
                        {{ $post->title }}
                    </a>
                </h3>

                @if ($showExcerpt && !empty($post->excerpt))
                    <p class="mb-2 text-break text-wrap text-body-secondary">{!! $post->excerpt !!}</p>
                @endif

                <div class="d-flex align-items-center gap-2 mb-2">
                    <a class="text-decoration-none"
                       href="{{ route('authors.show', $post->user->username) }}"
                       rel="author"
                       title="Penulis: {{ $post->user->name }}">
                        <img alt="{{ $post->user->name }}"
                             class="rounded-circle object-fit-cover"
                             height="25"
                             loading="lazy"
                             src="{{ $post->user->avatar_url }}"
                             width="25">
                    </a>
                    <a class="text-decoration-none small fw-semibold text-body-secondary hover-link-muted"
                       href="{{ route('authors.show', $post->user->username) }}"
                       rel="author"
                       title="Profil Penulis">
                        <span>{{ str()->words($post->user->name, 2, '') }}</span>
                    </a>
                </div>

                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-flex align-items-center gap-2">
                        <time class="small text-body-secondary"
                              datetime="{{ $post->created_at->toIso8601String() }}">
                            {{ $post->created_at->diffForHumans() }}
                        </time>
                        @if ($post->updated_at && $post->updated_at->gt($post->created_at))
                            <time class="visually-hidden"
                                  datetime="{{ $post->updated_at->toIso8601String() }}"
                                  itemprop="dateModified">
                                {{ $post->updated_at->toFormattedDateString() }}
                            </time>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        <div class="small text-body-secondary">
                            <i aria-hidden="true"
                               class="bi bi-heart me-1"></i>{{ number_format($post->likes_count ?? 0) }}
                        </div>
                        <div class="small text-body-secondary">
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
    <article class="card border-0 bg-transparent post-article">
        <div class="row g-2 align-items-center">
            <div class="col-3">
                <a class="d-block ratio ratio-16x9 rounded-2 overflow-hidden bg-body-secondary"
                   href="{{ route('posts.show', $post->slug) }}">
                    <img alt="{{ $post->image_caption }}"
                         class="img-fluid object-fit-cover"
                         loading="lazy"
                         src="{{ $post->image_url }}">
                </a>
            </div>

            <div class="col-9">
                <div class="card-body py-0">
                    <a class="text-decoration-none d-block"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h6 class="fw-semibold mb-1 line-clamp-2 text-break text-wrap text-body-emphasis">
                            {{ $post->title }}
                        </h6>
                    </a>

                    @if ($showExcerpt && !empty($post->excerpt))
                        <p class="small mb-1 line-clamp-2 text-break text-wrap text-body-secondary">
                            {!! $post->excerpt !!}
                        </p>
                    @endif

                    <div class="small text-body-secondary">
                        <span class="fw-semibold">{{ str()->words($post->user->name, 2, '') }}</span>
                        <span class="mx-1">•</span>
                        <time datetime="{{ $post->created_at->toIso8601String() }}">
                            {{ $post->created_at->diffForHumans() }}
                        </time>
                    </div>
                </div>
            </div>
        </div>
    </article>

@endif

@push('styles')
    <style>
        /* Scoped styles for post component to avoid duplication in custom.scss */
        .post-article {
            --post-thumb-radius: .5rem;
        }

        .post-article .post-thumb {
            width: 100% !important;
            height: 100% !important;
            object-fit: cover;
            border-radius: var(--post-thumb-radius);
            transition: transform .2s ease;
        }

        .post-article .post-thumb:hover {
            transform: scale(1.12);
        }

        .post-article a:focus-visible {
            outline: 0;
            box-shadow: 0 0 0 .25rem rgba(var(--bs-primary-rgb), .18);
            border-radius: .375rem;
        }

        /* Card hover subtle lift */
        .post-article.post-card {
            transition: box-shadow .2s ease, transform .25s ease;
            transform: translateY(0);
        }

        @media (prefers-reduced-motion: no-preference) {
            .post-article.post-card:hover {
                transform: translateY(-3px);
                box-shadow: 0 10px 26px rgba(0, 0, 0, .06);
            }
        }

        /* Small utility tweaks for compact layout */
        @media (min-width: 768px) {
            .post-article .post-thumb {
                height: 160px;
            }
        }
    </style>
@endpush
