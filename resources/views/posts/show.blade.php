@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb --}}
        <div class="mb-3 mb-lg-5">
            <nav aria-label="breadcrumb">
                <div class="breadcrumb-line d-flex align-items-center gap-2 small text-muted overflow-hidden"
                     style="min-width: 0">
                    <a class="text-decoration-none flex-shrink-0"
                       href="{{ route('home') }}">
                        <i class="bi bi-house-fill"></i>
                    </a>

                    <i class="bi bi-chevron-right opacity-50 flex-shrink-0"></i>

                    <a class="text-decoration-none flex-shrink-0"
                       href="{{ route('posts.index') }}">
                        Blog
                    </a>

                    <i class="bi bi-chevron-right opacity-50 flex-shrink-0"></i>

                    <a class="text-decoration-none flex-shrink-0"
                       href="{{ route('posts.category', $post->category->slug) }}">
                        {{ $post->category->name }}
                    </a>

                    <i class="bi bi-chevron-right opacity-50 flex-shrink-0"></i>

                    <span class="flex-grow-1 text-truncate">
                        {{ $post->title }}
                    </span>
                </div>
            </nav>
        </div>

        {{-- End Breadcrumb --}}

        <div class="row">
            <div class="col-lg-8">
                <div class="content">
                    {{-- Category --}}
                    <div class="content-category mb-2 mb-lg-3">
                        <a class="badge text-bg-warning text-white fs-6 text-decoration-none rounded-0"
                           href="{{ route('posts.category', $post->category->slug) }}">
                            {{ $post->category->name }}
                        </a>
                    </div>
                    {{-- End Category --}}

                    {{-- Title --}}
                    <h1 class="content-title mb-3">
                        {{ $post->title }}
                    </h1>
                    {{-- End Title --}}

                    {{-- Meta Mobile --}}
                    <div class="content-meta-mobile d-lg-none mb-3">
                        <div>
                            <a class="text-decoration-none small fw-semibold text-muted"
                               href="{{ route('posts.author', $post->user->id) }}">
                                <i class="bi bi-person-fill me-1"></i> Oleh
                                {{ $post->user->name }}
                            </a>
                        </div>
                        <div class="d-flex align-items-center justify-content-between small text-muted">
                            <div>
                                <span class="me-2">
                                    <i class="bi bi-clock me-1"></i>{{ $post->created_at->format('l, d F Y H:i') }}
                                </span>
                                <span>
                                    <i class="bi bi-stopwatch me-1"></i>{{ $post->min_read }} min read
                                </span>
                            </div>
                            <div>
                                <button aria-label="Share"
                                        class="btn btn-light btn-sm shadow-sm rounded-circle btn-share"
                                        type="button">
                                    <i class="bi bi-share-fill fs-6"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    {{-- End Meta Mobile --}}

                    {{-- Meta Desktop --}}
                    <ul
                        class="content-meta d-none d-lg-flex list-unstyled flex-wrap align-items-center small text-muted mb-3">
                        <li>
                            <a class="d-flex align-items-center text-decoration-none text-dark fw-semibold"
                               href="{{ route('posts.author', $post->user->id) }}">
                                <span class="me-2 d-inline-block rounded-circle overflow-hidden"
                                      style="width: 36px; height: 36px">
                                    <img alt="{{ $post->user->name }}"
                                         class="w-100 h-100 object-fit-cover"
                                         src="{{ $post->user->avatar_url }}" />
                                </span>
                                <span>{{ $post->user->name }}</span>
                            </a>
                        </li>

                        <li>
                            <i class="bi bi-clock me-1"></i>
                            {{ $post->created_at->format('l, d F Y H:i') }}
                        </li>

                        <li>
                            <i class="bi bi-stopwatch me-1"></i>
                            {{ $post->min_read }} min read
                        </li>

                        <li>
                            <i class="bi bi-eye me-1"></i>
                            {{ $post->views_count }}
                        </li>

                        <li class="ms-auto">
                            <button aria-label="Share"
                                    class="btn btn-light btn-sm shadow-sm rounded-circle btn-share"
                                    type="button">
                                <i class="bi bi-share-fill fs-6"></i>
                            </button>
                        </li>
                    </ul>
                    {{-- End Meta Desktop --}}

                    {{-- Image --}}
                    <div class="content-image mb-3">
                        <figure class="w-100">
                            <div class="rounded-3 overflow-hidden bg-gray-200 ratio ratio-16x9">
                                <picture>
                                    <img alt="{{ $post->image_caption }}"
                                         class="h-100 w-100 object-fit-fill"
                                         src="{{ $post->image_url }}" />
                                </picture>
                            </div>
                            <figcaption class="text-muted text-center mt-2">
                                {{ $post->image_caption }}
                            </figcaption>
                        </figure>
                    </div>
                    {{-- End Image --}}

                    {{-- Content --}}
                    <div class="content-body text-wrap mb-3">
                        {!! $post->content !!}
                    </div>
                    {{-- End Content --}}

                    {{-- Tags --}}
                    <div class="content-tags mb-3">
                        <span class="fw-semibold me-2"> Tag: </span>
                        @foreach ($post->tags as $tag)
                            <x-badge-tag :tag="$tag" />
                        @endforeach
                    </div>
                    {{-- End Tags --}}

                    {{-- Social Media Share --}}
                    <div class="social-media-share">
                        <x-social-media-share :post="$post"
                                              gap="2"
                                              justify="start"
                                              shape="square"
                                              showLabel="0"
                                              size="40" />
                    </div>
                    {{-- End Social Media Share --}}

                    {{-- Comments --}}
                    <div class="comments mt-5">
                        <h3 class="fw-bold mb-4">Komentar</h3>

                        {{-- Form Komentar --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                @if (auth()->check())
                                    <img alt="{{ auth()->user()->name }}"
                                         class="rounded-circle me-3"
                                         src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                                         style="
                                    width: 40px;
                                    height: 40px;
                                    object-fit: cover;
                                " />
                                @endif
                                <form class="flex-grow-1"
                                      id="commentForm">
                                    <div class="mb-3">
                                        <textarea class="form-control"
                                                  id="commentBody"
                                                  name="body"
                                                  placeholder="Tulis komentar..."
                                                  required
                                                  rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-primary btn-sm"
                                            type="submit">
                                        <i class="bi bi-send"></i> Kirim
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Daftar Komentar --}}
                        <div id="commentList"></div>
                    </div>
                    {{-- End Comments --}}

                    {{-- Modal Edit Komentar --}}
                    <div aria-hidden="true"
                         aria-labelledby="editCommentModalLabel"
                         class="modal fade"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false"
                         id="editCommentModal"
                         tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content"
                                  id="editCommentForm">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title"
                                        id="editCommentModalLabel">
                                        Edit Komentar
                                    </h5>
                                    <button class="btn-close"
                                            data-bs-dismiss="modal"
                                            type="button"></button>
                                </div>
                                <div class="modal-body">
                                    <textarea class="form-control"
                                              id="editCommentBody"
                                              name="body"
                                              required
                                              rows="3"></textarea>
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button class="btn btn-link text-decoration-none text-primary"
                                            data-bs-dismiss="modal"
                                            type="button">
                                        Batal
                                    </button>
                                    <button class="btn btn-link text-decoration-none text-primary"
                                            type="submit">
                                        Simpan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- Modal Konfirmasi Hapus Komentar --}}
                    <div aria-hidden="true"
                         aria-labelledby="deleteCommentModalLabel"
                         class="modal fade"
                         data-bs-backdrop="static"
                         data-bs-keyboard="false"
                         id="deleteCommentModal"
                         tabindex="-1">
                        <div class="modal-dialog modal-dialog-centered">
                            <form class="modal-content"
                                  id="deleteCommentForm">
                                <div class="modal-header border-bottom-0">
                                    <h5 class="modal-title"
                                        id="deleteCommentModalLabel">
                                        Perhatian
                                    </h5>
                                    <button class="btn-close"
                                            data-bs-dismiss="modal"
                                            type="button"></button>
                                </div>
                                <div class="modal-body">
                                    Apakah Anda yakin ingin menghapus komentar ini?
                                </div>
                                <div class="modal-footer border-top-0">
                                    <button class="btn btn-link text-decoration-none text-primary"
                                            data-bs-dismiss="modal"
                                            type="button">
                                        Batal
                                    </button>
                                    <button class="btn btn-link text-decoration-none text-danger"
                                            type="submit">
                                        Hapus
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-4">@include('partials.sidebar')</div>
        </div>

        @if ($relatedPosts->count() > 0)
            <div class="mt-5">
                <h2 class="fw-bold mb-3 m-0">Artikel Terkait</h2>

                <div class="row gx-0 gy-2 g-lg-4">
                    @foreach ($relatedPosts as $post)
                        <div class="col-12 col-md-6 col-lg-4">
                            <x-post-item :post="$post"
                                         type="vertical" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    <!-- Modal Social Media Share -->
    <div aria-hidden="true"
         aria-labelledby="shareModalLabel"
         class="modal fade"
         id="shareModal"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header d-flex align-items-start border-bottom-0">
                    <h5 class="modal-title text-wrap text-truncate fs-6 line-clamp-2"
                        id="shareModalLabel">
                        {{ $post->title }}
                    </h5>
                    <button aria-label="Tutup"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>

                <div class="modal-body">
                    <p class="mb-4 text-center">Bagikan artikel ini melalui:</p>
                    <x-social-media-share :post="$post" />
                </div>
            </div>
        </div>
    </div>

    <div class="toast-container position-fixed top-0 end-0 p-3"></div>
@endsection

@push('scripts')
    <script>
        const postId = {{ $post->id }};
        const userId = {{ auth()->id() ?? 'null' }};
        const avatarUrl = '{{ auth()->user()->avatar_url ?? '' }}';

        let comments = [];
        let editCommentId = null;
        let deleteCommentId = null;

        // Render comments & replies
        function renderComments() {
            const container = document.getElementById('commentList');

            container.innerHTML = comments.map(comment => `
            <div class="comment mb-4" data-id="${comment.id}">
                <div class="d-flex align-items-start">
                    <img alt="${comment.user.name}" class="rounded-circle me-3" src="${comment.user.avatar_url}" style="width:40px;height:40px;object-fit:cover;">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <span class="fw-semibold me-2">${comment.user.name}</span>
                            <span class="text-muted small">${comment.created_at_human}</span>
                            ${userId === comment.user_id ? `
                                <div class="ms-auto d-flex gap-2">
                                    <button class="btn btn-link btn-sm p-0 text-decoration-none edit-btn" data-id="${comment.id}" type="button">
                                        <i class="bi bi-pencil"></i>
                                    </button>
                                    <button class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn" data-id="${comment.id}" type="button">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            ` : ''}

                        </div>
                        <div class="mb-2">${comment.body}</div>
                        <div class="d-flex align-items-center gap-3">
                            <button class="btn btn-link btn-sm p-0 text-decoration-none reply-btn" data-id="${comment.id}" type="button">
                                <i class="bi bi-reply"></i> Balas
                            </button>
                            <button class="btn btn-link btn-sm p-0 text-decoration-none like-btn" data-id="${comment.id}" type="button">
                                <i class="bi ${comment.liked ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up'}"></i>
                                <span class="like-count">${comment.likes_count}</span>
                            </button>
                            ${comment.replies.length > 0 ? `
                                <button class="btn btn-link btn-sm p-0 text-decoration-none" type="button" data-bs-toggle="collapse" data-bs-target="#repliesCollapse${comment.id}">
                                    <i class="bi bi-chat-left-text"></i> Lihat ${comment.replies.length} Balasan
                                </button>
                            ` : ''}
                        </div>
                        <form class="reply-form mt-3 d-none" data-id="${comment.id}">
                            <textarea class="form-control mb-2" name="body" rows="2" placeholder="Tulis balasan..." required></textarea>
                            <button class="btn btn-primary btn-sm" type="submit"><i class="bi bi-send"></i> Kirim Balasan</button>
                        </form>
                        <div class="collapse mt-2" id="repliesCollapse${comment.id}">
                            ${comment.replies.map(reply => `
                                <div class="comment reply mt-3 ms-4" data-id="${reply.id}">
                                    <div class="d-flex align-items-start">
                                        <img alt="${reply.user.name}" class="rounded-circle me-2" src="${reply.user.avatar_url}" style="width:32px;height:32px;object-fit:cover;">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="fw-semibold me-2">${reply.user.name}</span>
                                                <span class="text-muted small">${reply.created_at_human}</span>
                                                ${userId === reply.user_id ? `
                                            <div class="ms-auto d-flex gap-2">
                                                <button class="btn btn-link btn-sm p-0 text-decoration-none edit-btn" data-id="${reply.id}" type="button">
                                                    <i class="bi bi-pencil"></i>
                                                </button>
                                                <button class="btn btn-link btn-sm p-0 text-decoration-none text-danger delete-btn" data-id="${reply.id}" type="button">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </div>
                                        ` : ''}
                                            </div>
                                            <div class="mb-2">${reply.body}</div>
                                            <div class="d-flex align-items-center gap-3">
                                                <button class="btn btn-link btn-sm p-0 text-decoration-none like-btn" data-id="${reply.id}" type="button">
                                                    <i class="bi ${reply.liked ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up'}"></i>
                                                    <span class="like-count">${reply.likes_count}</span>
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            `).join('')}
                        </div>
                    </div>
                </div>
            </div>
        `).join('');
            attachCommentEvents();
        }

        // Fetch comments
        function fetchComments() {
            fetch("{{ route('comments.list', $post->id) }}")
                .then(res => res.json())
                .then(data => {
                    comments = data;
                    renderComments();
                });
        }

        // Submit new comment
        document.getElementById('commentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const body = document.getElementById('commentBody').value;
            fetch("{{ route('comments.store') }}", {
                    method: 'POST',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        body,
                        post_id: postId
                    })
                })
                .then(res => res.json())
                .then((data) => {
                    document.getElementById('commentBody').value = '';
                    showToast(data.success, data.message);
                    fetchComments();
                });
        });

        // Attach events after comments are rendered
        function attachCommentEvents() {
            // Reply form toggle
            document.querySelectorAll('.reply-btn').forEach(btn => {
                btn.onclick = function() {
                    const id = btn.getAttribute('data-id');
                    const form = btn.closest('.comment').querySelector('.reply-form[data-id="' + id + '"]');
                    form.classList.toggle('d-none');
                    form.querySelector('textarea').focus();
                };
            });

            // Submit reply
            document.querySelectorAll('.reply-form').forEach(form => {
                form.onsubmit = function(e) {
                    e.preventDefault();
                    const id = form.getAttribute('data-id');
                    const body = form.querySelector('textarea').value;
                    fetch(`/comments/${id}/reply`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                body
                            })
                        })
                        .then(res => res.json())
                        .then((data) => {
                            form.querySelector('textarea').value = '';
                            form.classList.add('d-none');
                            showToast(data.success, data.message);
                            fetchComments();
                        });
                };
            });

            // Like/unlike
            document.querySelectorAll('.like-btn').forEach(btn => {
                btn.onclick = function() {
                    const id = btn.getAttribute('data-id');
                    fetch(`/comments/${id}/like`, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        })
                        .then(res => res.json())
                        .then((data) => {
                            showToast(data.success, data.message);
                            fetchComments()
                        });
                };
            });

            // Edit
            document.querySelectorAll('.edit-btn').forEach(btn => {
                btn.onclick = function() {
                    editCommentId = btn.getAttribute('data-id');
                    const comment = findCommentById(editCommentId);
                    document.getElementById('editCommentBody').value = comment.body;
                    new bootstrap.Modal(document.getElementById('editCommentModal')).show();
                };
            });

            // Delete
            document.querySelectorAll('.delete-btn').forEach(btn => {
                btn.onclick = function() {
                    deleteCommentId = btn.getAttribute('data-id');
                    new bootstrap.Modal(document.getElementById('deleteCommentModal')).show();
                };
            });
        }

        // Submit edit
        document.getElementById('editCommentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const body = document.getElementById('editCommentBody').value;
            fetch(`/comments/${editCommentId}`, {
                    method: 'PUT',
                    headers: {
                        'Accept': 'application/json',
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({
                        body
                    })
                })
                .then(res => res.json())
                .then((data) => {
                    bootstrap.Modal.getInstance(document.getElementById('editCommentModal')).hide();
                    showToast(data.success, data.message);
                    fetchComments();
                });
        });

        // Submit delete
        document.getElementById('deleteCommentForm').addEventListener('submit', function(e) {
            e.preventDefault();
            fetch(`/comments/${deleteCommentId}`, {
                    method: 'DELETE',
                    headers: {
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                })
                .then(res => res.json())
                .then((data) => {
                    bootstrap.Modal.getInstance(document.getElementById('deleteCommentModal')).hide();
                    showToast(data.success, data.message);
                    fetchComments();
                });
        });

        // Find comment
        function findCommentById(id) {
            for (const c of comments) {
                if (c.id == id) return c;
                for (const r of c.replies) {
                    if (r.id == id) return r;
                }
            }
            return null;
        }

        // Show toast
        function showToast(success, message) {
            const toastId = 'toast-' + Date.now();
            const bgClass = success ? 'bg-success text-white' : 'bg-danger text-white';

            if (!success !== 'boolean') {
                success = false;
            }

            const toastHtml = `
            <div id="${toastId}" class="toast align-items-center ${bgClass} border-0 mb-2" role="alert" aria-live="assertive" aria-atomic="true">
                <div class="d-flex">
                <div class="toast-body">${message ?? 'Terjadi kesalahan'}</div>
                <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                </div>
            </div>`;

            document.querySelector('.toast-container').insertAdjacentHTML('beforeend', toastHtml);

            const toastEl = document.getElementById(toastId);
            const toast = new bootstrap.Toast(toastEl, {
                delay: 3000
            });
            toast.show();

            toastEl.addEventListener('hidden.bs.toast', () => toastEl.remove());
        }

        document.addEventListener('DOMContentLoaded', fetchComments);
    </script>
@endpush
