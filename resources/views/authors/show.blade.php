@extends('layouts.app')

@section('styles')
    <style>
        .author-cover {
            position: relative;
            min-height: 240px;
            border-radius: .75rem;
            background:
                radial-gradient(1200px 400px at -5% 0%, rgba(255, 255, 255, .05), rgba(255, 255, 255, 0) 60%),
                radial-gradient(1000px 300px at 105% 30%, rgba(255, 255, 255, .08), rgba(255, 255, 255, 0) 60%),
                linear-gradient(180deg, #3a3f44, #2a2f34);
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* .author-cover::after {
                content: '';
                position: absolute;
                inset: 0;
                border-radius: .75rem;
                background: rgba(0, 0, 0, .35);
                pointer-events: none;
            } */
        .author-avatar {
            width: 110px;
            height: 110px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 8px 18px rgba(0, 0, 0, .25);
            background-color: #fff;
        }

        .author-name {
            font-size: clamp(1.6rem, 2.5vw, 2rem);
            line-height: 1.2;
            color: #fff;
            letter-spacing: .2px;
            margin: .75rem 0 0 0;
        }

        .author-stats {
            color: rgba(255, 255, 255, .9);
        }

        .author-stats .stat-item {
            display: inline-flex;
            align-items: baseline;
            gap: .375rem;
        }

        .author-stats .stat-item .value {
            font-weight: 700;
        }

        .author-stats .divider {
            width: 1px;
            height: 16px;
            background: rgba(255, 255, 255, .5);
            margin: 0 .25rem;
        }

        @media (min-width: 992px) {
            .author-cover {
                min-height: 280px;
            }

            .author-avatar {
                width: 128px;
                height: 128px;
            }

            .author-name {
                font-size: clamp(1.8rem, 2.5vw, 2.2rem);
            }
        }
    </style>
@endsection

@section('content')
    <div class="container">
        <div class="author-header mb-4 mb-lg-5">
            <div @if ($user->cover_url) style="background-image: url('{{ $user->cover_url }}');" @endif
                 class="author-cover d-flex align-items-center justify-content-center overflow-hidden">
                <div class="text-center p-4 p-lg-5">
                    <img alt="{{ $user->name }}"
                         class="author-avatar"
                         src="{{ $user->avatar_url }}">
                    <h1 class="author-name fw-semibold">{{ $user->name }}</h1>

                    <div class="author-stats d-flex flex-wrap align-items-center justify-content-center gap-3 mt-2">
                        <span class="stat-item">
                            <span class="value">{{ number_format((int) $articlesCount, 0, ',', '.') }}</span>
                            <span class="label text-white-50">artikel</span>
                        </span>
                        <span class="divider"></span>
                        <span class="stat-item">
                            <span class="value">{{ number_format((int) $totalViews, 0, ',', '.') }}</span>
                            <span class="label text-white-50">pembaca</span>
                        </span>
                        <span class="divider"></span>
                        <span class="stat-item">
                            <span class="value">{{ number_format((int) $contributionsCount, 0, ',', '.') }}</span>
                            <span class="label text-white-50">kontribusi</span>
                        </span>
                    </div>

                    @if (!empty($user->bio))
                        <p class="mt-3 mb-0 text-white-50"
                           style="max-width: 780px; margin-inline: auto;">
                            {{ $user->bio }}
                        </p>
                    @endif
                </div>
            </div>
        </div>

        <div class="mb-3 d-flex align-items-center justify-content-between">
            <h2 class="h5 h4-lg fw-bold mb-0">Artikel oleh {{ $user->name }}</h2>
            @if ($articlesCount > 0)
                <span class="text-muted small">{{ number_format((int) $articlesCount, 0, ',', '.') }} artikel</span>
            @endif
        </div>

        @if ($posts->count() > 0)
            <div class="row gx-0 gy-2 g-lg-4">
                @foreach ($posts as $post)
                    <div class="col-12 col-md-6 col-lg-4">
                        <x-post-item :post="$post"
                                     type="vertical" />
                    </div>
                @endforeach
            </div>

            <div class="mt-4">
                {{ $posts->links() }}
            </div>
        @else
            <div class="alert alert-light border">
                Belum ada artikel yang dipublikasikan.
            </div>
        @endif
    </div>
@endsection
