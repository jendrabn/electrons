@extends('layouts.app')

@section('content')
    <div class="container py-3 py-lg-4 thread-index-page">
        <div class="row">
            <div class="col-lg-8">
                {{-- Header --}}
                <div class="mb-4 thread-header">
                    <div class="thread-hero-main d-flex flex-column align-items-start gap-3 flex-grow-1">
                        <div>
                            <span class="thread-hero-badge">
                                <i class="bi bi-activity"></i>
                                <span>Komunitas Teknologi</span>
                            </span>
                            <h2 class="fw-bold mb-1 thread-hero-title">Diskusi Komunitas</h2>
                            <p class="text-muted mb-0 thread-hero-sub">Temukan jawaban, bagikan pengetahuan, dan terlibat
                                dalam diskusi dengan anggota komunitas lainnya.</p>
                        </div>
                    </div>
                </div>

                {{-- Search & Create Thread --}}
                <div class="card thread-toolbar-card shadow-sm border-0 mb-4">
                    <div class="card-body py-3">
                        <div class="thread-toolbar">
                            <form action="{{ route('community.index') }}"
                                  autocomplete="off"
                                  class="thread-toolbar__search"
                                  method="GET">
                                <div class="thread-search-group">
                                    <input class="thread-search-input"
                                           id="thread-search"
                                           name="search"
                                           placeholder="Cari thread..."
                                           type="search"
                                           value="{{ request('search') }}">
                                    <button class="thread-search-submit"
                                            type="submit">
                                        <i class="bi bi-search"></i>
                                    </button>
                                </div>
                            </form>

                            <div class="thread-toolbar__actions">
                                <a class="thread-create-btn"
                                   href="{{ route('community.create') }}">
                                    <i class="bi bi-plus-lg"></i>
                                    <span>Buat Thread</span>
                                </a>

                                @php
                                    $filterLabel = 'Semua';
                                    if ($filter === 'mine') {
                                        $filterLabel = 'Thread Saya';
                                    } elseif ($filter === 'bookmarks') {
                                        $filterLabel = 'Bookmark';
                                    } elseif ($filter === 'answered') {
                                        $filterLabel = 'Terjawab';
                                    }
                                @endphp

                                <div class="dropdown thread-filter-dropdown">
                                    <button aria-expanded="false"
                                            class="thread-filter-trigger"
                                            data-bs-toggle="dropdown"
                                            id="threadFilterDropdown"
                                            type="button">
                                        <i class="bi bi-funnel"></i>
                                        <span>{{ $filterLabel }}</span>
                                    </button>
                                    <ul aria-labelledby="threadFilterDropdown"
                                        class="dropdown-menu thread-filter-dropdown-menu">
                                        <li>
                                            <a class="dropdown-item thread-filter-option @if (empty($filter)) active @endif"
                                               href="{{ route('community.index') }}">
                                                <i class="bi bi-globe"></i>
                                                <span>Semua</span>
                                            </a>
                                        </li>
                                        @auth
                                            <li>
                                                <a class="dropdown-item thread-filter-option @if ($filter === 'mine') active @endif"
                                                   href="{{ route('community.index', ['filter' => 'mine']) }}">
                                                    <i class="bi bi-person-lines-fill"></i>
                                                    <span>Thread Saya</span>
                                                </a>
                                            </li>
                                            <li>
                                                <a class="dropdown-item thread-filter-option @if ($filter === 'bookmarks') active @endif"
                                                   href="{{ route('community.index', ['filter' => 'bookmarks']) }}">
                                                    <i class="bi bi-bookmark-heart"></i>
                                                    <span>Bookmark</span>
                                                </a>
                                            </li>
                                        @endauth
                                        <li>
                                            <a class="dropdown-item thread-filter-option @if ($filter === 'answered') active @endif"
                                               href="{{ route('community.index', ['filter' => 'answered']) }}">
                                                <i class="bi bi-check-circle"></i>
                                                <span>Terjawab</span>
                                            </a>
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Thread List --}}
                @forelse($threads as $thread)
                    <div class="card shadow-sm border-0 mb-3">
                        <div class="card-body">
                            <div class="d-flex gap-3">
                                {{-- User Avatar --}}
                                <div>
                                    <img alt="{{ $thread->user->name }}"
                                         class="rounded-circle"
                                         height="40"
                                         src="{{ $thread->user->avatar_url }}"
                                         width="40">
                                </div>

                                {{-- Thread Content --}}
                                <div class="flex-grow-1">
                                    {{-- User Info & Time --}}
                                    <div class="d-flex gap-2 align-items-center mb-2">
                                        <a class="text-primary fw-medium text-decoration-none"
                                           href="{{ route('authors.show', $thread->user->username) }}">{{ $thread->user->username }}</a>
                                        <small class="text-muted">•</small>
                                        <small class="text-muted">
                                            {{ $thread->updated_at->diffForHumans() }}
                                        </small>
                                    </div>

                                    {{-- Title --}}
                                    <h5 class="card-title mb-2">
                                        <a class="text-decoration-none text-break text-wrap"
                                           href="{{ route('community.show', $thread->id) }}">
                                            {{ $thread->title }}
                                        </a>
                                        @if (!empty($thread->is_done))
                                            <span class="badge bg-success ms-2 align-middle"
                                                  title="Thread sudah terjawab">
                                                <i class="bi bi-check2-circle me-1"></i>Terjawab
                                            </span>
                                        @endif
                                    </h5>

                                    {{-- Tags --}}
                                    <div class="mb-2">
                                        @foreach ($thread->tags as $tag)
                                            <x-thread.badge-tag :tag="$tag"
                                                                small
                                                                withIcon="0" />
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
                            <i class="bi bi-chat-left display-1 text-muted"></i>
                        </div>
                        <h4>No threads found</h4>
                        <p class="text-muted">Let's start a discussion by creating a new thread!</p>
                    </div>
                @endforelse

                {{-- Pagination --}}
                <div class="mt-5">
                    {{ $threads->links() }}
                </div>
            </div>

            <div class="col-lg-4">
                @include('frontpages.threads.partials._sidebar')
            </div>
        </div>
    </div>
@endsection
