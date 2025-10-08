<aside class="d-flex flex-column gap-3">
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">Terpopuler</h5>
        </div>
        <div class="card-body">
            @if ($popularPosts->isEmpty())
                <div class="rounded-3 p-4 bg-body-tertiary text-body-secondary text-center">
                    -- No Popular Posts --
                </div>
            @else
                <ul class="list-unstyled mb-0 popular-post-list">
                    @foreach ($popularPosts as $post)
                        <li class="{{ !$loop->last ? 'mb-3' : '' }}">
                            <x-post.article :post="$post"
                                            role="listitem"
                                            variant="compact" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">Terbaru</h5>
        </div>
        <div class="card-body">
            @if ($recentPosts->isEmpty())
                <div class="rounded-3 p-4 bg-body-tertiary text-body-secondary text-center">
                    -- No Recent Posts --
                </div>
            @else
                <ul class="list-unstyled mb-0 latest-post-list">
                    @foreach ($recentPosts as $post)
                        <li class="{{ !$loop->last ? 'mb-3' : '' }}">
                            <x-post.article :post="$post"
                                            role="listitem"
                                            variant="compact" />
                        </li>
                    @endforeach
                </ul>
            @endif
        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">Kategori</h5>
        </div>
        <div class="card-body">
            @php
                $visibleCategories = $categories
                    ->map(function ($cat) {
                        $cat->published_posts_count = $cat->posts()->published()->count();
                        return $cat;
                    })
                    ->filter(fn($cat) => $cat->published_posts_count > 0);
            @endphp

            @if ($visibleCategories->isEmpty())
                <div class="rounded-3 p-4 bg-body-tertiary text-body-secondary text-center">
                    -- No Categories --
                </div>
            @else
                <ul class="list-group list-group-flush mb-0 category-list">
                    @foreach ($visibleCategories as $category)
                        <li class="list-group-item px-0 py-2 bg-transparent">
                            <a class="d-flex align-items-center justify-content-between text-decoration-none px-2 py-2 rounded-2 focus-ring"
                               href="{{ route('posts.category', $category->slug) }}"
                               style="--bs-focus-ring-color: rgba(var(--bs-primary-rgb), .25);">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle"
                                          style="width:.5rem;height:.5rem;background-color: {{ $category->color ?? '#0d6efd' }};"></span>
                                    <span class="text-body-emphasis fw-semibold">{{ $category->name }}</span>
                                </span>

                                <span class="badge bg-body-tertiary text-body-secondary border">
                                    {{ $category->published_posts_count }}
                                </span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            @endif

        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">Tag</h5>
        </div>
        <div class="card-body">
            @if ($tags->isEmpty())
                <div class="rounded-3 p-4 bg-body-tertiary text-body-secondary text-center">
                    -- No Tags --
                </div>
            @else
                <div class="d-flex flex-wrap gap-2 tag-list">
                    @foreach ($tags as $tag)
                        <x-post.badge-tag :tag="$tag" />
                    @endforeach
                </div>
            @endif

        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])
</aside>

@push('styles')
    <style>
        .category-list a:hover {
            background-color: var(--bs-body-secondary-bg);
        }

        .category-list a:focus-visible {
            outline: 0;
        }
    </style>
@endpush
