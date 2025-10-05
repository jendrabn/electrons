<aside>
    <div class="d-flex flex-column gap-3">

        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <h5 class="mb-3 fw-bold">Terpopuler</h5>
                <ul class="list-unstyled mb-0 popular-post-list">
                    @forelse ($popularPosts as $post)
                        <li class="{{ !$loop->last ? 'mb-3' : '' }}">
                            <x-post.article :post="$post"
                                            variant="compact" />
                        </li>
                    @empty
                        <li class="mb-0">
                            <div class="rounded-3 p-4 bg-light text-muted text-center">
                                Tidak Ada Blog Populer
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        @include('partials.ads.display-responsive', ['slot' => '8485643721'])

        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <h5 class="mb-3 fw-bold">Terbaru</h5>
                <ul class="list-unstyled mb-0 latest-post-list">
                    @forelse ($recentPosts as $post)
                        <li class="{{ !$loop->last ? 'mb-3' : '' }}">
                            <x-post.article :post="$post"
                                            variant="compact" />
                        </li>
                    @empty
                        <li class="mb-0">
                            <div class="rounded-3 p-4 bg-light text-muted text-center">
                                Tidak Ada Blog Terbaru
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        @include('partials.ads.display-responsive', ['slot' => '8485643721'])

        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <h5 class="mb-3 fw-bold">Kategori</h5>
                @php
                    /** @var \Illuminate\Support\Collection $categories */
                    $visibleCategories = $categories
                        ->map(function ($cat) {
                            $cat->published_posts_count = $cat->posts()->published()->count();
                            return $cat;
                        })
                        ->filter(fn($cat) => $cat->published_posts_count > 0);
                @endphp
                <ul class="list-group list-group-flush mb-0 category-list">
                    @forelse ($visibleCategories as $category)
                        <li class="list-group-item px-0 py-2 bg-transparent">
                            <a class="d-flex align-items-center justify-content-between text-decoration-none px-2 py-2 rounded-2"
                               href="{{ route('posts.category', $category->slug) }}">
                                <span class="d-flex align-items-center gap-2">
                                    <span class="rounded-circle"
                                          style="width:.5rem;height:.5rem;background-color: {{ $category->color ?? '#0d6efd' }};"></span>
                                    <span class="text-dark fw-semibold">{{ $category->name }}</span>
                                </span>
                                <span
                                      class="badge bg-light text-secondary">{{ $category->published_posts_count }}</span>
                            </a>
                        </li>
                    @empty
                        <li class="list-group-item px-0 py-2 bg-transparent">
                            <div class="rounded-3 p-4 bg-light text-muted text-center">
                                Tidak Ada Kategori
                            </div>
                        </li>
                    @endforelse
                </ul>
            </div>
        </div>

        @include('partials.ads.display-responsive', ['slot' => '8485643721'])

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Tag</h5>
                <div class="d-flex flex-wrap gap-2 tag-list">
                    @forelse ($tags as $tag)
                        <x-post.badge-tag :tag="$tag" />
                    @empty
                        <div class="rounded-3 p-4 bg-light text-muted text-center">
                            Tidak Ada Tag
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

        @include('partials.ads.display-responsive', ['slot' => '8485643721'])
    </div>
</aside>
