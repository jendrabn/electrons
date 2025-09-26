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
                                <form action="{{ route('posts.comments.store', $post->id) }}"
                                      class="flex-grow-1"
                                      id="comment-form"
                                      method="POST">
                                    @csrf
                                    <div class="mb-3">
                                        <textarea class="form-control"
                                                  id="commentBody"
                                                  name="body"
                                                  placeholder="Tulis komentar..."
                                                  required
                                                  rows="3"></textarea>
                                    </div>
                                    <div class="mt-2 text-end">
                                        <button class="btn btn-primary"
                                                type="submit">
                                            <i class="bi bi-send"></i> Kirim
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        @include('posts.partials._comments-list')
                    </div>
                    {{-- End Comments --}}

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

    @include('posts.partials._modals')
@endsection

@section('scripts')
    <script></script>
    <script>
        document.addEventListener('click', function(e) {
            // comment like (top-level comment)
            const commentLikeBtn = e.target.closest('.comment-like-btn, .reply-like-btn');
            if (!commentLikeBtn) return;

            e.preventDefault();

            const url = commentLikeBtn.dataset.url;
            if (!url) return;

            const icon = commentLikeBtn.querySelector('i.bi');
            const counter = commentLikeBtn.querySelector('.like-count');

            fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                        'content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({})
            }).then(async (res) => {
                if (res.status === 401 || res.status === 403) {
                    showToast('danger', 'Anda harus login untuk melakukan aksi ini.');
                    return;
                }

                const json = await res.json();
                if (json?.success) {
                    // update like UI
                    if (json.liked) {
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

                    if (counter) {
                        counter.textContent = json.count ?? counter.textContent;
                    }
                } else {
                    showToast('danger', json?.message ?? 'Terjadi kesalahan');
                }
            }).catch((err) => {
                console.error(err);
                showToast('danger', 'Terjadi kesalahan jaringan');
            });
        });
    </script>

    <script>
        // Replace copied text from .content-body with attribution per user request
        (function() {
            document.addEventListener('copy', function(e) {
                try {
                    const selection = document.getSelection();
                    if (!selection || selection.isCollapsed) return;

                    const anchor = selection.anchorNode;
                    if (!anchor) return;

                    const contentEl = anchor.nodeType === Node.ELEMENT_NODE ?
                        anchor.closest && anchor.closest('.content-body') :
                        anchor.parentElement && anchor.parentElement.closest('.content-body');
                    if (!contentEl) return;

                    const postTitle = {!! json_encode($post->title) !!};
                    const postUrl = window.location.href;
                    const siteName = {!! json_encode(config('app.name')) !!};

                    // exact plain text format requested
                    const attributionText = "\n\nArtikel ini telah tayang di " + siteName +
                        " dengan judul [\"" + postTitle + "\"], Klik untu baca: " + postUrl;

                    // build HTML attribution
                    const attributionHtml =
                        '<div style="margin-top:8px;border-top:1px solid #ddd;padding-top:8px;font-size:90%;color:#555">Artikel ini telah tayang di <strong>' +
                        escapeHtml(siteName) + '</strong> dengan judul "' + escapeHtml(postTitle) +
                        '", <a href="' + postUrl + '">Klik untu baca</a></div>';

                    // get HTML/text of selection
                    const container = document.createElement('div');
                    for (let i = 0; i < selection.rangeCount; i++) {
                        const range = selection.getRangeAt(i).cloneRange();
                        container.appendChild(range.cloneContents());
                    }

                    const selectedHtml = container.innerHTML || selection.toString();
                    const selectedText = selection.toString();

                    if (e.clipboardData) {
                        e.clipboardData.setData('text/html', selectedHtml + attributionHtml);
                        e.clipboardData.setData('text/plain', selectedText + attributionText);
                        e.preventDefault();
                    }
                } catch (err) {
                    console.error('copy attribution error', err);
                    // let default copy happen
                }
            });

            function escapeHtml(unsafe) {
                return String(unsafe)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }
        })();
    </script>

    <script>
        (function() {
            const deleteModalEl = document.getElementById('commentDeleteModal');
            const deleteModal = deleteModalEl ? new bootstrap.Modal(deleteModalEl) : null;
            const deleteConfirmBtn = document.getElementById('commentDeleteConfirmBtn');
            let pendingDelete = null;

            // Open confirmation modal when delete button clicked
            document.addEventListener('click', function(e) {
                const delBtn = e.target.closest('.comment-delete, .reply-delete');
                if (!delBtn) return;

                e.preventDefault();

                const url = delBtn.dataset.url;
                const id = delBtn.dataset.id;
                if (!url || !id) return;

                // set modal message
                if (deleteModalEl) {
                    deleteModalEl.querySelector('.modal-body').textContent =
                        'Yakin ingin menghapus komentar ini? Tindakan ini tidak bisa dibatalkan.';
                }

                // store pending delete info on confirm button dataset
                if (deleteConfirmBtn) {
                    deleteConfirmBtn.dataset.url = url;
                    deleteConfirmBtn.dataset.id = id;
                }

                pendingDelete = {
                    url,
                    id
                };

                if (deleteModal) {
                    deleteModal.show();
                }
            });

            // Confirm delete click
            if (deleteConfirmBtn) {
                deleteConfirmBtn.addEventListener('click', function(ev) {
                    ev.preventDefault();

                    const url = deleteConfirmBtn.dataset.url;
                    const id = deleteConfirmBtn.dataset.id;
                    if (!url || !id) return;

                    // disable button while request in-flight
                    deleteConfirmBtn.disabled = true;
                    deleteConfirmBtn.textContent = 'Menghapus...';

                    fetch(url, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')
                                .getAttribute('content'),
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json',
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({})
                    }).then(async (res) => {
                        if (res.status === 401 || res.status === 403) {
                            showToast('danger',
                                'Anda tidak memiliki izin untuk menghapus komentar ini.');
                            return;
                        }

                        const json = await res.json();
                        if (json?.success) {
                            // remove comment element from DOM
                            const el = document.getElementById('comment-' + json.id);
                            if (el) el.remove();

                            // if reply deletion, update parent reply count badge/button
                            if (json.parent_id) {
                                const parentToggle = document.querySelector(
                                    '[data-bs-target="#repliesCollapse' + json.parent_id + '"]');
                                if (parentToggle) {
                                    parentToggle.innerHTML =
                                        '<i class="bi bi-chat-left-text"></i> Lihat ' + (json
                                            .count ?? 0) + ' Balasan';
                                }
                            }

                            showToast('success', 'Komentar dihapus.');
                            // hide modal
                            if (deleteModal) deleteModal.hide();
                        } else {
                            showToast('danger', json?.message ?? 'Gagal menghapus komentar');
                        }
                    }).catch((err) => {
                        console.error(err);
                        showToast('danger', 'Terjadi kesalahan jaringan');
                    }).finally(() => {
                        deleteConfirmBtn.disabled = false;
                        deleteConfirmBtn.textContent = 'Hapus';
                    });
                });
            }
        })();
    </script>

    <script>
        (function() {
            // Handle reply form submissions via AJAX (delegated)
            document.addEventListener('submit', function(e) {
                const form = e.target.closest('.reply-form');
                if (!form) return;

                e.preventDefault();

                const action = form.getAttribute('action') || window.location.href;
                const textarea = form.querySelector('textarea[name="body"]');
                const parentInput = form.querySelector('input[name="parent_id"]');
                const parentId = parentInput ? parentInput.value : null;
                const submitBtn = form.querySelector('button[type="submit"]');

                if (!action || !textarea) return;

                const payload = {
                    body: textarea.value
                };
                if (parentId) payload.parent_id = parentId;

                if (submitBtn) {
                    submitBtn.disabled = true;
                    const origHtml = submitBtn.innerHTML;
                    submitBtn.textContent = 'Mengirim ...';
                }

                fetch(action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute(
                            'content'),
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify(payload)
                }).then(async (res) => {
                    if (res.status === 401 || res.status === 403) {
                        showToast('danger', 'Anda harus login untuk membalas komentar.');
                        return;
                    }

                    const json = await res.json();
                    if (json?.success) {
                        // insert the returned HTML into replies container
                        const target = document.getElementById('repliesCollapse' + (json
                            .parent_id ?? parentId));
                        if (target) {
                            target.insertAdjacentHTML('beforeend', json.html);
                        }

                        // update replies count on the toggle button
                        const parentToggle = document.querySelector(
                            '[data-bs-target="#repliesCollapse' + (json.parent_id ?? parentId) +
                            '"]');
                        if (parentToggle) {
                            parentToggle.innerHTML = '<i class="bi bi-chat-left-text"></i> Lihat ' +
                                (json.count ?? 0) + ' Balasan';
                        }

                        // clear textarea
                        textarea.value = '';
                        showToast('success', json.message ?? 'Balasan dikirim.');
                    } else {
                        showToast('danger', json?.message ?? 'Gagal mengirim balasan');
                    }
                }).catch((err) => {
                    console.error(err);
                    showToast('danger', 'Terjadi kesalahan jaringan');
                }).finally(() => {
                    if (submitBtn) {
                        submitBtn.disabled = false;
                        // restore original label (try to be safe)
                        submitBtn.innerHTML = '<i class="bi bi-send"></i> Kirim';
                    }
                });
            });
        })();
    </script>

    <script>
        (function() {
            // Edit comment / reply via AJAX
            document.addEventListener('click', async function(e) {
                const editBtn = e.target.closest('.comment-edit, .reply-edit');
                if (!editBtn) return;

                e.preventDefault();

                const url = editBtn.dataset.url;
                if (!url) return;

                // fetch the edit form HTML
                try {
                    const res = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });

                    if (res.status === 401 || res.status === 403) {
                        showToast('danger', 'Anda tidak memiliki izin untuk mengedit komentar ini.');
                        return;
                    }

                    const json = await res.json();
                    if (!json?.html) {
                        showToast('danger', 'Gagal memuat form edit');
                        return;
                    }

                    // decide which modal to use based on button class
                    let modalEl, bodyEl, modalObj;
                    if (editBtn.classList.contains('comment-edit')) {
                        modalEl = document.getElementById('commentEditModal');
                        bodyEl = document.getElementById('commentEditModalBody');
                    } else {
                        modalEl = document.getElementById('replyEditModal');
                        bodyEl = document.getElementById('replyEditModalBody');
                    }

                    if (!modalEl || !bodyEl) {
                        showToast('danger', 'Modal edit tidak tersedia');
                        return;
                    }

                    bodyEl.innerHTML = json.html;
                    modalObj = new bootstrap.Modal(modalEl);
                    modalObj.show();

                    // intercept form submit inside modal
                    const form = bodyEl.querySelector('form');
                    if (!form) return;

                    form.addEventListener('submit', async function(ev) {
                        ev.preventDefault();
                        const action = form.getAttribute('action');
                        const method = (form.querySelector('input[name="_method"]') || {})
                            .value || form.method || 'POST';

                        const formData = new FormData(form);
                        const payload = {};
                        for (const [k, v] of formData.entries()) payload[k] = v;

                        // send PUT via fetch
                        try {
                            const putRes = await fetch(action, {
                                method: 'PUT',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector(
                                        'meta[name="csrf-token"]').getAttribute(
                                        'content'),
                                    'X-Requested-With': 'XMLHttpRequest',
                                    'Accept': 'application/json',
                                    'Content-Type': 'application/json'
                                },
                                body: JSON.stringify(payload)
                            });

                            if (putRes.status === 401 || putRes.status === 403) {
                                showToast('danger',
                                    'Anda tidak memiliki izin untuk mengedit komentar ini.');
                                return;
                            }

                            const putJson = await putRes.json();
                            if (putJson?.success) {
                                // update DOM: find comment/reply body
                                const id = putJson.id;
                                const el = document.querySelector('#comment-' + id +
                                    ' .comment-body, #comment-' + id + ' .reply-body');
                                if (el) el.innerHTML = putJson.body;

                                showToast('success', 'Komentar diperbarui.');
                                modalObj.hide();
                            } else {
                                showToast('danger', putJson?.message ??
                                    'Gagal menyimpan perubahan');
                            }
                        } catch (err) {
                            console.error(err);
                            showToast('danger', 'Terjadi kesalahan jaringan');
                        }
                    }, {
                        once: true
                    });

                } catch (err) {
                    console.error(err);
                    showToast('danger', 'Gagal memuat form edit');
                }
            });
        })();
    </script>
@endsection
