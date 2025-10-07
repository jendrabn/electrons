@extends('layouts.app')

@section('content')
    <section class="container blog-index-page">
        <div class="row">
            <div class="col-lg-8">
                @php
                    $currentCategory = null;
                    if (request()->routeIs('posts.category')) {
                        $catParam = request()->route('category');
                        $currentCategory = is_object($catParam)
                            ? $catParam
                            : \App\Models\Category::query()->where('slug', $catParam)->first();
                    }
                    $latestPost = $posts->first();
                    $latestTimestamp = $latestPost?->published_at ?? $latestPost?->created_at;
                    $latestUpdatedAtHuman = $latestTimestamp?->diffForHumans();
                    $blogHeroTitle =
                        request()->has('search') && trim(request('search')) !== ''
                            ? 'Blog'
                            : $title ?? ($currentCategory->name ?? 'Blog');
                    $blogHeroSubtitle =
                        $currentCategory?->description ??
                        'Temukan artikel, tutorial, dan insight seputar teknologi, pemrograman, dan produktivitas developer.';
                    $blogHeroBadge = $currentCategory?->name ?? 'Wawasan Terkini';
                @endphp

                {{-- Blog Header --}}
                <div class="mb-5 blog-header">
                    <div class="blog-hero-main d-flex flex-column align-items-start gap-3 flex-grow-1">
                        <span class="blog-hero-badge">
                            <i class="bi bi-journal-text"></i>
                            <span>{{ $blogHeroBadge }}</span>
                        </span>
                        <h1 class="fw-bold blog-hero-title mb-1">{{ $blogHeroTitle }}</h1>
                        <p class="text-muted blog-hero-sub mb-0">
                            {{ $blogHeroSubtitle }}
                        </p>
                    </div>
                </div>

                {{-- Search Results  --}}
                @if (request()->has('search') && trim(request('search')) !== '')
                    <section class="mb-4 search-results">
                        <h2 class="h5 mb-2">Hasil Pencarian</h2>
                        <p class="text-muted">
                            <i aria-hidden="true"
                               class="bi bi-search me-2"></i>
                            Ditemukan untuk kata kunci "<span class="fw-semibold">{{ request('search') }}</span>"
                        </p>
                    </section>
                @endif

                {{-- Post List --}}
                <div class="d-flex flex-column gap-3">
                    @forelse ($posts as $post)
                        <x-post.article :post="$post"
                                        variant="horizontal" />
                    @empty
                        <div class="text-center py-5">
                            <div class="mb-3">
                                <i class="bi bi-journal-x display-1 text-muted"></i>
                            </div>
                            <h2>No articles found</h2>
                            <p class="text-muted">
                                Please check back later for new articles.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Pagination --}}
                <div class="mt-5">
                    {{ $posts->links() }}
                </div>
            </div>
            <div class="col-lg-4">
                @include('frontpages.posts.partials._sidebar')
            </div>
        </div>
    </section>
@endsection
