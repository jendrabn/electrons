@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="breadcrumb mb-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none"
                           href="{{ route('home') }}">Home</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none"
                           href="{{ route('posts.index') }}">Blog</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none"
                           href="{{ route('posts.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                    </li>
                </ol>
            </nav>
        </div>

        <div class="row">
            <div class="col-lg-8">
                <div class="content">
                    <div class="content-category mb-3">
                        <a class="badge text-bg-primary text-decoration-none rounded-0"
                           href="{{ route('posts.category', $post->category->slug) }}">
                            {{ $post->category->name }}
                        </a>
                    </div>

                    <h1 class="content-title mb-3">
                        {{ $post->title }}
                    </h1>

                    <div class="content-meta mb-3 d-flex align-items-center">
                        <a class="text-decoration-none text-dark fw-semibold d-flex align-items-center"
                           href="{{ route('posts.author', $post->user->id) }}">
                            <span class="me-2 d-inline-block rounded-circle overflow-hidden"
                                  style="width: 36px; height: 36px">
                                <img alt="{{ $post->user->name }}"
                                     class="w-100 h-100 object-fit-cover"
                                     src="{{ $post->user->avatar }}">
                            </span>
                            <span> {{ $post->user->name }}</span>
                        </a>
                        <span class="mx-2">•</span>
                        <span>{{ $post->created_at->format('d M Y H:i') }}</span>
                        <span class="mx-2">•</span>
                        <span>{{ $post->min_read }} min read</span>
                        <span class="mx-2">•</span>
                        <span>{{ $post->views_count }} views</span>
                        <span class="mx-2">•</span>
                        <button class="btn btn-light btn-sm shadow-sm rounded-circle"
                                id="btn-share">
                            <i class="bi bi-share-fill fs-6"></i>
                        </button>
                    </div>

                    <div class="content-image mb-3">
                        <figure class="w-100">
                            <div class="rounded-3 overflow-hidden bg-gray-200 ratio ratio-16x9">
                                <picture>
                                    <img alt="{{ $post->image_caption }}"
                                         class="h-100 w-100 object-fit-fill"
                                         src="{{ $post->image_url }}" />
                                </picture>
                            </div>
                            <figcaption class="text-muted text-center fst-italic mt-2">{{ $post->image_caption }}
                            </figcaption>
                        </figure>
                    </div>

                    <div class="content-body text-wrap mb-3">
                        {!! $post->content !!}
                    </div>

                    <div class="content-tags">
                        @foreach ($post->tags as $tag)
                            <a class="badge text-bg-secondary fw-normal text-decoration-none rounded-0"
                               href="{{ route('posts.tag', $tag->slug) }}">{{ $tag->name }}</a>
                        @endforeach
                    </div>
                </div>
            </div>
            <div class="col-lg-4">
                @include('partials.sidebar')
            </div>
        </div>

        <div class="mt-5">
            <h2 class="fw-bold mb-3 m-0">Artikel Terkait</h2>

            <div class="row">
                @foreach ($relatedPosts as $post)
                    <div class="col-md-4">
                        <x-post-item :post="$post" />
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Modal Share -->
    <div aria-hidden="true"
         aria-labelledby="shareModalLabel"
         class="modal fade"
         id="shareModal"
         tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0">
                <div class="modal-header d-flex align-items-start border-bottom-0 p-4">
                    <h5 class="modal-title text-wrap"
                        id="shareModalLabel">{{ $post->title }}</h5>
                    <button aria-label="Tutup"
                            class="btn-close"
                            data-bs-dismiss="modal"
                            type="button"></button>
                </div>

                <div class="modal-body p-4">
                    <p class="mb-4 text-center">Bagikan artikel ini melalui:</p>
                    <div class="d-flex flex-wrap gap-3 justify-content-center">
                        <div class="text-center">
                            <a class="btn btn-share-social text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                               href="https://wa.me/?text={{ urlencode($post->title . ' ' . request()->fullUrl()) }}"
                               style="background: #25D366; width:36px; height:36px;"
                               target="_blank">
                                <i class="bi bi-whatsapp fs-5"></i>
                            </a>
                            <div class="small text-muted">WhatsApp</div>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-share-social text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                               href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(request()->fullUrl()) }}"
                               style="background: #1877F3; width:36px; height:36px;"
                               target="_blank">
                                <i class="bi bi-facebook fs-5"></i>
                            </a>
                            <div class="small text-muted">Facebook</div>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-share-social text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                               href="https://x.com/intent/tweet?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
                               style="background: #000; width:36px; height:36px;"
                               target="_blank">
                                <i class="bi bi-twitter-x fs-5"></i>
                            </a>
                            <div class="small text-muted">X</div>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-share-social text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                               href="https://t.me/share/url?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
                               style="background: #229ED9; width:36px; height:36px;"
                               target="_blank">
                                <i class="bi bi-telegram fs-5"></i>
                            </a>
                            <div class="small text-muted">Telegram</div>
                        </div>
                        <div class="text-center">
                            <a class="btn btn-share-social text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                               href="https://www.threads.net/intent/post?url={{ urlencode(request()->fullUrl()) }}&text={{ urlencode($post->title) }}"
                               style="background: #000; width:36px; height:36px;"
                               target="_blank">
                                <i class="bi bi-threads fs-5"></i>
                            </a>
                            <div class="small text-muted">Threads</div>
                        </div>
                        <div class="text-center">
                            <button class="btn btn-share-social text-white shadow-sm rounded-circle d-flex align-items-center justify-content-center mx-auto mb-1"
                                    id="btn-copy-link"
                                    style="background: #6c757d; width:36px; height:36px;"
                                    type="button">
                                <i class="bi bi-link-45deg fs-5"></i>
                            </button>
                            <div class="small text-muted">Copy Link</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Share Modal
            const shareBtn = document.getElementById('btn-share');
            const shareModal = new bootstrap.Modal(document.getElementById('shareModal'));
            if (shareBtn) {
                shareBtn.addEventListener('click', function() {
                    shareModal.show();
                });
            }

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
        });
    </script>
@endpush
