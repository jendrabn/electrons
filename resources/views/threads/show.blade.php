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
                                       href="{{ route('comunity.edit', $thread->slug) }}"
                                       title="Edit thread">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan

                                @can('delete', $thread)
                                    <button aria-label="Hapus thread"
                                            class="btn btn-sm btn-outline-danger me-2 d-flex align-items-center justify-content-center"
                                            data-url="{{ route('comunity.destroy', $thread->slug) }}"
                                            id="threadDeleteBtn"
                                            title="Hapus thread"
                                            type="button">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                @endcan

                                @can('toggleDone', $thread)
                                    <button class="btn btn-sm btn-outline-success me-2 d-flex align-items-center justify-content-center"
                                            data-url="{{ route('comunity.toggleDone', $thread->slug) }}"
                                            id="threadToggleDoneBtn"
                                            type="button">
                                        {{ $thread->is_done ? 'Buka Kembali' : 'Tandai Sudah Terjawab' }}
                                    </button>
                                @endcan

                                <button aria-label="Like thread"
                                        class="btn btn-sm btn-outline-primary me-2 d-flex align-items-center justify-content-center thread-like-btn"
                                        data-id="{{ $thread->id }}"
                                        data-url="{{ route('comunity.like', $thread->slug) }}"
                                        title="Suka">
                                    <i
                                       class="bi {{ $thread->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                                    <span class="thread-like-count ms-1">{{ $thread->likes->count() }}</span>
                                </button>

                                <button aria-label="Bookmark thread"
                                        class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center thread-bookmark-btn"
                                        data-id="{{ $thread->id }}"
                                        data-url="{{ route('comunity.bookmark', $thread->slug) }}"
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
                                <form action="{{ route('comunity.comments.store', $thread->slug) }}"
                                      id="comment-form"
                                      method="POST">
                                    @csrf
                                    <div id="quill-editor"
                                         style="height:150px;"></div>
                                    <input id="comment-body"
                                           name="body"
                                           type="hidden">
                                    <input id="comment-parent-id"
                                           name="parent_id"
                                           type="hidden">
                                    <div class="mt-2 text-end">
                                        <button class="btn btn-primary">Kirim Komentar</button>
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

        // Toast helper using Bootstrap 5 Toasts
        function showToast(type, message) {
            try {
                const containerId = 'toast-container';
                let container = document.getElementById(containerId);
                if (!container) {
                    container = document.createElement('div');
                    container.id = containerId;
                    container.className = 'position-fixed top-0 end-0 p-3';
                    container.style.zIndex = '1080';
                    document.body.appendChild(container);
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'toast align-items-center text-white text-bg-' + (type === 'success' ? 'success' : (
                    type ===
                    'info' ? 'info' : 'danger')) + ' border-0';
                wrapper.role = 'alert';
                wrapper.setAttribute('aria-live', 'assertive');
                wrapper.setAttribute('aria-atomic', 'true');

                wrapper.innerHTML =
                    `\n                    <div class="d-flex">\n                        <div class="toast-body">${message}</div>\n                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>\n                    </div>\n                `;

                container.appendChild(wrapper);
                const toast = new bootstrap.Toast(wrapper, {
                    delay: 5000
                });
                toast.show();
                wrapper.addEventListener('hidden.bs.toast', function() {
                    wrapper.remove();
                });
            } catch (e) {
                console.error('showToast failed', e);
                alert(message);
            }
        }

        document.getElementById('comment-form')?.addEventListener('submit', function(e) {
            const html = quill.root.innerHTML.trim();
            if (!html || html === '<p><br></p>') {
                e.preventDefault();
                showToast('danger', 'Komentar tidak boleh kosong');
                return;
            }
            document.getElementById('comment-body').value = html;
        });

        // reply button wiring
        document.addEventListener('click', function(e) {
            if (e.target.matches('.reply-btn')) {
                e.preventDefault();
                const id = e.target.getAttribute('data-id');
                document.getElementById('comment-parent-id').value = id;
                document.getElementById('quill-editor').scrollIntoView({
                    behavior: 'smooth'
                });
                quill.focus();
            }
        });

        // AJAX like handler for comment like buttons
        document.addEventListener('click', function(e) {
            const likeBtn = e.target.closest('.comment-like-btn');
            if (!likeBtn) return;
            e.preventDefault();

            const url = likeBtn.getAttribute('data-url');
            const id = likeBtn.getAttribute('data-id');

            console.log('sending like POST to', url);
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            }).then(response => {
                console.log('like response status', response.status);
                return response.json().catch(err => {
                    console.error('failed parse json', err);
                    throw err;
                });
            }).then(data => {
                console.log('like response data', data);
                if (data.success) {
                    const icon = likeBtn.querySelector('i.bi');
                    const count = likeBtn.querySelector('.like-count');
                    if (data.liked) {
                        // toggle to filled thumb-up
                        icon.classList.remove('bi-hand-thumbs-up');
                        icon.classList.add('bi-hand-thumbs-up-fill', 'text-primary');
                    } else {
                        icon.classList.remove('bi-hand-thumbs-up-fill', 'text-primary');
                        icon.classList.add('bi-hand-thumbs-up');
                    }
                    if (count) count.textContent = data.count;
                }
            }).catch(err => {
                console.error('Like request failed', err);
                showToast('danger', 'Gagal meng-update like');
            });
        });

        // Delete with confirmation modal
        document.addEventListener('click', function(e) {
            const deleteBtn = e.target.closest('.comment-delete');
            if (!deleteBtn) return;
            e.preventDefault();

            const id = deleteBtn.getAttribute('data-id');
            const url = deleteBtn.getAttribute('data-url');

            const modalEl = document.getElementById('commentDeleteModal');
            if (!modalEl) {
                // fallback to direct delete if modal missing
                if (!confirm('Hapus komentar ini?')) return;
                performDelete(url, id);
                return;
            }

            const confirmBtn = modalEl.querySelector('#commentDeleteConfirmBtn');
            const modalBody = modalEl.querySelector('.modal-body');
            // optional: show id or snippet
            modalBody.textContent = 'Yakin ingin menghapus komentar ini?';
            confirmBtn.dataset.url = url;
            confirmBtn.dataset.id = id;

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });

        // performDelete helper used by modal confirm or fallback
        function performDelete(url, id) {
            fetch(url, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    const node = document.getElementById('comment-' + data.id);
                    if (node) node.remove();
                    showToast('success', 'Komentar dihapus.');
                } else {
                    showToast('danger', (data && data.message) ? data.message : 'Gagal menghapus komentar');
                }
            }).catch(() => showToast('danger', 'Gagal menghapus komentar'));
        }
    </script>
    {{-- modals moved to partial: threads.partials._modals --}}

    <script>
        // thread delete modal wiring
        document.getElementById('threadDeleteBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const url = btn.dataset.url;
            const modalEl = document.getElementById('threadDeleteModal');
            const modal = new bootstrap.Modal(modalEl);
            modalEl.dataset.url = url;
            modal.show();
        });

        document.getElementById('threadDeleteConfirmBtn')?.addEventListener('click', function(e) {
            const modalEl = document.getElementById('threadDeleteModal');
            const url = modalEl.dataset.url;
            if (!url) return;
            const btn = e.currentTarget;
            btn.disabled = true;

            fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'Accept': 'application/json',
                }
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    // redirect to community index
                    window.location.href = '{{ route('comunity.index') }}';
                } else {
                    showToast('danger', (data && data.message) ? data.message : 'Gagal menghapus thread');
                }
            }).catch(err => {
                console.error('Delete thread failed', err);
                showToast('danger', 'Gagal menghapus thread');
            }).finally(() => btn.disabled = false);
        });
    </script>
    <script>
        // Edit modal: fetch HTML form and open modal
        document.addEventListener('click', function(e) {
            if (!e.target.closest('.comment-edit')) return;
            e.preventDefault();
            const btn = e.target.closest('.comment-edit');
            const id = btn.getAttribute('data-id');
            // build URL: we need thread slug (available in page) and comment id
            const threadSlug = '{{ $thread->slug }}';
            const url = `/comunity/${threadSlug}/comments/${id}/edit`;

            fetch(url, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                credentials: 'same-origin'
            }).then(r => r.json()).then(data => {
                const modalBody = document.getElementById('commentEditModalBody');
                modalBody.innerHTML = data.html;
                const modalEl = document.getElementById('commentEditModal');
                const modal = new bootstrap.Modal(modalEl);
                modal.show();

                // wire up the form submission via AJAX and initialize Quill
                const form = document.getElementById('comment-edit-form');
                if (!form) return;

                // initialize Quill on the edit container; use the hidden input's value as source
                try {
                    const hiddenInput = form.querySelector('input[name="body"]');
                    const quillContainer = form.querySelector('[id^="quill-edit-"]');
                    if (quillContainer) {
                        // create a Quill instance and attach to form to allow re-use
                        const quillInstance = new Quill(quillContainer, {
                            theme: 'snow'
                        });
                        // populate editor with existing HTML from hidden input (browser decodes entities)
                        if (hiddenInput && hiddenInput.value) {
                            quillInstance.root.innerHTML = hiddenInput.value;
                        }
                        // store reference so we can access it on submit
                        form._quill = quillInstance;
                    }
                } catch (initErr) {
                    console.error('Failed to initialize Quill in edit modal', initErr);
                }

                // handle submit: copy quill HTML into hidden input before sending
                form.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    const action = form.getAttribute('action');

                    // if quill is present, sync its HTML into the hidden input
                    if (form._quill) {
                        const html = form._quill.root.innerHTML.trim();
                        const hidden = form.querySelector('input[name="body"]');
                        if (hidden) hidden.value = html;
                        if (!html || html === '<p><br></p>') {
                            showToast('danger', 'Komentar tidak boleh kosong');
                            return;
                        }
                    }

                    const formData = new FormData(form);

                    fetch(action, {
                        method: 'POST',
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        },
                        body: formData
                    }).then(r => r.json()).then(resp => {
                        if (resp.success) {
                            // update comment body in DOM
                            const node = document.querySelector('#comment-' + resp.id +
                                ' .comment-body');
                            if (node) node.innerHTML = resp.body;
                            modal.hide();
                        } else if (resp.errors) {
                            // show validation errors as toast
                            const messages = Object.values(resp.errors).flat().join('\n');
                            showToast('danger', messages || 'Terjadi kesalahan');
                        }
                    }).catch(err => {
                        console.error('Edit submit failed', err);
                        showToast('danger', 'Gagal menyimpan perubahan');
                    });
                });
            }).catch(err => {
                console.error('Failed to load edit form', err);
                showToast('danger', 'Gagal memuat form edit');
            });
        });

        // show server-side flash messages (status / error) as toasts
        document.addEventListener('DOMContentLoaded', function() {
            @if (session('status'))
                showToast('success', @json(session('status')));
            @endif

            @if (session('error'))
                showToast('danger', @json(session('error')));
            @endif
        });

        // wire delete confirmation modal confirm button
        document.addEventListener('click', function(e) {
            if (!e.target.matches('#commentDeleteConfirmBtn')) return;
            const btn = e.target;
            const url = btn.dataset.url;
            const id = btn.dataset.id;
            const modalEl = document.getElementById('commentDeleteModal');
            // hide modal first
            const modal = bootstrap.Modal.getInstance(modalEl);
            if (modal) modal.hide();
            performDelete(url, id);
        });

        // Toggle thread 'is_done' via AJAX
        document.getElementById('threadToggleDoneBtn')?.addEventListener('click', function(e) {
            e.preventDefault();
            const btn = e.currentTarget;
            const url = btn.dataset.url;
            if (!url) return;

            btn.disabled = true;
            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    const isDone = !!data.is_done;
                    btn.textContent = isDone ? 'Buka Kembali' : 'Tandai Sudah Terjawab';

                    // hide or show the create form / alert
                    const form = document.getElementById('comment-form');
                    if (form) form.style.display = isDone ? 'none' : '';

                    // optionally insert/remove the alert that indicates answered
                    let alertEl = document.querySelector('.thread-answered-alert');
                    if (isDone) {
                        if (!alertEl) {
                            alertEl = document.createElement('div');
                            alertEl.className = 'alert alert-success small thread-answered-alert';
                            alertEl.textContent =
                                'Diskusi ditandai Sudah Terjawab. Anda tidak dapat menambahkan komentar baru.';
                            const cardBody = document.querySelector('.card-body');
                            // insert before the <hr> which precedes the comments list
                            const hr = cardBody.querySelector('hr');
                            if (hr) hr.parentNode.insertBefore(alertEl, hr);
                        }
                    } else {
                        if (alertEl) alertEl.remove();
                    }

                    showToast('success', isDone ? 'Thread ditandai sudah terjawab.' :
                        'Thread dibuka kembali.');
                } else {
                    showToast('danger', (data && data.message) ? data.message :
                        'Gagal mengubah status thread');
                }
            }).catch(err => {
                console.error('Toggle done failed', err);
                showToast('danger', 'Gagal mengubah status thread');
            }).finally(() => btn.disabled = false);
        });

        // AJAX like handler for thread like button
        document.addEventListener('click', function(e) {
            const likeBtn = e.target.closest('.thread-like-btn');
            if (!likeBtn) return;
            e.preventDefault();

            const url = likeBtn.getAttribute('data-url');
            const id = likeBtn.getAttribute('data-id');
            if (!url) return;

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            }).then(response => response.json()).then(data => {
                if (data.success) {
                    const icon = likeBtn.querySelector('i.bi');
                    const count = likeBtn.querySelector('.thread-like-count');
                    if (data.liked) {
                        icon.classList.remove('bi-hand-thumbs-up');
                        icon.classList.add('bi-hand-thumbs-up-fill', 'text-primary');
                    } else {
                        icon.classList.remove('bi-hand-thumbs-up-fill', 'text-primary');
                        icon.classList.add('bi-hand-thumbs-up');
                    }
                    if (count) count.textContent = data.count;
                }
            }).catch(err => {
                console.error('Thread like failed', err);
                showToast('danger', 'Gagal meng-update like thread');
            });
        });

        // AJAX bookmark handler for thread bookmark button
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('.thread-bookmark-btn');
            if (!btn) return;
            e.preventDefault();

            const url = btn.getAttribute('data-url');
            if (!url) return;

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({})
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    const icon = btn.querySelector('i.bi');
                    if (data.bookmarked) {
                        icon.classList.remove('bi-bookmark');
                        icon.classList.add('bi-bookmark-fill', 'text-primary');
                    } else {
                        icon.classList.remove('bi-bookmark-fill', 'text-primary');
                        icon.classList.add('bi-bookmark');
                    }
                    showToast('success', data.bookmarked ? 'Thread disimpan.' : 'Bookmark dihapus.');
                } else if (data && data.message) {
                    showToast('danger', data.message);
                }
            }).catch(err => {
                console.error('Bookmark request failed', err);
                showToast('danger', 'Gagal menyimpan bookmark');
            });
        });
    </script>
@endsection
