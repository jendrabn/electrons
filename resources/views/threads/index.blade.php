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
                                            <small class="text-muted">{{ '@' . $user->username }}</small>
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
                                <a {{-- href="{{ route('threads.category', $category->slug) }}" --}}
                                   class="badge rounded-pill text-bg-light text-decoration-none">
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
                              method="GET">
                            <div class="input-group">
                                <input class="form-control"
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
                    <h4 class="mb-0 fw-bold">Threads</h4>
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
                                           href="{{ route('comunity.show', $thread->slug) }}">
                                            {{ $thread->title }}
                                        </a>
                                    </h5>

                                    {{-- User Info & Time --}}
                                    <div class="d-flex gap-2 align-items-center mb-2">
                                        <span class="fw-medium">{{ $thread->user->name }}</span>
                                        <small class="text-muted">{{ '@' . $thread->user->username }}</small>
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
                                            <i class="bi bi-chat"></i>
                                            {{ $thread->comments_count }} Komentar
                                        </small>
                                        <small class="text-muted">
                                            <i class="bi bi-heart"></i>
                                            {{ $thread->likes_count }} Like
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
@endsection
