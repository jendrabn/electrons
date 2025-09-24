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
                                       title="Edit thread">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                @endcan

                                @can('delete', $thread)
                                    <button aria-label="Hapus thread"
                                            class="btn btn-sm btn-outline-danger me-2 d-flex align-items-center justify-content-center"
                                            data-url="{{ route('comunity.destroy', $thread->id) }}"
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
                                        title="Suka">
                                    <i
                                       class="bi {{ $thread->likes->where('user_id', auth()->id())->count() ? 'bi-hand-thumbs-up-fill text-primary' : 'bi-hand-thumbs-up' }}"></i>
                                    <span class="thread-like-count ms-1">{{ $thread->likes->count() }}</span>
                                </button>

                                <button aria-label="Bookmark thread"
                                        class="btn btn-sm btn-outline-warning d-flex align-items-center justify-content-center thread-bookmark-btn"
                                        data-id="{{ $thread->id }}"
                                        data-url="{{ route('comunity.bookmark', $thread->id) }}"
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
                                    {{-- <input id="comment-parent-id"
                                           name="parent_id"
                                           type="hidden"
                                           value="{{ $thread->id }}"> --}}
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
            const btn = e.target.closest('.reply-btn');
            if (!btn) return;
            e.preventDefault();

            // get collapse target and username (if any)
            const target = btn.getAttribute('data-bs-target') || btn.getAttribute('data-target');
            const targetId = btn.getAttribute('data-id');
            const username = btn.getAttribute('data-username');

            // set the hidden parent_id for the main Quill form (keeps compatibility)
            const parentInput = document.getElementById('comment-parent-id');
            if (parentInput) parentInput.value = targetId;

            if (target) {
                const collapseEl = document.querySelector(target);
                if (collapseEl) {
                    // when the collapse is shown, prefill and focus textarea
                    collapseEl.addEventListener('shown.bs.collapse', function onShown() {
                        collapseEl.removeEventListener('shown.bs.collapse', onShown);
                        const ta = collapseEl.querySelector('textarea[name="body"]');
                        if (ta) {
                            // if textarea is empty or only contains username, prefill with @username
                            if (username && (!ta.value || ta.value.trim() === '' || ta.value.trim() === (
                                    '@' + username))) {
                                ta.value = '@' + username + ' ';
                            }
                            ta.focus();
                            // move cursor to end
                            ta.selectionStart = ta.selectionEnd = ta.value.length;
                        }
                    });

                    // open the collapse (Bootstrap handles toggling via data attributes too)
                    const bsCollapse = bootstrap.Collapse.getOrCreateInstance(collapseEl);
                    bsCollapse.show();

                    // don't focus main Quill when using inline reply
                    return;
                }
            }

            // fallback: focus main quill for top-level reply if no inline form
            document.getElementById('quill-editor').scrollIntoView({
                behavior: 'smooth'
            });
            quill.focus();
        });

        // AJAX like handler for comment/reply like buttons
        document.addEventListener('click', function(e) {
            const likeBtn = e.target.closest('.comment-like-btn');
            if (!likeBtn) return;
            e.preventDefault();

            const url = likeBtn.getAttribute('data-url');
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
                if (data && data.success) {
                    const icon = likeBtn.querySelector('i.bi');
                    const count = likeBtn.querySelector('.like-count');
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
                    if (count && typeof data.count !== 'undefined') count.textContent = data.count;
                } else if (data && data.message) {
                    showToast('danger', data.message);
                }
            }).catch(err => {
                console.error('Comment like failed', err);
                showToast('danger', 'Gagal meng-update like komentar');
            });
        });

        // performDelete helper used by modal confirm or fallback
        function performDelete(url, id) {
            fetch(url, {
                method: 'DELETE',
                credentials: 'same-origin',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').
                    getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    const node = document.getElementById('comment-' + data.id);
                    if (node) node.remove();
                    // if server returned updated reply count for parent, update UI
                    if (data.parent_id && typeof data.count !== 'undefined') {
                        const btn = document.querySelector('[data-bs-target="#repliesCollapse' +
                            data.parent_id +
                            '"]');
                        if (btn) btn.innerHTML = '<i class="bi bi-chat-left-text"></i> Lihat ' +
                            data.count +
                            ' Balasan';
                        // if no replies left, collapse and hide replies container
                        if (data.count === 0) {
                            const repliesContainer = document.getElementById('repliesCollapse' +
                                data.parent_id);
                            if (repliesContainer) {
                                const inst = bootstrap.Collapse.getInstance(repliesContainer);
                                if (inst) inst.hide();
                            }
                        }
                    }
                    showToast('success', 'Komentar dihapus.');
                } else {
                    showToast('danger', (data && data.message) ? data.message :
                        'Gagal menghapus komentar');
                }
            }).catch(() => showToast('danger', 'Gagal menghapus komentar'));
        }

        // AJAX reply form submission (delegated)
        document.addEventListener('submit', function(e) {
            const form = e.target.closest('.reply-form');
            if (!form) return;
            e.preventDefault();

            const action = form.getAttribute('action');
            const formData = new FormData(form);
            // basic validation
            const body = formData.get('body');
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
                body: formData
            }).then(r => r.json()).then(data => {
                if (data && data.success) {
                    // insert returned HTML into replies container belonging to parent_id
                    const parentId = data.parent_id;
                    const repliesContainer = document.getElementById('repliesCollapse' +
                        parentId);
                    if (repliesContainer) {
                        // if replies container is collapsed and hidden, show it
                        const inst = bootstrap.Collapse.getOrCreateInstance(
                            repliesContainer);
                        inst.show();
                        // append the new reply HTML
                        const wrapper = document.createElement('div');
                        wrapper.innerHTML = data.html;
                        repliesContainer.appendChild(wrapper.firstElementChild);
                    } else {
                        // create the container if missing
                        const parent = document.querySelector('#comment-' + parentId +
                            ' .d-flex.align-items-center.gap-3');
                        if (parent) {
                            const btn = document.createElement('button');
                            btn.className = 'btn btn-link btn-sm p-0 text-decoration-none';
                            btn.setAttribute('data-bs-target', '#repliesCollapse' +
                                parentId);
                            btn.setAttribute('data-bs-toggle', 'collapse');
                            btn.innerHTML =
                                '<i class="bi bi-chat-left-text"></i> Lihat 1 Balasan';
                            parent.appendChild(btn);
                        }
                    }

                    // update the 'Lihat X Balasan' button text if count provided
                    if (typeof data.count !== 'undefined' && data.parent_id) {
                        const toggleBtn = document.querySelector(
                            '[data-bs-target="#repliesCollapse' + data
                            .parent_id + '"]');
                        if (toggleBtn) toggleBtn.innerHTML =
                            '<i class="bi bi-chat-left-text"></i> Lihat ' +
                            data.count + ' Balasan';
                    }

                    // clear the textarea and hide collapse
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
    </script>
    <script>
        // Fallback robust handlers for edit/delete buttons (ensures clicks always send requests)
        (function() {
            try {
                document.addEventListener('click', async function(e) {
                    // EDIT -- open modal with returned form
                    const editBtn = e.target.closest('.comment-edit');
                    if (editBtn) {
                        e.preventDefault();
                        const threadId = '{{ $thread->id }}';
                        const id = editBtn.getAttribute('data-id');
                        const btnUrl = editBtn.getAttribute('data-url');
                        const url = btnUrl ? btnUrl : `/comunity/${threadId}/comments/${id}/edit`;

                        try {
                            const res = await fetch(url, {
                                credentials: 'same-origin',
                                headers: {
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json'
                                }
                            });
                            const data = await res.json();
                            const modalBody = document.getElementById('commentEditModalBody');
                            modalBody.innerHTML = data.html || '';
                            const modalEl = document.getElementById('commentEditModal');
                            const modal = new bootstrap.Modal(modalEl);
                            modal.show();

                            const form = modalBody.querySelector('form');
                            if (!form) return;

                            // initialize Quill if present (same robust approach)
                            const quillContainer2 = modalBody.querySelector('[id^="edit-quill-editor-"]');
                            let localQuill2 = null;
                            if (quillContainer2) {
                                const initQuill2 = function() {
                                    if (localQuill2) return;
                                    try {
                                        localQuill2 = new Quill('#' + quillContainer2.id, {
                                            theme: 'snow'
                                        });
                                    } catch (err) {
                                        console.error('Quill init failed (fallback edit)', err);
                                    }
                                };
                                const modalEl2 = document.getElementById('commentEditModal');
                                if (modalEl2) {
                                    const onShown2 = function() {
                                        initQuill2();
                                        modalEl2.removeEventListener('shown.bs.modal', onShown2);
                                    };
                                    modalEl2.addEventListener('shown.bs.modal', onShown2);
                                }
                                setTimeout(initQuill2, 0);
                            }

                            // attach submit handler (ensure only one)
                            const submitHandler = async function(ev) {
                                ev.preventDefault();
                                // handle quill if present
                                const quillContainer = form.querySelector(
                                    '[id^="edit-quill-editor-"]');
                                let localQuill = null;
                                if (quillContainer) {
                                    localQuill = new Quill('#' + quillContainer.id, {
                                        theme: 'snow'
                                    });
                                }
                                if (localQuill) {
                                    const hidden = form.querySelector('input[name="body"]');
                                    if (hidden) hidden.value = localQuill.root.innerHTML.trim();
                                }

                                const bodyField = form.querySelector('textarea[name="body"]') ||
                                    form.querySelector('input[name="body"]');
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
                                    if (resp.success) {
                                        if (resp.html) {
                                            const wrapper = document.createElement('div');
                                            wrapper.innerHTML = resp.html;
                                            const newNode = wrapper.firstElementChild;
                                            const oldNode = document.getElementById('comment-' +
                                                resp.id);
                                            if (oldNode && newNode) oldNode.replaceWith(newNode);
                                        } else if (resp.body) {
                                            const node = document.querySelector('#comment-' + resp
                                                .id + ' .comment-body');
                                            if (node) node.innerHTML = resp.body;
                                        }
                                        modal.hide();
                                    } else if (resp.errors) {
                                        const messages = Object.values(resp.errors).flat().join(
                                            '\n');
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
                            console.error('Failed to load edit form (fallback)', err);
                            showToast('danger', 'Gagal memuat form edit');
                        }
                        return;
                    }

                    // DELETE -- show modal or perform direct delete
                    const delBtn = e.target.closest('.comment-delete');
                    if (delBtn) {
                        e.preventDefault();
                        const id = delBtn.getAttribute('data-id');
                        const url = delBtn.getAttribute('data-url');
                        const modalEl = document.getElementById('commentDeleteModal');
                        if (!modalEl) {
                            if (!confirm('Yakin ingin menghapus komentar ini?')) return;
                            performDelete(url, id);
                            return;
                        }
                        const confirmBtn = modalEl.querySelector('#commentDeleteConfirmBtn');
                        const modalBody = modalEl.querySelector('.modal-body');
                        modalBody.textContent = 'Yakin ingin menghapus komentar ini?';
                        confirmBtn.dataset.url = url;
                        confirmBtn.dataset.id = id;
                        const modal = new bootstrap.Modal(modalEl);
                        modal.show();
                        return;
                    }
                }, true); // use capture to increase chance of catching clicks
            } catch (e) {
                console.error('Failed to bind fallback edit/delete handlers', e);
            }
        })();
    </script>

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
            const threadId = '{{ $thread->id }}';

            // prefer an explicit data-url on the button (used for replies), otherwise build comment edit URL
            const btnUrl = btn.getAttribute('data-url');
            const url = btnUrl ? btnUrl : `/comunity/${threadId}/comments/${id}/edit`;

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

                // find the returned form (could be comment-edit-form with Quill or reply-edit-form with textarea)
                const form = modalBody.querySelector('form');
                if (!form) return;

                // initialize Quill only if the returned form contains a quill container
                const quillContainer = form.querySelector('[id^="edit-quill-editor-"]');
                let localQuill = null;
                if (quillContainer) {
                    const initQuill = function() {
                        if (localQuill) return;
                        try {
                            localQuill = new Quill('#' + quillContainer.id, {
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
                        } catch (err) {
                            console.error('Quill init failed (edit form)', err);
                        }
                    };

                    // initialize when modal is fully shown to avoid layout issues
                    const modalEl = document.getElementById('commentEditModal');
                    if (modalEl) {
                        const onShown = function() {
                            initQuill();
                            modalEl.removeEventListener('shown.bs.modal', onShown);
                        };
                        modalEl.addEventListener('shown.bs.modal', onShown);
                    }

                    // safety: try to init immediately as well (in case modal already visible)
                    setTimeout(initQuill, 0);
                }

                // handle submission for any returned form
                form.addEventListener('submit', function(ev) {
                    ev.preventDefault();
                    const action = form.getAttribute('action');

                    // if using Quill, copy its HTML into the hidden input
                    if (localQuill) {
                        const hidden = form.querySelector('input[name="body"]');
                        if (hidden) {
                            const html = localQuill.root.innerHTML.trim();
                            if (!html || html === '<p><br></p>') {
                                showToast('danger', 'Komentar tidak boleh kosong');
                                return;
                            }
                            hidden.value = html;
                        }
                    }

                    // validate body presence from textarea or hidden input
                    const bodyField = form.querySelector('textarea[name="body"]') || form
                        .querySelector('input[name="body"]');
                    const value = bodyField ? bodyField.value.trim() : '';
                    if (!value) {
                        showToast('danger', 'Komentar tidak boleh kosong');
                        return;
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
                            // If server returned full HTML for the updated item, replace the whole node
                            if (resp.html) {
                                const wrapper = document.createElement('div');
                                wrapper.innerHTML = resp.html;
                                const newNode = wrapper.firstElementChild;
                                const oldNode = document.getElementById('comment-' + resp
                                    .id);
                                if (oldNode && newNode) {
                                    oldNode.replaceWith(newNode);
                                }
                            } else if (resp.body) {
                                // update only the comment body
                                const node = document.querySelector('#comment-' + resp.id +
                                    ' .comment-body');
                                if (node) node.innerHTML = resp.body;
                            }
                            modal.hide();
                        } else if (resp.errors) {
                            const messages = Object.values(resp.errors).flat().join('\n');
                            showToast('danger', messages || 'Terjadi kesalahan');
                        } else if (resp && resp.message) {
                            showToast('danger', resp.message);
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

        // delegated handler for delete buttons (.comment-delete)
        document.addEventListener('click', function(e) {
            const delBtn = e.target.closest('.comment-delete');
            if (!delBtn) return;
            e.preventDefault();

            const id = delBtn.getAttribute('data-id');
            const url = delBtn.getAttribute('data-url');

            const modalEl = document.getElementById('commentDeleteModal');
            if (!modalEl) {
                // fallback to browser confirm + direct delete
                if (!confirm('Yakin ingin menghapus komentar ini?')) return;
                performDelete(url, id);
                return;
            }

            const confirmBtn = modalEl.querySelector('#commentDeleteConfirmBtn');
            const modalBody = modalEl.querySelector('.modal-body');
            modalBody.textContent = 'Yakin ingin menghapus komentar ini?';
            confirmBtn.dataset.url = url;
            confirmBtn.dataset.id = id;

            const modal = new bootstrap.Modal(modalEl);
            modal.show();
        });

        // wire delete confirmation modal confirm button (use closest so clicks on inner elements work)
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('#commentDeleteConfirmBtn');
            if (!btn) return;
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
                                    if (repliesContainer) {
                                        // if replies container is collapsed and hidden, show it
                                        const inst = bootstrap.Collapse.getOrCreateInstance(
                                            repliesContainer);
                                        inst.show();
                                        // append the new reply HTML
                                        const wrapper = document.createElement('div');
                                        wrapper.innerHTML = data.html;
                                        repliesContainer.appendChild(wrapper.firstElementChild);
                                    } else {
                                        // create the container and toggle button if missing (idempotent)
                                        const parent = document.querySelector('#comment-' + parentId +
                                            ' .d-flex.align-items-center.gap-3');
                                        // only create toggle button if one doesn't already exist
                                        const existingToggle = document.querySelector('[data-bs-target="#repliesCollapse' +
                                            parentId + '"]');
                                        if (parent && !existingToggle) {
                                            const btn = document.createElement('button');
                                            btn.className = 'btn btn-link btn-sm p-0 text-decoration-none';
                                            btn.type = 'button';
                                            btn.setAttribute('data-bs-target', '#repliesCollapse' + parentId);
                                            btn.setAttribute('data-bs-toggle', 'collapse');
                                            btn.setAttribute('aria-expanded', 'false');
                                            btn.setAttribute('aria-controls', 'repliesCollapse' + parentId);
                                            btn.innerHTML = '<i class="bi bi-chat-left-text"></i> Lihat 1 Balasan';
                                            parent.appendChild(btn);
                                        }

                                        // create the collapse container if it doesn't exist
                                        let repliesDiv = document.getElementById('repliesCollapse' + parentId);
                                        if (!repliesDiv) {
                                            const replyCollapseEl = document.getElementById('replyCollapse' + parentId);
                                            repliesDiv = document.createElement('div');
                                            repliesDiv.className = 'collapse mt-2';
                                            repliesDiv.id = 'repliesCollapse' + parentId;
                                            // append the new reply HTML into the replies container
                                            const wrapper = document.createElement('div');
                                            wrapper.innerHTML = data.html;
                                            if (wrapper.firstElementChild) repliesDiv.appendChild(wrapper
                                                .firstElementChild);

                                            if (replyCollapseEl && replyCollapseEl.parentNode) {
                                                replyCollapseEl.parentNode.insertBefore(repliesDiv, replyCollapseEl
                                                    .nextSibling);
                                            } else {
                                                const commentEl = document.getElementById('comment-' + parentId);
                                                if (commentEl) commentEl.appendChild(repliesDiv);
                                            }

                                            // show the newly created replies container
                                            const inst2 = bootstrap.Collapse.getOrCreateInstance(repliesDiv);
                                            inst2.show();
                                        }
                                    }

                                    // update the 'Lihat X Balasan' button text if count provided
                                    if (typeof data.count !== 'undefined' && data.parent_id) {
                                        const toggleBtns = document.querySelectorAll('[data-bs-target="#repliesCollapse' +
                                            data.parent_id + '"]');
                                        toggleBtns.forEach(function(toggle) {
                                            toggle.innerHTML = '<i class="bi bi-chat-left-text"></i> Lihat ' + data
                                                .count + ' Balasan';
                                        });
                                    }
                                    const url = likeBtn.getAttribute('data-url');
                                    const id = likeBtn.getAttribute('data-id');
                                    if (!url) return;

                                    fetch(url, {
                                        method: 'POST',
                                        credentials: 'same-origin',
                                        headers: {
                                            'Content-Type': 'application/json',
                                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                                .getAttribute(
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
                                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                            .getAttribute(
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
                                        showToast('success', data.bookmarked ? 'Thread disimpan.' :
                                            'Bookmark dihapus.');
                                    } else if (data && data.message) {
                                        showToast('danger', data.message);
                                    }
                                }).catch(err => {
                                    console.error('Bookmark request failed', err);
                                    showToast('danger', 'Gagal menyimpan bookmark');
                                });
                            });
    </script>

    <script>
        // Direct binding for comment delete confirm button as a final fallback.
        // Some pages may have multiple delegated handlers or runtime errors that prevent
        // other listeners from being attached. This listener uses capture on the body
        // so a click on the modal confirm button always triggers performDelete.
        (function() {
            try {
                function __onCommentDeleteConfirmClick(e) {
                    const btn = e.target.closest('#commentDeleteConfirmBtn');
                    if (!btn) return;
                    e.preventDefault();

                    const url = btn.dataset.url;
                    const id = btn.dataset.id;

                    // hide modal if present
                    const modalEl = document.getElementById('commentDeleteModal');
                    if (modalEl) {
                        const modal = bootstrap.Modal.getInstance(modalEl) || new bootstrap.Modal(modalEl);
                        try {
                            modal.hide();
                        } catch (err) {
                            /* ignore */
                        }
                    }

                    // disable button briefly to avoid double submits
                    btn.disabled = true;
                    try {
                        performDelete(url, id);
                    } finally {
                        setTimeout(function() {
                            btn.disabled = false;
                        }, 1500);
                    }
                }

                // use capture so this runs before other handlers and increases chance of catching clicks
                document.body.addEventListener('click', __onCommentDeleteConfirmClick, true);
            } catch (e) {
                console.error('Failed to attach direct comment delete confirm handler', e);
            }
        })();
    </script>
@endsection
