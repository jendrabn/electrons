@extends('layouts.app')

@section('content')
    <section class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-5">
                    <h1 class="fw-bold display-6 mb-1 d-inline-flex align-items-center">
                        {{ $title ?? 'Blog' }}
                    </h1>
                    <div class="text-muted fs-6">
                        Temukan artikel, tips, dan berita terbaru di sini.
                    </div>
                </div>

                @if (request()->has('search') && trim(request('search')) !== '')
                    <div class="mb-4">
                        <h2 class="h5 mb-2">Hasil Pencarian</h2>
                        <p class="text-muted">
                            <i class="bi bi-search me-2"></i>Ditemukan untuk kata kunci "<span
                                  class="fw-semibold">{{ request('search') }}</span>"
                        </p>
                    </div>
                @else
                    <h2 class="visually-hidden">Daftar Artikel Blog</h2>
                @endif

                <div class="d-flex flex-column gap-3">
                    @forelse ($posts as $post)
                        <article class="post-card bg-white rounded-4 p-3 p-md-4">
                            <div class="row g-3 g-md-4 align-items-start">
                                <div class="col-12 col-md-4">
                                    <a href="{{ route('posts.show', $post->slug) }}">
                                        <img alt="{{ $post->image_caption ?? $post->title }}"
                                             class="post-thumb"
                                             src="{{ $post->image_url }}">
                                    </a>
                                </div>
                                <div class="col-12 col-md-8">
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <a class="text-decoration-none"
                                           href="{{ route('posts.category', $post->category->slug) }}">
                                            <span class="text-uppercase category"
                                                  style="color: {{ $post->category->color ?? '#3B82F6' }}">{{ $post->category->name }}</span>
                                        </a>
                                    </div>
                                    <h3 class="h4 fw-bold post-title mb-2">
                                        <a class="text-decoration-none text-dark"
                                           href="{{ route('posts.show', $post->slug) }}">
                                            {{ $post->title }}
                                        </a>
                                    </h3>
                                    <!-- Author section -->
                                    <div class="d-flex align-items-center gap-2 mb-2">
                                        <a class="text-decoration-none"
                                           href="{{ route('posts.author', $post->user->id) }}">
                                            <img alt="{{ $post->user->name }}"
                                                 class="rounded-circle"
                                                 height="28"
                                                 src="{{ $post->user->avatar_url }}"
                                                 width="28">
                                        </a>
                                        <a class="text-decoration-none"
                                           href="{{ route('posts.author', $post->user->id) }}">
                                            <span class="meta">{{ str()->words($post->user->name, 2, '') }}</span>
                                        </a>
                                    </div>

                                    <!-- Meta information: timestamp left, engagement right -->
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="meta text-muted">{{ $post->created_at->diffForHumans() }}</span>
                                        <div class="d-flex align-items-center gap-3">
                                            <div class="text-muted small"><i
                                                   class="bi bi-heart me-1"></i>{{ number_format($post->likes_count ?? 0) }}
                                            </div>
                                            <div class="text-muted small"><i
                                                   class="bi bi-chat me-1"></i>{{ number_format($post->comments_count ?? 0) }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </article>
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-card-list display-1 text-muted"></i>
                            </div>
                            <h2>Belum ada artikel</h2>
                            <p class="text-muted">
                                Tidak ada artikel yang ditemukan.
                            </p>
                        </div>
                    @endforelse
                </div>

                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
            <div class="col-lg-4">
                @include('partials.sidebar')
            </div>
        </div>
    </section>
@endsection

@section('styles')
    <style>
        /* Sedikit penyesuaian tampilan */
        .post-card {
            transition: box-shadow .2s ease;
            border: 1px solid #eef1f4
        }

        .post-card:hover {
            box-shadow: 0 8px 24px rgba(0, 0, 0, .08)
        }

        .post-thumb {
            width: 100%;
            height: 180px;
            object-fit: cover;
            border-radius: .5rem
        }

        @media (min-width: 768px) {
            .post-thumb {
                height: 160px
            }
        }

        .category {
            letter-spacing: .04em;
            font-weight: 700
        }

        .post-title {
            line-height: 1.2
        }

        .meta {
            font-size: .95rem;
            color: #6c757d
        }

        .action i {
            font-size: 1rem
        }

        .action .btn {
            --bs-btn-padding-y: .25rem;
            --bs-btn-padding-x: .5rem
        }
    </style>
@endsection
