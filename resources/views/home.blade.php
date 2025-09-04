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
        @foreach ($sections as $section)
            <section class="mb-5 {{ $loop->last ? 'mb-0' : '' }}">
                <div class="mb-3 d-flex justify-content-between align-items-center">
                    <h2 class="fw-bold m-0">{{ $section->name }}</h2>
                    <a class="fw-semibold text-decoration-none text-primary fs-6"
                       href="{{ route('posts.tag', $section->slug) }}">Lihat Semua</a>
                </div>
                <div class="row gx-0 gy-2 gx-lg-4 gy-lg-0">
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
@endsection
