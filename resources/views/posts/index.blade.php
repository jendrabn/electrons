@extends('layouts.app')

@section('content')
    {{-- Halaman indeks blog: container utama dan metadata Blog schema --}}
    <section aria-label="Blog Index"
             class="container"
             itemscope
             itemtype="https://schema.org/Blog">
        <div class="row">
            <div class="col-lg-8">
                {{-- Header: judul halaman dan deskripsi kategori/umum --}}
                @php
                    /** Menentukan kategori saat ini (jika rute kategori aktif) */
                    $currentCategory = null;
                    if (request()->routeIs('posts.category')) {
                        $catParam = request()->route('category');
                        $currentCategory = is_object($catParam)
                            ? $catParam
                            : \App\Models\Category::query()->where('slug', $catParam)->first();
                    }
                @endphp
                <header class="mb-5 text-center text-md-start blog-header">
                    <h1 class="fw-bold display-5 mb-2 d-inline-flex align-items-center"
                        itemprop="name">
                        {{ request()->has('search') && trim(request('search')) !== '' ? 'Blog' : $title ?? ($currentCategory->name ?? 'Blog') }}
                    </h1>
                    <p class="text-muted lead blog-subtitle mb-0"
                       itemprop="description">
                        {{ $currentCategory?->description ?? 'Temukan artikel, tutorial, dan insight seputar teknologi, pemrograman, dan produktivitas developer.' }}
                    </p>
                </header>

                {{-- Pemberitahuan hasil pencarian (SEO-friendly: H2 sesuai konteks) --}}
                @if (request()->has('search') && trim(request('search')) !== '')
                    <section aria-label="Hasil Pencarian"
                             class="mb-4 search-results">
                        <h2 class="h5 mb-2">Hasil Pencarian</h2>
                        <p class="text-muted">
                            <i aria-hidden="true"
                               class="bi bi-search me-2"></i>
                            Ditemukan untuk kata kunci "<span class="fw-semibold">{{ request('search') }}</span>"
                        </p>
                    </section>
                @else
                    <h2 class="visually-hidden">Daftar Artikel Blog</h2>
                @endif

                {{-- Daftar artikel --}}
                <div aria-label="Daftar Artikel"
                     class="d-flex flex-column gap-3"
                     role="list">
                    @forelse ($posts as $post)
                        {{-- Kartu artikel (schema.org BlogPosting) --}}
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
                                           href="{{ route('posts.author', $post->user->id) }}"
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
                                           href="{{ route('posts.author', $post->user->id) }}"
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
                    @empty
                        {{-- Keadaan kosong: tidak ada artikel --}}
                        <div class="text-center py-5">
                            <div aria-hidden="true"
                                 class="mb-3">
                                <i class="bi bi-journal-x display-1 text-muted"></i>
                            </div>
                            <h2>Belum ada artikel</h2>
                            <p class="text-muted">
                                Tidak ada artikel yang ditemukan.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Navigasi pagination --}}
                <nav aria-label="Pagination"
                     class="mt-4">
                    {{ $posts->links() }}
                </nav>
            </div>
            {{-- Sidebar: konten tambahan, populer, terbaru, kategori, tag --}}
            <div class="col-lg-4">
                @include('partials.sidebar')
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        /* ===== Header Blog ===== */
        .blog-header {
            padding-top: .25rem;
            padding-bottom: .25rem;
        }

        .blog-header h1 {
            letter-spacing: .01em;
        }

        .blog-subtitle {
            max-width: 65ch;
            line-height: 1.6;
        }

        /* ===== Kartu Artikel ===== */
        .post-card {
            transition: box-shadow .2s ease, transform .25s ease;
            transform: translateY(0);
            will-change: transform, box-shadow;
            border: 1px solid #eef1f4;
        }

        .post-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08);
        }

        @media (prefers-reduced-motion: no-preference) {
            .post-card:hover {
                animation: postCardHoverBob .8s ease-in-out infinite alternate;
            }
        }

        @keyframes postCardHoverBob {
            0% {
                transform: translateY(-2px);
            }

            50% {
                transform: translateY(-4px);
            }

            100% {
                transform: translateY(-2px);
            }
        }

        /* ===== Thumbnail Artikel ===== */
        .post-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: .5rem;
            transition: transform 0.2s ease;
        }

        .post-thumb:hover {
            transform: scale(1.03);
        }

        @media (min-width: 768px) {
            .post-thumb {
                height: 160px;
            }
        }

        /* ===== Badge Kategori ===== */
        .category {
            letter-spacing: .04em;
            font-weight: 700;
        }

        /* ===== Judul Artikel (2 baris, ellipsis) ===== */
        .post-title {
            line-height: 1.2;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
            text-overflow: ellipsis;
            max-height: 2.4em;
        }

        /* ===== Aksi (ikon, tombol) ===== */
        .action i {
            font-size: 1rem;
        }

        .action .btn {
            --bs-btn-padding-y: .25rem;
            --bs-btn-padding-x: .5rem;
        }
    </style>
@endsection
