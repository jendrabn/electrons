@extends('layouts.app')

@section('styles')
    <style>
        /* Category badge - uses dynamic category color */
        .content .category-badge {
            display: inline-block;
            padding: .375rem .625rem;
            border-radius: .375rem;
            color: #fff;
            font-weight: 600;
            text-decoration: none;
        }

        /* Title typography */
        .content-title {
            font-size: clamp(1.85rem, 2.5vw, 2.6rem);
            line-height: 1.25;
            letter-spacing: .2px;
            color: #212529;
        }

        /* Author avatar */
        .author-link .author-avatar {
            width: 45px;
            height: 45px;
            border-radius: 50%;
            object-fit: cover;
        }

        /* Meta items */
        .content-meta .meta-item {
            display: inline-flex;
            align-items: center;
            gap: .375rem;
        }

        /* Hero image */
        .content-image figure {
            margin: 0;
        }

        .content-image img {
            object-fit: cover;
        }

        /* Content wrapping and responsive media */
        .content-body {
            --content-font-size: 1rem;
            font-size: var(--content-font-size);
            line-height: 1.8;
            overflow-wrap: anywhere;
            word-break: break-word;
            max-width: 100%;
        }

        .content-body img,
        .content-body table {
            max-width: 100%;
            height: auto;
        }

        .content-body pre,
        .content-body code {
            white-space: pre-wrap;
            word-break: break-word;
        }

        /* Font size slider (styled like screenshot) */
        .font-size-control .control-box {
            max-width: 560px;
            margin: 0 auto;
            background: #f8f9fa;
            border-radius: 1rem;
            padding: .75rem 1rem;
            border: 1px solid #eee;
        }

        .font-size-control input[type="range"] {
            width: 100%;
        }

        .font-size-control .labels {
            color: #6c757d;
        }

        /* Track & thumb colors */
        .font-size-control .form-range::-webkit-slider-runnable-track {
            height: 6px;
            border-radius: 999px;
            background: #f3d2d5;
        }

        .font-size-control .form-range::-webkit-slider-thumb {
            height: 18px;
            width: 18px;
            margin-top: -6px;
            background: #dc3545;
            border: none;
            border-radius: 50%;
            box-shadow: 0 0 0 4px rgba(220, 53, 69, .15);
        }

        .font-size-control .form-range::-moz-range-track {
            height: 6px;
            border-radius: 999px;
            background: #f3d2d5;
        }

        .font-size-control .form-range::-moz-range-thumb {
            height: 18px;
            width: 18px;
            background: #dc3545;
            border: none;
            border-radius: 50%;
        }

        @media (min-width: 992px) {
            .font-size-control .control-box {
                max-width: 640px;
            }

            .content-title {
                letter-spacing: .1px;
            }
        }
    </style>
@endsection

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
                <article class="content"
                         itemscope
                         itemtype="https://schema.org/Article">
                    <meta content="{{ url()->current() }}"
                          itemprop="mainEntityOfPage">
                    {{-- Category (gunakan warna kategori) --}}
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

                    {{-- Title (SEO-friendly H1) --}}
                    <h1 class="content-title mb-3"
                        itemprop="headline">
                        {{ $post->title }}
                    </h1>
                    {{-- End Title --}}

                    {{-- Author + Like & Share (dua kolom space-between) --}}
                    <div class="post-header-actions d-flex align-items-center justify-content-between mb-3">
                        <div class="d-flex align-items-center"
                             itemprop="author"
                             itemscope
                             itemtype="https://schema.org/Person">
                            <a class="d-flex align-items-center text-decoration-none text-dark fw-semibold author-link"
                               href="{{ route('authors.show', $post->user->username) }}"
                               rel="author">
                                <img alt="{{ $post->user->name }}"
                                     class="author-avatar me-2"
                                     itemprop="image"
                                     src="{{ $post->user->avatar_url }}" />
                                <span itemprop="name">{{ $post->user->name }}</span>
                            </a>
                        </div>
                        <div class="d-flex gap-2">
                            @auth
                                <button class="btn btn-outline-danger btn-sm shadow-sm rounded-pill"
                                        data-liked="{{ $post->isLikedBy(auth()->user()) ? 'true' : 'false' }}"
                                        data-post-id="{{ $post->id }}"
                                        id="like-btn"
                                        type="button">
                                    <i class="bi {{ $post->isLikedBy(auth()->user()) ? 'bi-heart-fill' : 'bi-heart' }}"></i>
                                    <span class="like-text">{{ $post->isLikedBy(auth()->user()) ? 'Disukai' : 'Suka' }}</span>
                                </button>
                            @else
                                <a class="btn btn-outline-danger btn-sm shadow-sm rounded-pill"
                                   href="{{ route('auth.show.login') }}">
                                    <i class="bi bi-heart"></i> Suka
                                </a>
                            @endauth

                            <button aria-label="Share"
                                    class="btn btn-light btn-sm shadow-sm rounded-circle btn-share"
                                    data-bs-target="#shareModal"
                                    data-bs-toggle="modal"
                                    type="button">
                                <i class="bi bi-share-fill fs-6"></i>
                            </button>
                        </div>
                    </div>

                    {{-- Meta (semua kiri: created_at + like + comment + views + min read) --}}
                    <div class="content-meta d-flex align-items-center flex-wrap gap-3 small text-muted mb-3">
                        <time class="meta-item"
                              datetime="{{ $post->created_at->toIso8601String() }}"
                              itemprop="datePublished">
                            <i class="bi bi-clock"></i>
                            {{ $post->created_at->setTimezone(config('app.timezone'))->translatedFormat('l, j F Y - H:i') }}
                        </time>
                        <span aria-label="{{ number_format($post->likes_count ?? 0) }} suka"
                              class="meta-item">
                            <i class="bi bi-heart"></i>
                            <span id="likes-count">{{ $post->likes_count }}</span>
                            <span class="visually-hidden"
                                  itemprop="interactionStatistic"
                                  itemscope
                                  itemtype="https://schema.org/InteractionCounter">
                                <meta content="https://schema.org/LikeAction"
                                      itemprop="interactionType" />
                                <meta content="{{ (int) ($post->likes_count ?? 0) }}"
                                      itemprop="userInteractionCount" />
                            </span>
                        </span>
                        <span aria-label="{{ number_format($post->comments_count ?? 0) }} komentar"
                              class="meta-item">
                            <i class="bi bi-chat"></i>
                            {{ $post->comments_count }}
                            <span class="visually-hidden"
                                  itemprop="interactionStatistic"
                                  itemscope
                                  itemtype="https://schema.org/InteractionCounter">
                                <meta content="https://schema.org/CommentAction"
                                      itemprop="interactionType" />
                                <meta content="{{ (int) ($post->comments_count ?? 0) }}"
                                      itemprop="userInteractionCount" />
                            </span>
                        </span>
                        <span aria-label="{{ number_format($post->views_count ?? 0) }} views"
                              class="meta-item">
                            <i class="bi bi-eye"></i>
                            {{ $post->views_count }}
                            <span class="visually-hidden"
                                  itemprop="interactionStatistic"
                                  itemscope
                                  itemtype="https://schema.org/InteractionCounter">
                                <meta content="https://schema.org/ViewAction"
                                      itemprop="interactionType" />
                                <meta content="{{ (int) ($post->views_count ?? 0) }}"
                                      itemprop="userInteractionCount" />
                            </span>
                        </span>
                        <span class="meta-item">
                            <i class="bi bi-stopwatch"></i>
                            {{ $post->min_read }} min read
                        </span>
                    </div>

                    {{-- Hero image (16:9 responsif dengan caption) --}}
                    <div class="content-image mb-3">
                        <figure class="w-100 m-0">
                            <div class="rounded-3 overflow-hidden bg-light ratio ratio-16x9">
                                <picture>
                                    <img alt="{{ $post->image_caption }}"
                                         class="h-100 w-100"
                                         decoding="async"
                                         loading="lazy"
                                         sizes="(min-width: 992px) 66vw, 100vw"
                                         src="{{ $post->image_url }}" />
                                </picture>
                            </div>
                            <figcaption class="text-muted text-center mt-2 fst-italic small">
                                {{ $post->image_caption }}
                            </figcaption>
                        </figure>
                    </div>
                    {{-- End Hero image --}}

                    {{-- Pengaturan ukuran font konten (range slider) --}}
                    <div aria-label="Pengaturan ukuran font"
                         class="font-size-control mb-3">
                        <div class="control-box">
                            <div class="text-center small fw-semibold mb-2">Ukuran Font</div>
                            <div class="d-flex align-items-center gap-3 labels">
                                <span class="small">Kecil</span>
                                <input aria-label="Ukuran font konten"
                                       class="form-range flex-grow-1"
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
                    <script>
                        // Inline: kontrol ukuran font konten
                        document.addEventListener('DOMContentLoaded', function() {
                            const range = document.getElementById('fontSizeRange');
                            const body = document.querySelector('.content-body');
                            if (!range || !body) return;
                            const KEY = 'post-content-font-size';
                            const saved = localStorage.getItem(KEY);
                            if (saved) {
                                body.style.setProperty('--content-font-size', saved + 'px');
                                range.value = saved;
                            }
                            range.addEventListener('input', function() {
                                body.style.setProperty('--content-font-size', this.value + 'px');
                                localStorage.setItem(KEY, this.value);
                            });
                        });
                    </script>

                    {{-- Content (dibatasi agar tidak overflow horizontal) --}}
                    <div class="content-body text-wrap mb-3">
                        {!! $post->content !!}
                    </div>
                    {{-- End Content --}}

                    {{-- Tags (reusable badges styled like sidebar) --}}
                    <div class="content-tags mb-3 d-flex flex-wrap align-items-center gap-2">
                        <span class="fw-semibold me-1">Tag:</span>
                        @foreach ($post->tags as $tag)
                            <x-post.badge-tag :tag="$tag" />
                            {{-- @include('frontpages.posts.partials._tag_link', ['tag' => $tag]) --}}
                        @endforeach
                    </div>
                    {{-- End Tags --}}

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
