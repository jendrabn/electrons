@extends('layouts.app')

@section('content')
    <div class="container py-3 py-lg-4">
        <div class="row">
            {{-- Sidebar Kiri --}}
            <div class="col-lg-3 order-2 order-lg-1">
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
                                             height="30"
                                             src="{{ $user->avatar_url }}"
                                             width="30">
                                        <div>
                                            <a class="fw-medium text-decoration-none small"
                                               href="{{ route('users.show', $user->id) }}">{{ '@' . $user->username }}</a>
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
                        <h5 class="card-title mb-0 fw-bold">Tag</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex flex-wrap gap-2">
                            @foreach ($tags as $category)
                                @php
                                    $isActiveCat = request('tag') == $category->slug || request('tag') == $category->id;
                                    $query = array_merge(request()->query(), ['tag' => $category->slug]);
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
            <div class="col-lg-9 order-1 order-lg-2">
                {{-- Header --}}
                <div class="mb-4 thread-header d-flex align-items-center justify-content-between">
                    <div class="d-flex align-items-center gap-3">
                        <div class="thread-hero-icon d-none d-md-flex align-items-center justify-content-center">
                            <!-- chat bubble SVG -->
                            <svg aria-hidden="true"
                                 fill="none"
                                 height="22"
                                 viewBox="0 0 24 24"
                                 width="22"
                                 xmlns="http://www.w3.org/2000/svg">
                                <path d="M21 15a2 2 0 0 1-2 2H8l-5 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"
                                      fill="white"
                                      opacity=".95" />
                            </svg>
                        </div>
                        <div>
                            <h2 class="fw-bold mb-1 thread-hero-title">Diskusi Komunitas</h2>
                            <p class="text-muted mb-0 thread-hero-sub">Temukan jawaban, bagikan pengetahuan, dan terlibat
                                dalam diskusi dengan anggota komunitas lainnya.</p>
                        </div>
                    </div>
                </div>

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

                            </div>
                        </form>
                    </div>
                </div>

                {{-- Thread List --}}
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <a class="btn btn-primary"
                       href="{{ route('comunity.create') }}">
                        <i class="bi bi-plus-lg"></i> Buat Thread
                    </a>

                    {{-- Responsive thread filters: dropdown on small/medium, btn-group on large+ --}}
                    <div class="thread-filters d-flex align-items-center">
                        @php
                            $filterLabel = 'Filter';
                            if (empty($filter)) {
                                $filterLabel = 'Semua';
                            } elseif ($filter === 'mine') {
                                $filterLabel = 'Thread Saya';
                            } elseif ($filter === 'bookmarks') {
                                $filterLabel = 'Bookmark';
                            } elseif ($filter === 'answered') {
                                $filterLabel = 'Terjawab';
                            }
                        @endphp

                        {{-- Mobile / Tablet: dropdown visible up to lg --}}
                        <div class="dropdown d-inline-block d-lg-none me-2">
                            <button aria-expanded="false"
                                    class="btn btn-primary dropdown-toggle"
                                    data-bs-toggle="dropdown"
                                    id="threadFilterDropdownMobile"
                                    type="button">
                                <i class="bi bi-funnel"></i> {{ $filterLabel }}
                            </button>
                            <ul aria-labelledby="threadFilterDropdownMobile"
                                class="dropdown-menu">
                                <li>
                                    <a class="dropdown-item @if (empty($filter)) active @endif"
                                       href="{{ route('comunity.index') }}">Semua</a>
                                </li>
                                @auth
                                    <li>
                                        <a class="dropdown-item @if ($filter === 'mine') active @endif"
                                           href="{{ route('comunity.index', ['filter' => 'mine']) }}">Thread Saya</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item @if ($filter === 'bookmarks') active @endif"
                                           href="{{ route('comunity.index', ['filter' => 'bookmarks']) }}">Bookmark</a>
                                    </li>
                                @endauth
                                <li>
                                    <a class="dropdown-item @if ($filter === 'answered') active @endif"
                                       href="{{ route('comunity.index', ['filter' => 'answered']) }}">Terjawab</a>
                                </li>
                            </ul>
                        </div>

                        {{-- Desktop: keep original btn-group (visible on lg+) --}}
                        <div aria-label="Thread filters"
                             class="btn-group d-none d-lg-inline-flex"
                             role="group">
                            <a class="btn @if (empty($filter)) btn-primary @else btn-outline-secondary @endif"
                               href="{{ route('comunity.index') }}">Semua</a>
                            @auth
                                <a class="btn @if ($filter === 'mine') btn-primary @else btn-outline-secondary @endif"
                                   href="{{ route('comunity.index', ['filter' => 'mine']) }}">Thread Saya</a>
                                <a class="btn @if ($filter === 'bookmarks') btn-primary @else btn-outline-secondary @endif"
                                   href="{{ route('comunity.index', ['filter' => 'bookmarks']) }}">Bookmark</a>
                            @endauth
                            <a class="btn @if ($filter === 'answered') btn-primary @else btn-outline-secondary @endif"
                               href="{{ route('comunity.index', ['filter' => 'answered']) }}">Terjawab</a>
                        </div>
                    </div>
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
                                        @foreach ($thread->tags as $category)
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

        /* Modern hero-style thread header */
        .thread-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1rem 1.25rem;
            border-radius: 0.75rem;
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.08), rgba(99, 102, 241, 0.02));
            border: 1px solid rgba(99, 102, 241, 0.06);
        }

        .thread-hero-icon {
            width: 56px;
            height: 56px;
            border-radius: 12px;
            background: linear-gradient(135deg, #7c3aed, #2563eb);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.12);
            color: white;
        }

        .thread-hero-title {
            font-size: 1.5rem;
            margin: 0;
            background: linear-gradient(90deg, #111827, #2563eb);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .thread-hero-sub {
            color: #6b7280;
            margin-top: 0.125rem
        }

        .btn-primary.btn-lg {
            padding: 0.6rem 1rem;
        }

        @media (min-width: 992px) {
            .thread-hero-title {
                font-size: 1.8rem
            }
        }
    </style>
@endsection
