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
        <div class="position-relative overflow-hidden rounded-3 bg-gradient-indigo text-white p-3 p-lg-4 community-ads">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="h4 h-lg-3 fw-bold lh-sm m-0">
                        Gabung Komunitas • Tanya, Jawab, Diskusi!
                    </h2>
                    <p class="mt-2 mb-0 text-white-50">
                        Dapatkan jawaban dari komunitas, berbagi pengalaman, dan bangun reputasi Anda.
                    </p>
                </div>
                <div class="col-lg-4 d-flex justify-content-lg-end">
                    <div class="d-flex align-items-center gap-2">
                        <a aria-label="Mulai Diskusi di Komunitas"
                           class="btn btn-light text-dark fw-semibold px-4 py-2 rounded-pill shadow-sm"
                           href="{{ route('community.create') }}"
                           title="Mulai Diskusi">
                            Mulai Diskusi
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="container mb-5">
        @foreach ($sections as $section)
            <section class="mb-5 {{ $loop->last ? 'mb-0' : '' }}">
                <div class="d-flex justify-content-between align-items-center mb-4">
                    <h2 class="m-0 fw-bold lh-sm fs-5 fs-lg-3"
                        id="section-{{ $section->slug }}">
                        {{ $section->name }}
                    </h2>
                    <a aria-label="Lihat semua artikel dalam {{ $section->name }}"
                       class="group d-inline-flex align-items-center gap-1 fw-semibold text-primary fs-6 text-decoration-none rounded-pill px-3 py-1 transition-all duration-200 hover:bg-black/5 link-underline link-underline-opacity-0 link-underline-opacity-75-hover"
                       href="{{ route('posts.tag', $section->slug) }}"
                       title="Lihat semua artikel dalam {{ $section->name }}">
                        Lihat Semua
                    </a>
                </div>
                <div class="row gx-0 gy-2 gx-lg-4 gy-lg-0 post-section-list">
                    @foreach ($section->posts as $post)
                        <div class="col-md-4">
                            <x-post-item :post="$post"
                                         type="vertical" />
                        </div>
                    @endforeach
                </div>
            </section>
        @endforeach
    </section>

    <section class="container">
        <div class="position-relative overflow-hidden rounded-3 bg-gradient-pink text-white p-3 p-lg-4 blog-ads">
            <div class="row align-items-center g-4">
                <div class="col-lg-8">
                    <h2 class="h4 h-lg-3 fw-bold lh-sm m-0">
                        Tulis Artikel • Bagikan Wawasanmu!
                    </h2>
                    <p class="mt-2 mb-0 text-white-50">
                        Siapa pun boleh menulis di blog ini—publikasikan ide, tutorial, dan pengalaman profesionalmu.
                    </p>
                </div>
                <div class="col-lg-4 d-flex justify-content-lg-end">
                    <div class="d-flex align-items-center gap-2">
                        @php
                            $createPostUrl = '';

                            if (auth()->check() && auth()->user()->isAdmin()) {
                                $createPostUrl = App\Filament\Shared\Resources\Posts\PostResource::getUrl(
                                    'create',
                                    panel: 'admin',
                                );
                            } elseif (auth()->check() && auth()->user()->isAuthor()) {
                                $createPostUrl = App\Filament\Shared\Resources\Posts\PostResource::getUrl(
                                    'create',
                                    panel: 'author',
                                );
                            } else {
                                $createPostUrl = route('auth.login');
                            }
                        @endphp
                        <a aria-label="Daftar dan mulai menulis artikel"
                           class="btn btn-light text-dark fw-semibold px-4 py-2 rounded-pill shadow-sm"
                           href="{{ $createPostUrl }}"
                           title="Daftar & Mulai Menulis">
                            Tulis Artikel Sekarang
                        </a>
                    </div>
                </div>
            </div>
        </div>
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
