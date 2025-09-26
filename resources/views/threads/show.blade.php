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
                                <div class="fw-normal">
                                    <a class="text-primary fw-semibold text-decoration-none"
                                       href="{{ route('users.show', $thread->user->id) }}">{{ '@' . $thread->user->username }}</a>
                                    <span class="mx-1 text-muted">&bull;</span>
                                    <small class="text-muted"> {{ $thread->created_at->diffForHumans() }}</small>
                                </div>
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
                        <h5 class="mb-0 fw-bold">{{ $thread->comments->count() }} Jawaban</h5>
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
                                                type="submit">
                                            <i class="bi bi-send"></i> Kirim
                                        </button>
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
        // Initialize Quill for the main comment composer and bind form submit
        document.addEventListener('DOMContentLoaded', function() {
            const quillEl = document.getElementById('quill-editor');
            const commentForm = document.getElementById('comment-form');
            const commentBodyInput = document.getElementById('comment-body');

            if (quillEl && typeof Quill !== 'undefined') {
                try {
                    // expose global `quill` so other scripts (reply fallback) can focus it
                    window.quill = new Quill('#quill-editor', {
                        theme: 'snow',
                        modules: {
                            toolbar: [
                                ['bold', 'italic', 'underline', 'strike'],
                                [{
                                    'list': 'ordered'
                                }, {
                                    'list': 'bullet'
                                }],
                                ['link'],
                                ['clean']
                            ]
                        }
                    });
                } catch (err) {
                    console.error('Quill init failed (main composer)', err);
                }
            }

            if (commentForm) {
                commentForm.addEventListener('submit', function(e) {
                    // if Quill is present, copy its HTML into the hidden input
                    if (window.quill) {
                        const html = window.quill.root.innerHTML || '';
                        const text = window.quill.getText ? window.quill.getText().trim() : html.replace(
                            /<[^>]*>/g, '').trim();
                        if (!text) {
                            e.preventDefault();
                            showToast('danger', 'Komentar tidak boleh kosong');
                            return;
                        }
                        if (commentBodyInput) commentBodyInput.value = html;
                    } else {
                        // fallback: ensure hidden input has some value
                        const val = commentBodyInput ? commentBodyInput.value : '';
                        if (!val || !val.trim()) {
                            e.preventDefault();
                            showToast('danger', 'Komentar tidak boleh kosong');
                            return;
                        }
                    }
                });
            }
        });
    </script>
    <script>
        // AJAX edit flow for comments and replies (open edit form in modal and submit via AJAX)
        document.addEventListener('click', function(e) {
            // handle both comment-edit and reply-edit buttons
            const editBtn = e.target.closest('.comment-edit, .reply-edit');
            if (!editBtn) return;
            e.preventDefault();

            const id = editBtn.getAttribute('data-id');
            const threadId = '{{ $thread->id }}';
            const btnUrl = editBtn.getAttribute('data-url');
            const url = btnUrl ? btnUrl : `/comunity/${threadId}/comments/${id}/edit`;

            (async function() {
                try {
                    const res = await fetch(url, {
                        credentials: 'same-origin',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();

                    // decide which modal to use based on button class
                    const isReply = editBtn.classList.contains('reply-edit');
                    const bodyId = isReply ? 'replyEditModalBody' : 'commentEditModalBody';
                    const modalId = isReply ? 'replyEditModal' : 'commentEditModal';

                    const modalBody = document.getElementById(bodyId);
                    if (!modalBody) return;
                    modalBody.innerHTML = data.html || '';

                    const modalEl = document.getElementById(modalId);
                    const modal = new bootstrap.Modal(modalEl);
                    modal.show();

                    // find the form inside returned HTML and wire AJAX submit
                    const form = modalBody.querySelector('form');
                    if (!form) return;

                    // initialize Quill for edit if present
                    const quillContainer = form.querySelector('[id^="edit-quill-editor-"]');
                    let localQuill = null;
                    if (quillContainer) {
                        try {
                            localQuill = new Quill('#' + quillContainer.id, {
                                theme: 'snow'
                            });
                        } catch (err) {
                            console.error('Quill init failed (edit)', err);
                        }
                    }

                    const submitHandler = async function(ev) {
                        ev.preventDefault();

                        if (localQuill) {
                            const hidden = form.querySelector('input[name="body"]');
                            if (hidden) hidden.value = localQuill.root.innerHTML.trim();
                        }

                        const bodyField = form.querySelector('textarea[name="body"]') || form
                            .querySelector('input[name="body"]');
                        if (!bodyField || !bodyField.value.trim()) {
                            showToast('danger', 'Komentar tidak boleh kosong');
                            return;
                        }

                        const action = form.getAttribute('action');
                        const fd = new FormData(form);
                        try {
                            const r = await fetch(action, {
                                method: 'POST',
                                credentials: 'same-origin',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                },
                                body: fd
                            });
                            const resp = await r.json();
                            if (resp && resp.success) {
                                if (resp.html) {
                                    const wrapper = document.createElement('div');
                                    wrapper.innerHTML = resp.html;
                                    const newNode = wrapper.firstElementChild;
                                    const oldNode = document.getElementById('comment-' + resp.id);
                                    if (oldNode && newNode) oldNode.replaceWith(newNode);
                                } else if (resp.body) {
                                    const node = document.querySelector('#comment-' + resp.id +
                                        ' .comment-body');
                                    if (node) node.innerHTML = resp.body;
                                }
                                // hide modal
                                try {
                                    const inst = bootstrap.Modal.getInstance(modalEl);
                                    if (inst) inst.hide();
                                } catch (e) {
                                    /* ignore */
                                }
                                showToast('success', resp.message || 'Komentar disimpan.');
                            } else if (resp.errors) {
                                const messages = Object.values(resp.errors).flat().join('\n');
                                showToast('danger', messages || 'Terjadi kesalahan');
                            } else if (resp && resp.message) {
                                showToast('danger', resp.message);
                            }
                        } catch (ex) {
                            console.error('Edit submit failed', ex);
                            showToast('danger', 'Gagal menyimpan perubahan');
                        }
                    };

                    form.removeEventListener('submit', submitHandler);
                    form.addEventListener('submit', submitHandler);
                } catch (err) {
                    console.error('Failed to load edit form', err);
                    showToast('danger', 'Gagal memuat form edit');
                }
            })();
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
    <script>
        // AJAX handler for comment like buttons
        document.addEventListener('click', function(e) {
            // handle both comment-like-btn and reply-like-btn
            const likeBtn = e.target.closest('.comment-like-btn, .reply-like-btn');
            if (!likeBtn) return;
            e.preventDefault();

            const url = likeBtn.getAttribute('data-url');
            if (!url) return;

            fetch(url, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                        'content') || '',
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    const icon = likeBtn.querySelector('i.bi');
                    const countEl = likeBtn.querySelector('.like-count');
                    if (data.liked) {
                        if (icon) {
                            icon.classList.remove('bi-hand-thumbs-up');
                            icon.classList.add('bi-hand-thumbs-up-fill', 'text-primary');
                        }
                    } else {
                        if (icon) {
                            icon.classList.remove('bi-hand-thumbs-up-fill', 'text-primary');
                            icon.classList.add('bi-hand-thumbs-up');
                        }
                    }
                    if (countEl && typeof data.count !== 'undefined') countEl.textContent = data.count;
                } else if (data && data.message) {
                    showToast('danger', data.message);
                }
            }).catch(err => {
                console.error('Comment like failed', err);
                showToast('danger', 'Gagal meng-update like');
            });
        });
    </script>
    <script>
        // Delete comment flow: open modal and send AJAX delete on confirm
        (function() {
            // when a delete button is clicked, show the modal and set confirm data
            document.addEventListener('click', function(e) {
                // handle both comment and reply delete buttons
                const del = e.target.closest('.comment-delete, .reply-delete');
                if (!del) return;
                e.preventDefault();

                const id = del.dataset.id;
                const url = del.dataset.url;

                const modalEl = document.getElementById('commentDeleteModal');
                if (!modalEl) {
                    if (!confirm('Yakin ingin menghapus komentar ini?')) return;
                    // fallback to AJAX delete directly
                    doCommentDelete(url, id);
                    return;
                }

                const modalBody = modalEl.querySelector('.modal-body');
                const confirmBtn = modalEl.querySelector('#commentDeleteConfirmBtn');
                if (modalBody) modalBody.textContent = 'Yakin ingin menghapus komentar ini?';
                if (confirmBtn) {
                    confirmBtn.dataset.url = url || '';
                    confirmBtn.dataset.id = id || '';
                }
                const modal = new bootstrap.Modal(modalEl);
                modal.show();
            });

            // actual AJAX delete function
            async function doCommentDelete(url, id) {
                if (!url) return;
                try {
                    const res = await fetch(url, {
                        method: 'DELETE',
                        credentials: 'same-origin',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute(
                                'content') || '',
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await res.json();
                    if (data && data.success) {
                        // remove the comment node
                        const node = document.getElementById('comment-' + (data.id || id));
                        if (node) node.remove();

                        // if server returned updated reply count for parent, update UI
                        if (data.parent_id && typeof data.count !== 'undefined') {
                            const toggleBtn = document.querySelector('[data-bs-target="#repliesCollapse' + data
                                .parent_id + '"]');
                            if (toggleBtn) toggleBtn.innerHTML = '<i class="bi bi-chat-left-text"></i> Lihat ' +
                                data.count + ' Balasan';
                        }

                        // hide modal if present and show toast
                        const modalEl = document.getElementById('commentDeleteModal');
                        if (modalEl) {
                            try {
                                const inst = bootstrap.Modal.getInstance(modalEl);
                                if (inst) inst.hide();
                            } catch (e) {
                                /* ignore */
                            }
                        }
                        showToast('success', data.message || 'Komentar dihapus.');
                    } else {
                        showToast('danger', (data && data.message) ? data.message : 'Gagal menghapus komentar');
                    }
                } catch (err) {
                    console.error('Delete comment failed', err);
                    showToast('danger', 'Gagal menghapus komentar');
                }
            }

            // confirm button click -> perform AJAX delete
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('#commentDeleteConfirmBtn');
                if (!btn) return;
                e.preventDefault();
                const url = btn.dataset.url;
                const id = btn.dataset.id;
                // disable briefly to prevent double click
                btn.disabled = true;
                doCommentDelete(url, id).finally(() => {
                    setTimeout(() => {
                        btn.disabled = false;
                    }, 1000);
                });
            });
        })();
    </script>
    <script>
        // Inline reply flow: open reply collapse, prefill @username, and submit via AJAX
        (function() {
            // clicking reply button opens collapse and prefills textarea with @username
            document.addEventListener('click', function(e) {
                const btn = e.target.closest('.reply-btn');
                if (!btn) return;
                e.preventDefault();

                const target = btn.getAttribute('data-bs-target') || btn.getAttribute('data-target');
                const parentId = btn.getAttribute('data-id');
                const username = btn.getAttribute('data-username');

                if (target) {
                    const collapseEl = document.querySelector(target);
                    if (collapseEl) {
                        const onShown = function() {
                            collapseEl.removeEventListener('shown.bs.collapse', onShown);
                            const ta = collapseEl.querySelector('textarea[name="body"]');
                            if (ta) {
                                if (username && (!ta.value || ta.value.trim() === '' || ta.value.trim() ===
                                        ('@' + username))) {
                                    ta.value = '@' + username + ' ';
                                }
                                ta.focus();
                                ta.selectionStart = ta.selectionEnd = ta.value.length;
                            }
                        };
                        collapseEl.addEventListener('shown.bs.collapse', onShown);
                        const inst = bootstrap.Collapse.getOrCreateInstance(collapseEl);
                        inst.show();
                        return;
                    }
                }

                // fallback: focus main quill composer
                const quillEl = document.getElementById('quill-editor');
                if (quillEl && typeof quill !== 'undefined') {
                    quillEl.scrollIntoView({
                        behavior: 'smooth'
                    });
                    try {
                        quill.focus();
                    } catch (e) {
                        /* ignore */
                    }
                }
            });

            // submit inline reply forms via AJAX
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('.reply-form');
                if (!form) return;
                e.preventDefault();

                const action = form.getAttribute('action');
                const fd = new FormData(form);
                const body = fd.get('body');
                if (!body || !body.toString().trim()) {
                    showToast('danger', 'Balasan tidak boleh kosong');
                    return;
                }

                fetch(action, {
                    method: 'POST',
                    credentials: 'same-origin',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: fd
                }).then(r => r.json()).then(data => {
                    if (data && data.success) {
                        const parentId = data.parent_id || fd.get('parent_id');
                        const repliesContainer = document.getElementById('repliesCollapse' + parentId);
                        if (repliesContainer) {
                            const inst = bootstrap.Collapse.getOrCreateInstance(repliesContainer);
                            inst.show();
                            if (data.html) {
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = data.html;
                                repliesContainer.appendChild(wrapper.firstElementChild);
                            }
                        } else {
                            // create toggle button if missing
                            const parent = document.querySelector('#comment-' + parentId +
                                ' .d-flex.align-items-center.gap-3');
                            if (parent) {
                                const btn = document.createElement('button');
                                btn.className = 'btn btn-link btn-sm p-0 text-decoration-none';
                                btn.setAttribute('data-bs-target', '#repliesCollapse' + parentId);
                                btn.setAttribute('data-bs-toggle', 'collapse');
                                btn.innerHTML = '<i class="bi bi-chat-left-text"></i> Lihat 1 Balasan';
                                parent.appendChild(btn);
                            }
                        }

                        // update toggle button text with count if provided
                        const toggleBtn = document.querySelector('[data-bs-target="#repliesCollapse' +
                            parentId + '"]');
                        if (toggleBtn) toggleBtn.innerHTML =
                            '<i class="bi bi-chat-left-text"></i> Lihat ' + (typeof data.count !==
                                'undefined' ? data.count : '') + ' Balasan';

                        // clear textarea and hide collapse
                        const ta = form.querySelector('textarea[name="body"]');
                        if (ta) ta.value = '';
                        const collapseEl = form.closest('.collapse');
                        if (collapseEl) {
                            const inst2 = bootstrap.Collapse.getInstance(collapseEl);
                            if (inst2) inst2.hide();
                        }

                        showToast('success', data.message || 'Balasan dikirim.');
                    } else if (data && data.errors) {
                        const messages = Object.values(data.errors).flat().join('\n');
                        showToast('danger', messages || 'Gagal mengirim balasan');
                    } else {
                        showToast('danger', (data && data.message) ? data.message :
                            'Gagal mengirim balasan');
                    }
                }).catch(err => {
                    console.error('Reply submit failed', err);
                    showToast('danger', 'Gagal mengirim balasan');
                });
            });
        })();
    </script>
@endsection
