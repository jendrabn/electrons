@extends('layouts.app')

@section('content')
    <section class="container">
        <div class="row">
            <div class="col-lg-8">
                <div class="mb-5">
                    <h1 class="fw-bold display-6 mb-1 d-inline-flex align-items-center">
                        {{ $title ?? 'Blog' }}
                    </h1>
                    <div class="text-muted fs-6">
                        Temukan artikel, tips, dan berita terbaru di sini.
                    </div>
                </div>

                @if (request()->has('search') && trim(request('search')) !== '')
                    <p>
                        <i class="bi bi-search me-2"></i>Hasil pencarian untuk "<span
                              class="fw-semibold">{{ request('search') }}</span>"
                    </p>
                @endif

                <div>
                    @foreach ($posts as $post)
                        <div class="mb-3">
                            <x-post-item :post="$post"
                                         type="horizontal" />
                        </div>
                    @endforeach
                </div>
                <div class="mt-4">
                    {{ $posts->links() }}
                </div>
            </div>
            <div class="col-lg-4">
                @include('partials.sidebar')
            </div>
        </div>
    </section>
@endsection
