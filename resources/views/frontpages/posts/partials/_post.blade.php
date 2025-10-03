@props(['post', 'type' => 'vertical'])

{{-- Vertical Type --}}
@if ($type == 'vertical')
    <article class="post-item card border-0 h-100 w-100"
             itemscope
             itemtype="https://schema.org/BlogPosting">
        <div class="row g-0">
            <div class="col-3 col-lg-12">
                <div class="position-relative">
                    <a aria-label="Buka artikel: {{ $post->title }}"
                       href="{{ route('posts.show', $post->slug) }}">
                        <figure class="post-item-image bg-gray-200 rounded-3 overflow-hidden w-100 ratio ratio-16x9">
                            <picture>
                                <img alt="{{ $post->image_caption }}"
                                     class="h-100 w-100 object-fit-cover"
                                     itemprop="image"
                                     loading="lazy"
                                     src="{{ $post->image_url }}" />
                            </picture>
                        </figure>

                        <x-badge-category :color="$post->category->color"
                                          :name="$post->category->name" />
                    </a>
                </div>
            </div>
            <div class="col-9 col-lg-12">
                <div class="card-body py-0 pe-0 px-lg-0">
                    <a class="text-decoration-none text-dark"
                       href="{{ route('posts.show', $post->slug) }}">
                        <h3 class="card-title fs-6 fs-lg-5 fw-bold lh-sm mb-0 mb-lg-2 line-clamp-2"
                            itemprop="headline">
                            {{ $post->title }}
                        </h3>
                    </a>
                    <div class="d-flex align-items-center gap-2 mt-2"
                         itemprop="author"
                         itemscope
                         itemtype="https://schema.org/Person">
                        <img alt="{{ $post->user->name }}"
                             class="rounded-circle object-fit-cover"
                             src="{{ $post->user->avatar_url }}"
                             style="width: 28px; height: 28px;" />
                        <a class="text-decoration-none text-muted fw-semibold"
                           href="{{ route('posts.author', $post->user->id) }}"
                           itemprop="name"
                           rel="author">
                            {{ str()->words($post->user->name, 2, '') }}
                        </a>
                        <span class="mx-1">•</span>
                        <time class="text-muted small"
                              datetime="{{ $post->created_at->toIso8601String() }}"
                              itemprop="datePublished">
                            {{ $post->created_at->format('d M Y') }}
                        </time>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Horizontal Type --}}
@if ($type == 'horizontal')
    <article class="post-card bg-white rounded-4 p-2 p-md-3"
             itemscope
             itemtype="https://schema.org/BlogPosting"
             role="listitem">
        <meta content="{{ route('posts.show', $post->slug) }}"
              itemprop="mainEntityOfPage">
        <div class="row g-3 g-md-4 align-items-start">
            {{-- Thumbnail artikel --}}
            <div class="col-12 col-md-4">
                <a class="d-block overflow-hidden rounded-3"
                   href="{{ route('posts.show', $post->slug) }}"
                   title="{{ $post->title }}">
                    <figure class="m-0">
                        <img alt="{{ $post->image_caption ?? $post->title }}"
                             class="post-thumb"
                             decoding="async"
                             itemprop="image"
                             loading="lazy"
                             sizes="(min-width: 768px) 33vw, 100vw"
                             src="{{ $post->image_url }}">
                        @if (!empty($post->image_caption))
                            <figcaption class="visually-hidden">{{ $post->image_caption }}</figcaption>
                        @endif
                    </figure>
                </a>
            </div>

            {{-- Konten artikel: kategori, judul, penulis, meta --}}
            <div class="col-12 col-md-8">
                {{-- Kategori artikel --}}
                <div class="d-flex align-items-center gap-2 mb-2">
                    <a class="text-decoration-none"
                       href="{{ route('posts.category', $post->category->slug) }}"
                       rel="tag"
                       title="Kategori: {{ $post->category->name }}">
                        <span class="text-uppercase category"
                              style="color: {{ $post->category->color ?? '#3B82F6' }}">{{ $post->category->name }}</span>
                    </a>
                    <meta content="{{ $post->category->name }}"
                          itemprop="articleSection">
                </div>

                {{-- Judul artikel --}}
                <h3 class="h4 fw-bold post-title mb-2"
                    itemprop="headline">
                    <a class="text-decoration-none text-dark"
                       href="{{ route('posts.show', $post->slug) }}"
                       title="{{ $post->title }}">
                        {{ $post->title }}
                    </a>
                </h3>

                {{-- Penulis artikel --}}
                <div class="d-flex align-items-center gap-2 mb-2"
                     itemprop="author"
                     itemscope
                     itemtype="https://schema.org/Person">
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
                        <span itemprop="name">{{ str()->words($post->user->name, 2, '') }}</span>
                    </a>
                </div>

                {{-- Informasi meta: waktu terbit & interaksi --}}
                <div class="d-flex justify-content-between align-items-center">
                    <div class="d-inline-flex align-items-center gap-2">
                        <time class="text-muted small"
                              datetime="{{ $post->created_at->toIso8601String() }}"
                              itemprop="datePublished">{{ $post->created_at->diffForHumans() }}</time>
                        @if ($post->updated_at && $post->updated_at->gt($post->created_at))
                            <time class="visually-hidden"
                                  datetime="{{ $post->updated_at->toIso8601String() }}"
                                  itemprop="dateModified">{{ $post->updated_at->toFormattedDateString() }}</time>
                        @endif
                    </div>
                    <div class="d-flex align-items-center gap-3">
                        {{-- Jumlah suka (LikeAction) --}}
                        <div aria-label="{{ number_format($post->likes_count ?? 0) }} suka"
                             class="text-muted small">
                            <i aria-hidden="true"
                               class="bi bi-heart me-1"></i>{{ number_format($post->likes_count ?? 0) }}
                            <span class="visually-hidden"
                                  itemprop="interactionStatistic"
                                  itemscope
                                  itemtype="https://schema.org/InteractionCounter">
                                <meta content="https://schema.org/LikeAction"
                                      itemprop="interactionType">
                                <meta content="{{ (int) ($post->likes_count ?? 0) }}"
                                      itemprop="userInteractionCount">
                            </span>
                        </div>
                        {{-- Jumlah komentar (CommentAction) --}}
                        <div aria-label="{{ number_format($post->comments_count ?? 0) }} komentar"
                             class="text-muted small">
                            <i aria-hidden="true"
                               class="bi bi-chat me-1"></i>{{ number_format($post->comments_count ?? 0) }}
                            <span class="visually-hidden"
                                  itemprop="interactionStatistic"
                                  itemscope
                                  itemtype="https://schema.org/InteractionCounter">
                                <meta content="https://schema.org/CommentAction"
                                      itemprop="interactionType">
                                <meta content="{{ (int) ($post->comments_count ?? 0) }}"
                                      itemprop="userInteractionCount">
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </article>
@endif

{{-- Sidebar Type --}}
@if ($type == 'sidebar')
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
