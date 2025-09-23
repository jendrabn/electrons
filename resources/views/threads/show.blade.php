@extends('layouts.app')

@section('content')
    <div class="container py-4">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                {{-- Thread --}}
                <div class="card mb-4 shadow-sm border-0">
                    <div class="card-body">
                        <div class="d-flex align-items-center mb-3">
                            <img alt="{{ $thread->user->name }}"
                                 class="rounded-circle me-3"
                                 src="{{ $thread->user->avatar_url }}"
                                 style="width:48px;height:48px;object-fit:cover;">
                            <div>
                                <div class="fw-semibold">{{ $thread->user->name }}</div>
                                <div class="text-muted small">{{ '@' . $thread->user->username }}</div>
                            </div>
                            <div class="ms-auto">
                                <button class="btn btn-link btn-sm p-0 text-decoration-none thread-like-btn"
                                        data-id="{{ $thread->id }}">
                                    <i
                                       class="bi {{ $thread->likes->where('user_id', auth()->id())->count() ? 'bi-heart-fill text-danger' : 'bi-heart' }}"></i>
                                    <span class="thread-like-count">{{ $thread->likes->count() }}</span>
                                </button>
                            </div>
                        </div>
                        <h3 class="fw-bold mb-2">{{ $thread->title }}</h3>
                        <div class="mb-3">{!! $thread->body !!}</div>
                        <div class="mb-2">
                            @foreach ($thread->categories as $cat)
                                <span class="badge bg-primary">{{ $cat->name }}</span>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Komentar --}}
                <div class="card shadow-sm border-0">
                    <div class="card-header bg-white">
                        <h5 class="mb-0 fw-bold">Komentar</h5>
                    </div>
                    <div class="card-body">
                        {{-- Form Komentar --}}
                        @auth
                            <form {{-- action="{{ route('thread-comments.store', $thread->id) }}" --}}
                                  id="commentForm"
                                  method="POST">
                                @csrf
                                <div class="d-flex align-items-start mb-3">
                                    <img alt="{{ auth()->user()->name }}"
                                         class="rounded-circle me-3"
                                         src="{{ auth()->user()->avatar_url }}"
                                         style="width:40px;height:40px;object-fit:cover;">
                                    <div class="flex-grow-1">
                                        <textarea class="form-control"
                                                  id="commentBody"
                                                  name="body"
                                                  placeholder="Tulis komentar..."
                                                  required
                                                  rows="3"></textarea>
                                    </div>
                                </div>
                                <div class="d-flex justify-content-end">
                                    <button class="btn btn-primary btn-sm"
                                            type="submit">
                                        <i class="bi bi-send"></i> Kirim
                                    </button>
                                </div>
                            </form>
                        @endauth

                        {{-- List Komentar --}}
                        <div id="commentList">
                            {{-- @foreach ($thread->comments()->whereNull('parent_id')->latest()->get() as $comment)
                                @include('threads._comment', ['comment' => $comment])
                            @endforeach --}}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // Toggle reply form dan mention @username
        document.querySelectorAll('.reply-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const id = btn.getAttribute('data-id');
                const username = btn.getAttribute('data-username');
                const form = btn.closest('.comment').querySelector('.reply-form[data-id="' + id + '"]');
                form.classList.toggle('d-none');
                const textarea = form.querySelector('textarea');
                textarea.value = '@' + username + ' ';
                textarea.focus();
            });
        });

        // Like thread
        document.querySelector('.thread-like-btn').addEventListener('click', function() {
            const btn = this;
            const threadId = btn.getAttribute('data-id');
            fetch(`/threads/${threadId}/like`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then(data => {
                    btn.querySelector('i').classList.toggle('bi-heart-fill', data.liked);
                    btn.querySelector('i').classList.toggle('text-danger', data.liked);
                    btn.querySelector('i').classList.toggle('bi-heart', !data.liked);
                    btn.querySelector('.thread-like-count').textContent = data.count;
                });
        });

        // Like comment
        document.querySelectorAll('.comment-like-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                    const commentId = btn.getAttribute('data-id');
                    fetch(`/thread-comments/${commentId}/like`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then data => {
                            btn.querySelector('i').classList.toggle('bi-heart-fill', data.liked);
                            btn.querySelector('i').classList.toggle('text-danger', data.liked);
                            btn.querySelector('i').classList.toggle('bi-heart', !data.liked);
                            btn.querySelector('.comment-like-count').textContent = data.count;
                        });
            });
        });


        // Like reply
        document.querySelectorAll('.reply-like-btn').forEach(function(btn) {
            btn.addEventListener('click', function() {
                const replyId = btn.getAttribute('data-id');
                fetch(`/thread-comments/${replyId}/like`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        btn.querySelector('i').classList.toggle('bi-heart-fill', data.liked);
                        btn.querySelector('i').classList.toggle('text-danger', data.liked);
                        btn.querySelector('i').classList.toggle('bi-heart', !data.liked);
                        btn.querySelector('.reply-like-count').textContent = data.count;
                    });
            });
        });
    </script>
@endpush
