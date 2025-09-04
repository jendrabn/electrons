@extends('layouts.app')

@section('content')
    <div class="container">
        {{-- Breadcrumb --}}
        <div class="breadcrumb mb-3 mb-lg-5">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none small"
                           href="{{ route('home') }}">
                            <i class="bi bi-house-fill"></i>
                        </a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none small"
                           href="{{ route('posts.index') }}">Blog</a>
                    </li>
                    <li class="breadcrumb-item">
                        <a class="text-decoration-none small"
                           href="{{ route('posts.category', $post->category->slug) }}">{{ $post->category->name }}</a>
                    </li>
                    <li aria-current="page"
                        class="breadcrumb-item active text-truncate d-none d-lg-block">
                        {{ $post->title }}
                    </li>
                </ol>
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
                                <span>
                                    <i class="bi bi-stopwatch me-1"></i> {{ $post->created_at->format('l, d F Y H:i') }}
                                </span>
                                <span class="mx-1">•</span>
                                <span> <i class="bi bi-stopwatch me-1"></i> {{ $post->min_read }} min read</span>
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
        });
    </script>
@endpush
