@extends('layouts.app')

@section('content')
    <section class="container mb-5">
        <h2 class="fw-bold m-0 mb-3">Artikel Terbaru</h2>

        <div class="glider-contain">
            <div class="glider">
                @foreach ($newPosts as $post)
                    <x-glider-item :post="$post" />
                @endforeach
            </div>

            <div class="glider-navigation mt-3">
                <div class="glider-navigation-dots">
                    <div class="dots"
                         id="dots"
                         role="tablist"></div>
                </div>

                <div class="glider-navigation-buttons">
                    <button aria-label="Previous"
                            class="glider-prev btn btn-light rounded-circle shadow-lg">
                        <i class="bi bi-chevron-left"></i>
                    </button>
                    <button aria-label="Next"
                            class="glider-next btn btn-light rounded-circle shadow-lg">
                        <i class="bi bi-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>

    </section>
    <section class="container mb-5">
        <div class="position-relative overflow-hidden rounded-3 bg-gradient-indigo text-white p-4 p-lg-5 community-ads">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="h4 h-lg-3 fw-bold lh-sm m-0">
                        Gabung Komunitas • Tanya, Jawab, Diskusi!
                    </h2>
                    <p class="mt-2 mb-0 text-white-50">
                        Dapatkan jawaban dari komunitas, berbagi pengalaman, dan bangun reputasi Anda.
                    </p>
                    <ul class="mt-3 mb-0 small opacity-75 list-unstyled d-flex flex-wrap gap-3">
                        <li class="d-flex align-items-center">
                            <i class="bi bi-chat-dots me-2"></i> Tanya jawab cepat
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-people me-2"></i> Diskusi dengan praktisi
                        </li>
                        <li class="d-flex align-items-center">
                            <i class="bi bi-award me-2"></i> Badge &amp; reputasi
                        </li>
                    </ul>
                </div>
                <div class="col-lg-4 d-flex justify-content-lg-end">
                    <div class="d-flex align-items-center gap-2">
                        <a aria-label="Mulai Diskusi di Komunitas"
                           class="btn btn-light text-dark fw-semibold px-4 py-2 rounded-pill shadow-sm"
                           href="{{ route('community.index') }}"
                           title="Mulai Diskusi">
                            Mulai Diskusi
                            <i class="bi bi-arrow-right-short ms-1"></i>
                        </a>
                        <a aria-label="Lihat Diskusi di Komunitas"
                           class="btn btn-outline-light fw-semibold rounded-pill px-3"
                           href="{{ route('community.index') }}"
                           title="Lihat Diskusi">
                            Lihat Diskusi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>
    <section class="container mb-5">
        @foreach ($sections as $section)
            <section class="mb-5 {{ $loop->last ? 'mb-0' : '' }}">
                <header aria-labelledby="section-{{ $section->slug }}"
                        class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="m-0 fw-bold lh-sm fs-5 fs-lg-4"
                        id="section-{{ $section->slug }}">
                        {{ $section->name }}
                    </h2>
                    <a aria-label="Lihat semua artikel dalam {{ $section->name }}"
                       class="group d-inline-flex align-items-center gap-1 fw-semibold text-primary fs-6 text-decoration-none rounded-pill px-3 py-1 transition-all duration-200 hover:bg-black/5 link-underline link-underline-opacity-0 link-underline-opacity-75-hover"
                       href="{{ route('posts.tag', $section->slug) }}"
                       title="Lihat semua artikel dalam {{ $section->name }}">
                        <span>Lihat Semua</span>
                        <i
                           class="bi bi-arrow-right-short ms-1 transition-transform duration-200 group-hover:translate-x-1"></i>
                    </a>
                </header>
                <div class="row gx-0 gy-2 gx-lg-4 gy-lg-0 post-section-list">
                    @foreach ($section->posts as $post)
                        <div class="col-md-4">
                            <article class="post-item card border-0 h-100 w-100"
                                     itemscope
                                     itemtype="https://schema.org/BlogPosting">
                                <div class="row g-0">
                                    <div class="col-3 col-lg-12">
                                        <div class="position-relative">
                                            <a aria-label="Buka artikel: {{ $post->title }}"
                                               href="{{ route('posts.show', $post->slug) }}">
                                                <figure
                                                        class="post-item-image bg-gray-200 rounded-3 overflow-hidden w-100 ratio ratio-16x9">
                                                    <picture>
                                                        <img alt="{{ $post->image_caption }}"
                                                             class="h-100 w-100 object-fit-cover"
                                                             itemprop="image"
                                                             loading="lazy"
                                                             src="{{ $post->image_url }}" />
                                                    </picture>
                                                </figure>

                                                <x-badge-category :color="$post->category->color"
                                                                  :name="$post->category->name" />
                                            </a>
                                        </div>
                                    </div>
                                    <div class="col-9 col-lg-12">
                                        <div class="card-body py-0 pe-0 px-lg-0">
                                            <a class="text-decoration-none text-dark"
                                               href="{{ route('posts.show', $post->slug) }}">
                                                <h3 class="card-title fs-6 fs-lg-5 fw-bold lh-sm mb-0 mb-lg-2 line-clamp-2"
                                                    itemprop="headline">
                                                    {{ $post->title }}
                                                </h3>
                                            </a>
                                            <div class="d-flex align-items-center gap-2 mt-2"
                                                 itemprop="author"
                                                 itemscope
                                                 itemtype="https://schema.org/Person">
                                                <img alt="{{ $post->user->name }}"
                                                     class="rounded-circle object-fit-cover"
                                                     src="{{ $post->user->avatar_url }}"
                                                     style="width: 28px; height: 28px;" />
                                                <a class="text-decoration-none text-muted fw-semibold"
                                                   href="{{ route('posts.author', $post->user->id) }}"
                                                   itemprop="name"
                                                   rel="author">
                                                    {{ str()->words($post->user->name, 2, '') }}
                                                </a>
                                                <span class="mx-1">•</span>
                                                <time class="text-muted small"
                                                      datetime="{{ $post->created_at->toIso8601String() }}"
                                                      itemprop="datePublished">
                                                    {{ $post->created_at->format('d M Y') }}
                                                </time>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </article>
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </section>
    <section class="container">
        <aside aria-label="Promosi Blog: Siapa pun boleh menulis artikel"
               class="blog-ads">
            <div class="position-relative overflow-hidden rounded-3 bg-gradient-pink text-white p-4 p-lg-5">
                <div class="row align-items-center g-4">
                    <div class="col-lg-8">
                        <h2 class="h4 h-lg-3 fw-bold lh-sm m-0">
                            Tulis Artikel • Bagikan Wawasanmu!
                        </h2>
                        <p class="mt-2 mb-0 text-white-50">
                            Siapa pun boleh menulis di blog ini—publikasikan ide, tutorial, dan pengalaman profesionalmu.
                        </p>
                        <ul class="mt-3 mb-0 small opacity-75 list-unstyled d-flex flex-wrap gap-3">
                            <li class="d-flex align-items-center">
                                <i class="bi bi-pencil-square me-2"></i> Editor ramah penulis
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="bi bi-search me-2"></i> SEO-ready & mudah ditemukan
                            </li>
                            <li class="d-flex align-items-center">
                                <i class="bi bi-award me-2"></i> Bangun portofolio & reputasi
                            </li>
                        </ul>
                    </div>
                    <div class="col-lg-4 d-flex justify-content-lg-end">
                        <div class="d-flex align-items-center gap-2">
                            <a aria-label="Daftar dan mulai menulis artikel"
                               class="btn btn-light text-dark fw-semibold px-4 py-2 rounded-pill shadow-sm"
                               href="{{ route('auth.show.register') }}"
                               title="Daftar & Mulai Menulis">
                                Tulis Artikel Sekarang
                                <i class="bi bi-arrow-right-short ms-1"></i>
                            </a>
                            <a aria-label="Lihat artikel terbaru di blog"
                               class="btn btn-outline-light fw-semibold rounded-pill px-3"
                               href="{{ route('posts.index') }}"
                               title="Lihat Artikel Terbaru">
                                Lihat Artikel
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </aside>
    </section>
@endsection

@section('scripts')
    <script>
        new Glider(document.querySelector('.glider'), {
            slidesToShow: 1,
            slidesToScroll: 1,
            draggable: true,
            gaps: 10,
            dots: '#dots',
            arrows: {
                prev: '.glider-prev',
                next: '.glider-next'
            },
            responsive: [{
                breakpoint: 768,
                settings: {
                    slidesToShow: 2.25,
                    slidesToScroll: 2
                }
            }, ]
        });
    </script>
@endsection
