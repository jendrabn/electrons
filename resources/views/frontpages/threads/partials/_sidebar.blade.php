<aside class="d-flex flex-column gap-3">
    {{-- Top Contributors --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 fw-bold">Top Kontributor</h5>
        </div>
        <div class="card-body">
            <div class="list-group list-group-flush">
                @foreach ($topContributors as $user)
                    <div class="list-group-item border-0 px-0">
                        <div class="d-flex align-items-center gap-2">
                            <img alt="{{ $user->name }}"
                                 class="rounded-circle"
                                 height="30"
                                 src="{{ $user->avatar_url }}"
                                 width="30">
                            <div>
                                <a class="fw-medium text-decoration-none small"
                                   href="{{ route('authors.show', $user->username) }}">{{ '@' . $user->username }}</a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

    {{-- Categories --}}
    <div class="card shadow-sm border-0">
        <div class="card-header bg-white">
            <h5 class="card-title mb-0 fw-bold">Tag</h5>
        </div>
        <div class="card-body">
            <div class="d-flex flex-wrap gap-2">
                @foreach ($tags as $tag)
                    @php
                        $isActiveCat = request('tag') == $tag->slug || request('tag') == $tag->id;
                        $query = array_merge(request()->query(), ['tag' => $tag->slug]);
                    @endphp
                    <x-thread.badge-tag :tag="$tag" />
                @endforeach
            </div>
        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

</aside>
