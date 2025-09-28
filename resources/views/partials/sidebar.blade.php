<aside>
    <div class="d-flex flex-column gap-3">

        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <h5 class="mb-3 fw-bold">Terpopuler</h5>
                <ul class="list-unstyled mb-0">
                    @foreach ($popularPosts as $post)
                        <li class="mb-3">
                            <x-post-item :post="$post"
                                         type="mini" />
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- @include('partials.ads.display-responsive', ['slot' => '8485643721']) --}}

        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <h5 class="mb-3 fw-bold">Terbaru</h5>
                <ul class="list-unstyled mb-0">
                    @foreach ($recentPosts as $post)
                        <li class="mb-3">
                            <x-post-item :post="$post"
                                         type="mini" />
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- @include('partials.ads.display-responsive', ['slot' => '8485643721']) --}}

        <div class="card shadow-sm border-0">
            <div class="card-body pb-2">
                <h5 class="mb-3 fw-bold">Kategori</h5>
                <ul class="list-unstyled mb-0">
                    @foreach ($categories as $category)
                        <li class="mb-2">
                            <a class="text-decoration-none text-dark d-flex justify-content-between align-items-center"
                               href="{{ route('posts.category', $category->slug) }}">
                                <span>{{ $category->name }}</span>
                                <span class="badge bg-light text-secondary">{{ $category->posts_count }}</span>
                            </a>
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

        {{-- @include('partials.ads.display-responsive', ['slot' => '8485643721']) --}}

        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="mb-3 fw-bold">Tag</h5>
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        <x-badge-tag :tag="$tag" />
                    @endforeach
                </div>
            </div>
        </div>

        @include('partials.ads.display-responsive', ['slot' => '8485643721'])
    </div>
</aside>
