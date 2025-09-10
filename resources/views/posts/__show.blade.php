@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb --}}
        <div class="mb-3 mb-lg-5">
            <nav aria-label="breadcrumb">
                <div class="breadcrumb-line d-flex align-items-center gap-2 small text-muted overflow-hidden"
                     style="min-width:0">
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

                    <!-- yang di-truncate -->
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
                                <i class="bi bi-person-fill me-1"></i> Oleh {{ $post->user->name }}
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
                                         src="{{ $post->user->avatar_url }}">
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
                            <figcaption class="text-muted text-center mt-2">{{ $post->image_caption }}
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
                        <span class="fw-semibold me-2">
                            Tag:
                        </span>
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
                                         style="width:40px;height:40px;object-fit:cover;" />
                                @endif
                                <form action="{{ route('comments.store', $post->id) }}"
                                      class="flex-grow-1"
                                      method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control"
                                                  name="body"
                                                  placeholder="Tulis komentar..."
                                                  required
                                                  rows="3"></textarea>
                                    </div>
                                    <button class="btn btn-primary btn-sm"
                                            type="submit"><i class="bi bi-send"></i> Kirim</button>
                                </form>
                            </div>

                        </div>

                        {{-- Daftar Komentar --}}
                        <div class="comment-list">
                            @forelse ($post->comments()->whereNull('parent_id')->latest()->get() as $comment)
                                <div class="comment mb-4">
                                    <div class="d-flex align-items-start">
                                        <img alt="{{ $comment->user->name }}"
                                             class="rounded-circle me-3"
                                             src="{{ $comment->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($comment->user->name) }}"
                                             style="width:40px;height:40px;object-fit:cover;">
                                        <div class="flex-grow-1">
                                            <div class="d-flex align-items-center mb-1">
                                                <span class="fw-semibold me-2">{{ $comment->user->name }}</span>
                                                <span
                                                      class="text-muted small">{{ $comment->created_at->diffForHumans() }}</span>
                                                @if (auth()->id() === $comment->user_id)
                                                    <div class="ms-auto d-flex gap-2">
                                                        <!-- Edit Button -->
                                                        <button class="btn btn-link btn-sm p-0 text-decoration-none edit-btn"
                                                                data-bs-target="#editCommentModal{{ $comment->id }}"
                                                                data-bs-toggle="modal"
                                                                type="button">
                                                            <i class="bi bi-pencil"></i>
                                                        </button>
                                                        {{-- Tombol Hapus Komentar --}}
                                                        <button class="btn btn-link btn-sm p-0 text-decoration-none text-danger"
                                                                data-bs-target="#deleteCommentModal{{ $comment->id }}"
                                                                data-bs-toggle="modal"
                                                                type="button">
                                                            <i class="bi bi-trash"></i>
                                                        </button>

                                                        {{-- Modal Konfirmasi Hapus Komentar --}}
                                                        <div aria-hidden="true"
                                                             aria-labelledby="deleteCommentModalLabel{{ $comment->id }}"
                                                             class="modal fade"
                                                             data-bs-backdrop="static"
                                                             data-bs-keyboard="false"
                                                             id="deleteCommentModal{{ $comment->id }}"
                                                             tabindex="-1">
                                                            <div class="modal-dialog modal-dialog-centered">
                                                                <form action="{{ route('comments.destroy', $comment->id) }}"
                                                                      class="modal-content"
                                                                      method="POST">
                                                                    @csrf
                                                                    @method('DELETE')
                                                                    <div class="modal-header border-bottom-0">
                                                                        <h5 class="modal-title"
                                                                            id="deleteCommentModalLabel{{ $comment->id }}">
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
                                                                        <button class="btn btn-link text-decoration-none text-danger"
                                                                                data-bs-dismiss="modal"
                                                                                type="button">Batal</button>
                                                                        <button class="btn btn-link text-decoration-none text-danger"
                                                                                type="submit">Hapus</button>
                                                                    </div>
                                                                </form>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                            <div class="mb-2">{{ $comment->body }}</div>
                                            <div class="d-flex align-items-center gap-3">
                                                <button class="btn btn-link btn-sm p-0 text-decoration-none reply-btn"
                                                        data-comment-id="{{ $comment->id }}"
                                                        type="button">
                                                    <i class="bi bi-reply"></i> Balas
                                                </button>
                                                <button class="btn btn-link btn-sm p-0 text-decoration-none like-btn"
                                                        data-comment-id="{{ $comment->id }}"
                                                        type="button">
                                                    <i
                                                       class="bi {{ $comment->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                                                    <span class="like-count">{{ $comment->likes->count() }}</span>
                                                </button>
                                            </div>
                                            {{-- Form Reply (hidden by default) --}}
                                            <form action="{{ route('comments.reply', $comment->id) }}"
                                                  class="reply-form mt-3 d-none"
                                                  method="POST">
                                                @csrf
                                                <textarea class="form-control mb-2"
                                                          name="body"
                                                          placeholder="Tulis balasan..."
                                                          required
                                                          rows="2"></textarea>
                                                <button class="btn btn-secondary btn-sm"
                                                        type="submit">Kirim Balasan</button>
                                            </form>
                                            {{-- Modal Edit Komentar --}}
                                            <div aria-hidden="true"
                                                 aria-labelledby="editCommentModalLabel{{ $comment->id }}"
                                                 class="modal fade"
                                                 data-bs-backdrop="static"
                                                 data-bs-keyboard="false"
                                                 id="editCommentModal{{ $comment->id }}"
                                                 tabindex="-1">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <form action="{{ route('comments.update', $comment->id) }}"
                                                          class="modal-content"
                                                          method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <div class="modal-header border-bottom-0">
                                                            <h5 class="modal-title"
                                                                id="editCommentModalLabel{{ $comment->id }}">Edit
                                                                Komentar</h5>
                                                            <button class="btn-close"
                                                                    data-bs-dismiss="modal"
                                                                    type="button"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <textarea class="form-control"
                                                                      name="body"
                                                                      required
                                                                      rows="3">{{ $comment->body }}</textarea>
                                                        </div>
                                                        <div class="modal-footer border-top-0">
                                                            <button class="btn btn-link text-decoration-none text-primary"
                                                                    data-bs-dismiss="modal"
                                                                    type="button">Batal</button>
                                                            <button class="btn btn-link text-decoration-none text-primary"
                                                                    type="submit">Simpan</button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                            {{-- Reply Comments Collapsible --}}
                                            @if ($comment->replies()->count() > 0)
                                                <button aria-controls="repliesCollapse{{ $comment->id }}"
                                                        aria-expanded="false"
                                                        class="btn btn-link btn-sm p-0 text-decoration-none"
                                                        data-bs-target="#repliesCollapse{{ $comment->id }}"
                                                        data-bs-toggle="collapse"
                                                        type="button">
                                                    <i class="bi bi-chat-left-text"></i>
                                                    Lihat {{ $comment->replies()->count() }} Balasan
                                                </button>
                                            @endif

                                            <div class="collapse mt-2"
                                                 id="repliesCollapse{{ $comment->id }}">
                                                @foreach ($comment->replies()->latest()->get() as $reply)
                                                    <div class="comment reply mt-3 ms-4">
                                                        <div class="d-flex align-items-start">
                                                            <img alt="{{ $reply->user->name }}"
                                                                 class="rounded-circle me-2"
                                                                 src="{{ $reply->user->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode($reply->user->name) }}"
                                                                 style="width:32px;height:32px;object-fit:cover;">
                                                            <div class="flex-grow-1">
                                                                <div class="d-flex align-items-center mb-1">
                                                                    <span
                                                                          class="fw-semibold me-2">{{ $reply->user->name }}</span>
                                                                    <span
                                                                          class="text-muted small">{{ $reply->created_at->diffForHumans() }}</span>
                                                                    @if (auth()->id() === $reply->user_id)
                                                                        <div class="ms-auto d-flex gap-2">
                                                                            <!-- Edit Button -->
                                                                            <button class="btn btn-link btn-sm p-0 text-decoration-none edit-btn"
                                                                                    data-bs-target="#editReplyModal{{ $reply->id }}"
                                                                                    data-bs-toggle="modal"
                                                                                    type="button">
                                                                                <i class="bi bi-pencil"></i>
                                                                            </button>
                                                                            <!-- Delete Button (pakai modal) -->
                                                                            <button class="btn btn-link btn-sm p-0 text-decoration-none text-danger"
                                                                                    data-bs-target="#deleteReplyModal{{ $reply->id }}"
                                                                                    data-bs-toggle="modal"
                                                                                    type="button">
                                                                                <i class="bi bi-trash"></i>
                                                                            </button>
                                                                            {{-- Modal Konfirmasi Hapus Reply --}}
                                                                            <div aria-hidden="true"
                                                                                 aria-labelledby="deleteReplyModalLabel{{ $reply->id }}"
                                                                                 class="modal fade"
                                                                                 data-bs-backdrop="static"
                                                                                 data-bs-keyboard="false"
                                                                                 id="deleteReplyModal{{ $reply->id }}"
                                                                                 tabindex="-1">
                                                                                <div
                                                                                     class="modal-dialog modal-dialog-centered">
                                                                                    <form action="{{ route('comments.destroy', $reply->id) }}"
                                                                                          class="modal-content"
                                                                                          method="POST">
                                                                                        @csrf
                                                                                        @method('DELETE')
                                                                                        <div
                                                                                             class="modal-header border-bottom-0">
                                                                                            <h5 class="modal-title"
                                                                                                id="deleteReplyModalLabel{{ $reply->id }}">
                                                                                                Konfirmasi Hapus</h5>
                                                                                            <button class="btn-close"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    type="button"></button>
                                                                                        </div>
                                                                                        <div class="modal-body">
                                                                                            Apakah Anda yakin ingin
                                                                                            menghapus komentar ini?
                                                                                        </div>
                                                                                        <div
                                                                                             class="modal-footer border-top-0">
                                                                                            <button class="btn btn-link text-decoration-none text-danger"
                                                                                                    data-bs-dismiss="modal"
                                                                                                    type="button">Batal</button>
                                                                                            <button class="btn btn-link text-decoration-none text-danger"
                                                                                                    type="submit">Hapus</button>
                                                                                        </div>
                                                                                    </form>
                                                                                </div>
                                                                            </div>
                                                                        </div>
                                                                    @endif
                                                                </div>
                                                                <div class="mb-2">{{ $reply->body }}</div>
                                                                <div class="d-flex align-items-center gap-3">
                                                                    <span class="text-muted small">
                                                                        <button class="btn btn-link btn-sm p-0 text-decoration-none like-btn"
                                                                                data-comment-id="{{ $reply->id }}"
                                                                                type="button">
                                                                            <i
                                                                               class="bi {{ $reply->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                                                                            <span
                                                                                  class="like-count">{{ $reply->likes->count() }}</span>
                                                                        </button>
                                                                    </span>
                                                                </div>
                                                                {{-- Modal Edit Reply --}}
                                                                <div aria-hidden="true"
                                                                     aria-labelledby="editReplyModalLabel{{ $reply->id }}"
                                                                     class="modal fade"
                                                                     id="editReplyModal{{ $reply->id }}"
                                                                     tabindex="-1">
                                                                    <div class="modal-dialog">
                                                                        <form action="{{ route('comments.update', $reply->id) }}"
                                                                              class="modal-content"
                                                                              method="POST">
                                                                            @csrf
                                                                            @method('PUT')
                                                                            <div class="modal-header">
                                                                                <h5 class="modal-title"
                                                                                    id="editReplyModalLabel{{ $reply->id }}">
                                                                                    Edit Balasan</h5>
                                                                                <button class="btn-close"
                                                                                        data-bs-dismiss="modal"
                                                                                        type="button"></button>
                                                                            </div>
                                                                            <div class="modal-body">
                                                                                <textarea class="form-control"
                                                                                          name="body"
                                                                                          required
                                                                                          rows="3">{{ $reply->body }}</textarea>
                                                                            </div>
                                                                            <div class="modal-footer">
                                                                                <button class="btn btn-secondary"
                                                                                        data-bs-dismiss="modal"
                                                                                        type="button">Batal</button>
                                                                                <button class="btn btn-primary"
                                                                                        type="submit">Simpan</button>
                                                                            </div>
                                                                        </form>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="text-muted">Belum ada komentar. Jadilah yang pertama!</div>
                            @endforelse
                        </div>
                    </div>
                    {{-- End Comments --}}

                </div>
            </div>
            <div class="col-lg-4">
                @include('partials.sidebar')
            </div>
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
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Share Modal
            const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));

            document.querySelectorAll('.btn-share').forEach(function(shareBtn) {
                shareBtn.addEventListener('click', function() {
                    shareModal.show();
                });
            });

            // Copy Link
            const btnCopy = document.getElementById('btn-copy-link');
            if (btnCopy) {
                btnCopy.addEventListener('click', function() {
                    navigator.clipboard.writeText(window.location.href).then(function() {
                        btnCopy.innerHTML =
                            '<i class="bi bi-check-circle-fill"></i>';
                        setTimeout(() => {
                            btnCopy.innerHTML =
                                '<i class="bi bi-link-45deg fs-5"></i>  ';
                        }, 1500);
                    });
                });
            }

            // Attribution on copy (existing code)
            document.addEventListener('copy', function(e) {
                const selection = window.getSelection();
                const selectedText = selection.toString();

                if (!selectedText) return;

                const articleTitle = @json($post->title ?? document . title);
                const articleUrl = window.location.href;
                const websiteName = "Electrons";

                const attribution =
                    `\n\nArtikel ini telah tayang di ${websiteName} dengan judul "${articleTitle}". Baca selengkapnya di: ${articleUrl}`;

                const modifiedText = selectedText + attribution;

                e.clipboardData.setData('text/plain', modifiedText);
                e.preventDefault();
            });

            // Toggle reply form
            document.querySelectorAll('.reply-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const commentId = btn.getAttribute('data-comment-id');
                    const form = btn.closest('.comment').querySelector('.reply-form');
                    form.classList.toggle('d-none');
                    form.querySelector('textarea').focus();
                });
            });

            // Like/Unlike Comment
            document.querySelectorAll('.like-btn').forEach(function(btn) {
                btn.addEventListener('click', function() {
                    const commentId = btn.getAttribute('data-comment-id');
                    fetch("{{ url('/comments') }}/" + commentId + "/like", {
                            method: "POST",
                            headers: {
                                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                                'Accept': 'application/json'
                            }
                        })
                        .then(res => res.json())
                        .then(data => {
                            // Update icon and count
                            if (data.likes) {
                                btn.querySelector('i').classList.toggle(
                                    'bi-hand-thumbs-up-fill', data.likes.some(like => like
                                        .user_id == {{ auth()->id() }}));
                                btn.querySelector('i').classList.toggle('text-primary', data
                                    .likes.some(like => like.user_id ==
                                        {{ auth()->id() }}));
                                btn.querySelector('i').classList.toggle('bi-hand-thumbs-up', !
                                    data.likes.some(like => like.user_id ==
                                        {{ auth()->id() }}));
                                btn.querySelector('.like-count').textContent = data.likes
                                    .length;
                            } else {
                                // fallback: reload
                                location.reload();
                            }
                        });
                });
            });
        });
    </script>
@endpush
