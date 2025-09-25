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
                                <div class="small text-info">{{ '@' . $thread->user->username }}</div>
                            </div>
                            <div class="ms-auto d-flex align-items-stretch">
                                @can('update', $thread)
                                    <a aria-label="Edit thread"
                                       class="btn btn-sm btn-outline-primary me-2 d-flex align-items-center justify-content-center"
                                       href="{{ route('comunity.edit', $thread->id) }}"
                                       id="threadEditBtn"
                                       title="Edit thread">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan

                                @can('delete', $thread)
                                    <button aria-label="Hapus thread"
                                            class="btn btn-sm btn-outline-danger me-2 d-flex align-items-center justify-content-center"
                                            data-bs-target="#threadDeleteModal"
                                            data-bs-toggle="modal"
                                            id="threadDeleteBtn"
                                            title="Hapus thread"
                                            type="button">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan

                                @can('toggleDone', $thread)
                                    <button class="btn btn-sm btn-outline-success me-2 d-flex align-items-center justify-content-center"
                                            data-url="{{ route('comunity.toggleDone', $thread->id) }}"
                                            id="threadToggleDoneBtn"
                                            type="button">
                                        {{ $thread->is_done ? 'Buka Kembali' : 'Tandai Sudah Terjawab' }}
                                    </button>
                                @endcan

                                <button aria-label="Like thread"
                                        class="btn btn-sm btn-outline-primary me-2 d-flex align-items-center justify-content-center thread-like-btn"
                                        data-id="{{ $thread->id }}"
                                        data-url="{{ route('comunity.like', $thread->id) }}"
                                        id="threadLikeBtn"
                                        title="Suka">
                                    <i
                                       class="bi {{ $thread->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                                    <span class="thread-like-count ms-1">{{ $thread->likes->count() }}</span>
                                </button>

                                <button aria-label="Bookmark thread"
                                        class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center thread-bookmark-btn"
                                        data-id="{{ $thread->id }}"
                                        data-url="{{ route('comunity.bookmark', $thread->id) }}"
                                        id="threadBookmarkBtn"
                                        title="Simpan">
                                    <i
                                       class="bi {{ $thread->bookmarks->where('user_id', auth()->id())->count() ? 'bi-bookmark-fill text-warning' : 'bi-bookmark' }}"></i>
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
                        <h5 class="mb-0 fw-bold">{{ $thread->comments->count() }} Komentar</h5>
                    </div>
                    <div class="card-body">
                        @auth
                            @if (!$thread->is_done)
                                <form action="{{ route('comunity.comments.store', $thread->id) }}"
                                      id="comment-form"
                                      method="POST">
                                    @csrf
                                    <div id="quill-editor"
                                         style="height:150px;"></div>
                                    <input id="comment-body"
                                           name="body"
                                           type="hidden">
                                    <div class="mt-2 text-end">
                                        <button class="btn btn-primary"
                                                type="submit">Kirim Komentar</button>
                                    </div>
                                </form>
                            @else
                                <div class="alert alert-success small">Diskusi ditandai <strong>Sudah Terjawab</strong>. Anda
                                    tidak dapat menambahkan komentar baru.</div>
                            @endif
                        @else
                            <p class="text-muted">Silakan <a href="{{ route('auth.show.login') }}">masuk</a> untuk berkomentar.
                            </p>
                        @endauth

                        <hr>

                        @include('threads.partials._comments-list')
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@include('threads.partials._modals')

@section('scripts')
    <link href="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.snow.css"
          rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/quill@2.0.3/dist/quill.min.js"></script>
    <script>
        const quill = new Quill('#quill-editor', {
            theme: 'snow',
            modules: {
                toolbar: [
                    [{
                        header: [2, 3, false]
                    }],
                    ['bold', 'italic', 'underline', 'strike'],
                    ['blockquote', 'code-block'],
                    [{
                        list: 'ordered'
                    }, {
                        list: 'bullet'
                    }],
                    ['link'],
                    ['clean']
                ]
            }
        });
    </script>
    <script>
        (function() {
            function submitPlainPost(url, method = 'POST') {
                if (!url) return;
                const form = document.createElement('form');
                form.method = 'POST';
                form.action = url;
                form.style.display = 'none';

                const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';
                const inputToken = document.createElement('input');
                inputToken.type = 'hidden';
                inputToken.name = '_token';
                inputToken.value = token;
                form.appendChild(inputToken);

                if (method && method.toUpperCase() !== 'POST') {
                    const inputMethod = document.createElement('input');
                    inputMethod.type = 'hidden';
                    inputMethod.name = '_method';
                    inputMethod.value = method.toUpperCase();
                    form.appendChild(inputMethod);
                }

                document.body.appendChild(form);
                form.submit();
            }

            // thread toggle done
            document.getElementById('threadToggleDoneBtn')?.addEventListener('click', function(e) {
                const url = this.dataset.url;
                if (!url) return;
                submitPlainPost(url, 'POST');
            });

            // thread like
            document.getElementById('threadLikeBtn')?.addEventListener('click', function(e) {
                const url = this.dataset.url;
                if (!url) return;
                submitPlainPost(url, 'POST');
            });

            // thread bookmark
            document.getElementById('threadBookmarkBtn')?.addEventListener('click', function(e) {
                const url = this.dataset.url;
                if (!url) return;
                submitPlainPost(url, 'POST');
            });

            // thread delete: wire modal confirm button to submit a plain POST to the delete route
            const threadDeleteBtn = document.getElementById('threadDeleteBtn');
            if (threadDeleteBtn) {
                // The button already has data-bs-toggle/data-bs-target to open the modal
                // Ensure the confirm button in the modal submits to the appropriate route
                const confirm = document.getElementById('threadDeleteConfirmBtn');
                if (confirm) {
                    confirm.addEventListener('click', function(ev) {
                        // Attempt to find a data-url on the original button, otherwise build from route pattern
                        const url = threadDeleteBtn.dataset.url ||
                            '{{ route('comunity.destroy', $thread->id) }}';
                        submitPlainPost(url, 'DELETE');
                    });
                }
            }
        })();
    </script>
@endsection
