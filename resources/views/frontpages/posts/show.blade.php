@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb --}}
        <div class="mb-4 mb-md-5">
            <nav aria-label="breadcrumb">
                <div class="breadcrumb-line d-flex align-items-center gap-2 text-muted overflow-hidden"
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
                <article class="content">
                    {{-- Category --}}
                    <div class="content-category mb-2 mb-lg-3">
                        @php
                            $bg = $post->category->color ?? '#6c757d';
                            $hex = ltrim($bg, '#');
                            $r = hexdec(substr($hex, 0, 2));
                            $g = hexdec(substr($hex, 2, 2));
                            $b = hexdec(substr($hex, 4, 2));
                            $brightness = ($r * 299 + $g * 587 + $b * 114) / 1000;
                            $textColor = $brightness > 160 ? '#212529' : '#ffffff';
                        @endphp
                        <a class="category-badge text-decoration-none"
                           href="{{ route('posts.category', $post->category->slug) }}"
                           style="background-color: {{ $bg }}; color: {{ $textColor }};">
                            {{ $post->category->name }}
                        </a>
                    </div>
                    {{-- End Category --}}

                    {{-- Title --}}
                    <h1 class="content-title text-break text-wrap mb-3">
                        {{ $post->title }}
                    </h1>
                    {{-- End Title --}}

                    {{-- Author + Like & Share --}}
                    <div class="post-header-actions d-flex flex-column flex-lg-row justify-content-between mb-3">
                        <div class="d-flex align-items-center mb-3 mb-lg-0">
                            <a class="text-decoration-none text-dark fw-semibold author-link d-inline-block me-3"
                               href="{{ route('authors.show', $post->user->username) }}">
                                <img alt="{{ $post->user->name }}"
                                     class="author-avatar"
                                     src="{{ $post->user->avatar_url }}" />
                            </a>
                            <div class="d-flex flex-column">
                                <a class="text-decoration-none text-dark fw-semibold author-link d-inline-flex align-items-center"
                                   href="{{ route('authors.show', $post->user->username) }}"
                                   rel="author">
                                    {{ $post->user->name }}
                                </a>
                                <time class="small text-muted"
                                      datetime="{{ $post->created_at->toIso8601String() }}">
                                    <i class="bi bi-clock"></i>
                                    {{ $post->created_at->setTimezone(config('app.timezone'))->translatedFormat('l, j F Y - H:i') }}
                                </time>
                            </div>
                        </div>
                        <div class="d-flex align-items-center gap-2 meta-info">
                            @auth
                                <button class="btn btn-light btn-sm btn-md-md shadow-sm rounded-0 d-inline-flex align-items-center justify-content-center gap-2"
                                        data-liked="{{ $post->isLikedBy(auth()->user()) ? 'true' : 'false' }}"
                                        data-post-id="{{ $post->id }}"
                                        id="like-btn"
                                        title="{{ $post->isLikedBy(auth()->user()) ? 'Batalkan Suka' : 'Suka' }}"
                                        type="button">
                                    <i class="bi {{ $post->isLikedBy(auth()->user()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                    <span id="likes-count-mobile">{{ $post->likes_count }}</span>
                                </button>
                            @else
                                <a class="btn btn-light btn-sm btn-md-md shadow-sm rounded-0 d-inline-flex align-items-center justify-content-center gap-2"
                                   href="{{ route('auth.show.login') }}"
                                   title="Suka">
                                    <i class="bi bi-heart"></i>
                                    <span id="likes-count-mobile">{{ $post->likes_count }}</span>
                                </a>
                            @endauth

                            <button class="btn btn-light btn-sm btn-md-md shadow-sm rounded-0 d-inline-flex align-items-center gap-2"
                                    title="Jumlah komentar"
                                    type="button">
                                <i class="bi bi-chat"></i>
                                {{ $post->comments_count }}
                            </button>

                            <button class="btn btn-light btn-sm btn-md-md shadow-sm rounded-0 d-inline-flex align-items-center gap-2"
                                    title="Jumlah pembaca"
                                    type="button">
                                <i class="bi bi-eye"></i>
                                {{ $post->views_count }}
                            </button>

                            <button class="btn btn-light btn-sm btn-md-md shadow-sm rounded-0 d-inline-flex align-items-center gap-2"
                                    title="Perkiraan waktu membaca"
                                    type="button">
                                <i class="bi bi-stopwatch"></i>
                                {{ $post->min_read }}
                            </button>

                            <button class="btn btn-light btn-sm btn-md-md shadow-sm rounded-0 d-inline-flex align-items-center gap-2"
                                    data-bs-target="#shareModal"
                                    data-bs-toggle="modal"
                                    title="Bagikan artikel ini ke media sosial"
                                    type="button">
                                <i class="bi bi-share"></i>
                                Share
                            </button>
                        </div>
                    </div>
                    {{-- End Author + Like & Share --}}

                    {{-- Cover Image --}}
                    <div class="content-image mb-3">
                        <figure class="w-100 m-0">
                            <div class="rounded-3 overflow-hidden bg-gray-300 ratio ratio-16x9">
                                <img alt="{{ $post->image_caption }}"
                                     class="h-100 w-100 object-fit-cover"
                                     loading="lazy"
                                     src="{{ $post->image_url }}" />
                            </div>
                            <figcaption class="text-muted text-center mt-2">
                                {{ $post->image_caption }}
                            </figcaption>
                        </figure>
                    </div>
                    {{-- End Cover Image --}}

                    {{-- Font Size Control --}}
                    <div class="font-size-control my-3 my-lg-4">
                        <div class="control-box">
                            <div class="text-center small fw-semibold mb-2">Ukuran Font</div>
                            <div class="d-flex align-items-center gap-3 labels">
                                <span class="small">Kecil</span>
                                <input class="form-range flex-grow-1"
                                       id="fontSizeRange"
                                       max="22"
                                       min="14"
                                       step="1"
                                       type="range"
                                       value="16">
                                <span class="small">Besar</span>
                            </div>
                        </div>
                    </div>
                    {{-- End Font Size Control --}}

                    {{-- Content --}}
                    <div class="content-body text-break text-wrap mb-5">
                        {!! $post->content !!}
                    </div>
                    {{-- End Content --}}

                    {{-- Tags --}}
                    <div class="content-tags mb-5 d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-semibold me-1">Tag:</span>
                        @foreach ($post->tags as $tag)
                            <x-post.badge-tag :tag="$tag" />
                        @endforeach
                    </div>
                    {{-- End Tags --}}

                    {{-- Comments --}}
                    <div class="comments">
                        <h3 class="fw-bold mb-4">Komentar</h3>

                        {{-- Form Komentar --}}
                        <div class="mb-4">
                            <div class="d-flex align-items-start">
                                @if (auth()->check())
                                    <img alt="{{ auth()->user()->name }}"
                                         class="rounded-circle me-3"
                                         height="40"
                                         src="{{ auth()->user()->avatar_url ?? 'https://ui-avatars.com/api/?name=' . urlencode(auth()->user()->name) }}"
                                         style="
                                    object-fit: cover;
                                "
                                         width="40" />
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

                        @include('frontpages.posts.partials._comments-list')
                    </div>
                    {{-- End Comments --}}

                </article>
            </div>
            <div class="col-lg-4">
                @include('frontpages.posts.partials._sidebar')
            </div>
        </div>

        @if ($relatedPosts->count() > 0)
            <div class="mt-5">
                <h2 class="fw-bold mb-3 m-0">Artikel Terkait</h2>

                <div class="row gx-0 gy-2 g-lg-4">
                    @foreach ($relatedPosts as $post)
                        <div class="col-12 col-md-6 col-lg-4">
                            <x-post.article :post="$post"
                                            role="listitem"
                                            variant="vertical" />
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    @include('frontpages.posts.partials._modals')
@endsection

@section('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const range = document.getElementById('fontSizeRange');
            const body = document.querySelector('.content-body');
            if (!range || !body) return;

            const KEY = 'post-content-font-size';

            // Update CSS var used by the track gradient to fill from left up to the thumb
            function updateProgress() {
                const min = Number(range.min) || 0;
                const max = Number(range.max) || 100;
                const val = Number(range.value);
                const pct = max > min ? ((val - min) * 100) / (max - min) : 0;
                range.style.setProperty('--progress', pct + '%');
            }

            const saved = localStorage.getItem(KEY);
            if (saved) {
                body.style.setProperty('--content-font-size', saved + 'px');
                range.value = saved;
            }

            // initialize track fill on load
            updateProgress();

            range.addEventListener('input', function() {
                body.style.setProperty('--content-font-size', this.value + 'px');
                localStorage.setItem(KEY, this.value);
                updateProgress();
            });

            range.addEventListener('change', updateProgress);
        });

        // Like functionality
        document.addEventListener('click', function(e) {
            const likeBtn = e.target.closest('#like-btn, #like-btn-mobile');
            if (!likeBtn) return;

            e.preventDefault();

            const postId = likeBtn.dataset.postId;
            const isLiked = likeBtn.dataset.liked === 'true';

            // Disable button during request
            likeBtn.disabled = true;

            fetch(`/posts/${postId}/like`, {
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
                if (res.status === 401) {
                    window.location.href = '/auth/login';
                    return;
                }

                const json = await res.json();
                if (res.ok) {
                    // Update both desktop and mobile buttons
                    const desktopBtn = document.getElementById('like-btn');
                    const mobileBtn = document.getElementById('like-btn-mobile');

                    [desktopBtn, mobileBtn].forEach(btn => {
                        if (!btn) return;

                        const icon = btn.querySelector('i');
                        const text = btn.querySelector('.like-text');

                        if (json.liked) {
                            icon.classList.remove('bi-heart');
                            icon.classList.add('bi-heart-fill');
                            if (text) text.textContent = 'Disukai';
                            btn.dataset.liked = 'true';
                        } else {
                            icon.classList.remove('bi-heart-fill');
                            icon.classList.add('bi-heart');
                            if (text) text.textContent = 'Suka';
                            btn.dataset.liked = 'false';
                        }
                    });

                    // Update counts
                    const desktopCount = document.getElementById('likes-count');
                    const mobileCount = document.getElementById('likes-count-mobile');

                    [desktopCount, mobileCount].forEach(count => {
                        if (count) count.textContent = json.likes_count;
                    });

                } else {
                    if (typeof showToast === 'function') {
                        showToast('danger', json.error || 'Terjadi kesalahan');
                    }
                }
            }).catch((err) => {
                console.error(err);
                if (typeof showToast === 'function') {
                    showToast('danger', 'Terjadi kesalahan jaringan');
                }
            }).finally(() => {
                likeBtn.disabled = false;
            });
        });
    </script>
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

    <script>
        document.addEventListener('click', function(e) {
            const btn = e.target.closest('#shareCopyLinkBtn');
            if (!btn) return;

            const url = window.location.href;
            navigator.clipboard?.writeText(url).then(function() {
                // optional: show a toast if application provides showToast
                if (typeof showToast === 'function') showToast('success', 'Link disalin ke clipboard');
            }).catch(function(err) {
                console.error('copy failed', err);
                if (typeof showToast === 'function') showToast('danger', 'Gagal menyalin link');
            });
        });
    </script>
@endsection
