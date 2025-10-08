<aside class="d-flex flex-column gap-3">
    {{-- Top Contributors --}}
    <div class="card shadow-sm border-0">
        <div class="card-header border-bottom-0">
            <h5 class="card-title mb-0 fw-bold">Top Kontributor</h5>
        </div>
        <div class="card-body">
            @if ($topContributors->isEmpty())
                <div class="rounded-3 p-4 bg-body-tertiary text-body-secondary text-center">
                    -- No Top Contributors --
                </div>
            @else
                <div class="list-group list-group-flush">
                    @foreach ($topContributors as $user)
                        <div class="list-group-item border-0 px-0">
                            <div class="d-flex align-items-center gap-2">
                                <a aria-label="Profil {{ $user->name }}"
                                   class="flex-shrink-0"
                                   href="{{ route('authors.show', $user->username) }}">
                                    <img alt="{{ $user->name }}"
                                         class="rounded-circle border"
                                         height="30"
                                         loading="lazy"
                                         src="{{ $user->avatar_url }}"
                                         style="object-fit:cover"
                                         width="30">
                                </a>

                                <a class="fw-medium text-decoration-none small link-body-emphasis"
                                   href="{{ route('authors.show', $user->username) }}">
                                    {{ '@' . $user->username }}
                                </a>
                            </div>
                        </div>
                    @endforeach

                </div>
            @endif
        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

    {{-- Categories --}}
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
                <div class="d-flex flex-wrap gap-2">
                    @foreach ($tags as $tag)
                        @php
                            $isActiveCat = request('tag') == $tag->slug || request('tag') == $tag->id;
                            $query = array_merge(request()->query(), ['tag' => $tag->slug]);
                        @endphp
                        <x-thread.badge-tag :tag="$tag" />
                    @endforeach
                </div>
            @endif
        </div>
    </div>

    @include('partials.ads.display-responsive', ['slot' => '8485643721'])

</aside>
