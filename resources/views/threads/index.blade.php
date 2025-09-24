@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row">
            {{-- Sidebar Kiri --}}
            <div class="col-lg-3">
                {{-- Top Contributors --}}
                <div class="card shadow-sm border-0 mb-4">
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
                                             height="32"
                                             src="{{ $user->avatar_url }}"
                                             width="32">
                                        <div>
                                            <div class="fw-medium">{{ $user->name }}</div>
                                            <small class="text-info">{{ '@' . $user->username }}</small>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Categories --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="card-title mb-0 fw-bold">Kategori</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($categories as $category)
                                @php
                                    $isActiveCat =
                                        request('category') == $category->slug || request('category') == $category->id;
                                    $query = array_merge(request()->query(), ['category' => $category->slug]);
                                @endphp
                                <a class="badge rounded-pill text-decoration-none badge-category @if ($isActiveCat) text-bg-primary @else text-bg-light @endif"
                                   href="{{ route('comunity.index', $query) }}">
                                    {{ $category->name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>

            {{-- Konten Utama --}}
            <div class="col-lg-9">
                {{-- Search Bar --}}
                <div class="card shadow-sm border-0 mb-4">
                    <div class="card-body">
                        <form action="{{ route('comunity.index') }}"
                              autocomplete="off"
                              method="GET">
                            <div class="input-group position-relative">
                                <input class="form-control"
                                       id="thread-search"
                                       name="search"
                                       placeholder="Cari thread..."
                                       type="text"
                                       value="{{ request('search') }}">
                                <button class="btn btn-primary"
                                        type="submit">
                                    <i class="bi bi-search"></i>
                                </button>

                                <div class="list-group position-absolute w-100 mt-1 d-none"
                                     id="search-suggestions"
                                     style="z-index: 1050; max-height: 300px; overflow:auto;">
                                    {{-- suggestions injected here --}}
                                </div>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Thread List --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <div>
                        <h4 class="mb-0 fw-bold">
                            @if (!empty($currentCategory))
                                Thread {{ $currentCategory->name }}
                            @else
                                Threads
                            @endif
                        </h4>
                        <div class="mt-2">
                            <div aria-label="Thread filters"
                                 class="btn-group"
                                 role="group">
                                <a class="btn btn-sm @if (empty($filter)) btn-primary @else btn-outline-secondary @endif"
                                   href="{{ route('comunity.index') }}">Semua</a>
                                @auth
                                    <a class="btn btn-sm @if ($filter === 'mine') btn-primary @else btn-outline-secondary @endif"
                                       href="{{ route('comunity.index', ['filter' => 'mine']) }}">Thread Saya</a>
                                    <a class="btn btn-sm @if ($filter === 'bookmarks') btn-primary @else btn-outline-secondary @endif"
                                       href="{{ route('comunity.index', ['filter' => 'bookmarks']) }}">Bookmark</a>
                                @endauth
                                <a class="btn btn-sm @if ($filter === 'answered') btn-primary @else btn-outline-secondary @endif"
                                   href="{{ route('comunity.index', ['filter' => 'answered']) }}">Terjawab</a>
                            </div>
                        </div>
                    </div>

                    <a class="btn btn-primary"
                       href="{{ route('comunity.create') }}">
                        <i class="bi bi-plus-lg"></i> Buat Thread
                    </a>
                </div>

                @forelse($threads as $thread)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                {{-- User Avatar --}}
                                <div>
                                    <img alt="{{ $thread->user->name }}"
                                         class="rounded-circle"
                                         height="48"
                                         src="{{ $thread->user->avatar_url }}"
                                         width="48">
                                </div>

                                {{-- Thread Content --}}
                                <div class="flex-grow-1">
                                    <h5 class="card-title mb-1">
                                        <a class="text-decoration-none"
                                           href="{{ route('comunity.show', $thread->id) }}">
                                            {{ $thread->title }}
                                        </a>
                                        @if (!empty($thread->is_done))
                                            <span class="badge bg-success ms-2 align-middle"
                                                  title="Thread sudah terjawab">
                                                <i class="bi bi-check2-circle me-1"></i>Terjawab
                                            </span>
                                        @endif
                                    </h5>

                                    {{-- User Info & Time --}}
                                    <div class="d-flex gap-2 align-items-center mb-2">
                                        <a class="text-primary fw-medium text-decoration-none"
                                           href="{{ route('users.show', $thread->user->id) }}">{{ '@' . $thread->user->username }}</a>
                                        <small class="text-muted">•</small>
                                        <small class="text-muted">
                                            {{ $thread->updated_at->diffForHumans() }}
                                        </small>
                                    </div>

                                    {{-- Categories --}}
                                    <div class="mb-2">
                                        @foreach ($thread->categories as $category)
                                            <span class="badge rounded-pill text-bg-light">
                                                {{ $category->name }}
                                            </span>
                                        @endforeach
                                    </div>

                                    {{-- Stats --}}
                                    <div class="d-flex gap-3">
                                        <small class="text-muted">
                                            <i class="bi bi-chat me-1"></i>
                                            {{ $thread->comments_count }}
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-heart me-1"></i>
                                            {{ $thread->likes_count }}
                                        </small>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-5">
                        <div class="mb-3">
                            <i class="bi bi-chat-square-text display-1 text-muted"></i>
                        </div>
                        <h4>Belum ada thread</h4>
                        <p class="text-muted">Mari mulai diskusi dengan membuat thread baru!</p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                <div class="d-flex justify-content-center">
                    {{ $threads->links() }}
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .card {
            transition: transform 0.2s;
        }

        .card:hover {
            transform: translateY(-2px);
        }
    </style>
    <style>
        /* suggestion styling */
        #search-suggestions {
            top: calc(100% + 6px) !important;
            /* place below input, leave space */
            left: 0 !important;
            right: 0 !important;
            z-index: 2000 !important;
            box-shadow: 0 6px 18px rgba(0, 0, 0, 0.08);
            border-radius: .375rem;
            overflow: auto;
            background: #fff;
        }

        #search-suggestions .list-group-item {
            cursor: pointer;
            border: none;
            padding: .5rem .75rem;
        }

        #search-suggestions .list-group-item.active,
        #search-suggestions .list-group-item:hover {
            background-color: #e9f2ff;
        }
    </style>
@endsection

@section('scripts')
    <script>
        (function() {
            const input = document.getElementById('thread-search');
            const box = document.getElementById('search-suggestions');
            let timer = null;
            let selectedIndex = -1;

            function setActive(index) {
                const items = box.querySelectorAll('.list-group-item');
                items.forEach((it, i) => {
                    it.classList.toggle('active', i === index);
                });
                selectedIndex = index;
                // ensure visible
                const active = items[index];
                if (active) {
                    const boxRect = box.getBoundingClientRect();
                    const actRect = active.getBoundingClientRect();
                    if (actRect.top < boxRect.top) active.scrollIntoView(true);
                    else if (actRect.bottom > boxRect.bottom) active.scrollIntoView(false);
                }
            }

            function renderSuggestions(items) {
                if (!items.length) {
                    box.classList.add('d-none');
                    box.innerHTML = '';
                    selectedIndex = -1;
                    return;
                }
                box.classList.remove('d-none');
                box.innerHTML = items.map(i => `
                    <a href="${i.url}" class="list-group-item list-group-item-action" tabindex="-1">${i.title}</a>
                `).join('');
                selectedIndex = -1;
            }

            function fetchSuggestions(q) {
                fetch(`{{ route('comunity.suggest') }}?q=${encodeURIComponent(q)}`, {
                        headers: {
                            'Accept': 'application/json'
                        }
                    })
                    .then(r => r.json())
                    .then(json => {
                        renderSuggestions(json.suggestions || []);
                    }).catch(() => renderSuggestions([]));
            }

            input && input.addEventListener('input', function(e) {
                const q = e.target.value.trim();
                if (timer) clearTimeout(timer);
                if (q.length < 3) {
                    box.classList.add('d-none');
                    box.innerHTML = '';
                    return;
                }
                timer = setTimeout(() => fetchSuggestions(q), 250);
            });

            // keyboard navigation
            input && input.addEventListener('keydown', function(e) {
                const items = box.querySelectorAll('.list-group-item');
                if (box.classList.contains('d-none') || !items.length) return;

                if (e.key === 'ArrowDown') {
                    e.preventDefault();
                    const next = Math.min(selectedIndex + 1, items.length - 1);
                    setActive(next);
                } else if (e.key === 'ArrowUp') {
                    e.preventDefault();
                    const prev = Math.max(selectedIndex - 1, 0);
                    setActive(prev);
                } else if (e.key === 'Enter') {
                    if (selectedIndex >= 0 && items[selectedIndex]) {
                        e.preventDefault();
                        window.location = items[selectedIndex].href;
                    }
                } else if (e.key === 'Escape') {
                    box.classList.add('d-none');
                }
            });

            // mouse click on suggestion
            box.addEventListener('click', function(e) {
                const a = e.target.closest('a');
                if (!a) return;
                // allow normal navigation
            });

            // hide suggestions on outside click
            document.addEventListener('click', function(e) {
                if (!box.contains(e.target) && e.target !== input) {
                    box.classList.add('d-none');
                }
            });
        })();
    </script>
@endsection
