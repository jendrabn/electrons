<nav class="navbar navbar-expand-lg bg-light navbar-light sticky-top navbar-elevated shadow-sm py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center me-4 me-lg-5 py-0"
           href="{{ route('home') }}">
            <img alt="Logo"
                 class="brand-logo"
                 src="{{ asset('images/logo.png') }}">
        </a>
        <button aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation"
                class="navbar-toggler border-0"
                data-bs-target="#navbarSupportedContent"
                data-bs-toggle="collapse"
                type="button">
            <img alt="Toggle icon"
                 src="{{ asset('images/navbar-toggler-icon.svg') }}">
        </button>

        <div class="collapse navbar-collapse"
             id="navbarSupportedContent">
            <ul class="navbar-nav me-auto mb-2 mb-lg-0 gap-2 gap-lg-3">
                <li class="nav-item">
                    <a aria-current="page"
                       class="nav-link nav-link-animated {{ request()->is('home') ? 'active' : '' }}"
                       href="{{ route('home') }}">Home</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-animated {{ request()->is('posts') ? 'active' : '' }}"
                       href="{{ route('posts.index') }}">Blog</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link nav-link-animated"
                       href="{{ route('community.index') }}">Komunitas</a>
                </li>
                <li class="nav-item dropdown-center">
                    <a aria-expanded="false"
                       class="nav-link nav-link-animated dropdown-toggle-custom d-flex align-items-center gap-1"
                       data-bs-toggle="dropdown"
                       href="#"
                       role="button">
                        Kategori <i class="bi bi-chevron-down"
                           style="font-size: 14px;"></i>
                    </a>
                    <ul class="dropdown-menu border-primary shadow-sm rounded-3">
                        @foreach ($categories as $category)
                            <li>
                                <a class="dropdown-item"
                                   href="{{ route('posts.category', $category->slug) }}">{{ $category->name }}</a>
                            </li>
                        @endforeach
                    </ul>
                </li>
            </ul>

            <form action="{{ route('posts.index') }}"
                  class="d-flex mb-3 mb-lg-0 search-form order-3 order-lg-2 mx-lg-4"
                  method="GET"
                  role="search">
                <div class="input-group">
                    <input aria-label="Search"
                           class="form-control search-input"
                           name="search"
                           placeholder="Apa yang kamu cari?"
                           type="search"
                           value="{{ request('search') }}">
                    <button class="btn btn-outline-secondary search-btn"
                            type="submit">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 order-2 order-lg-3">
                @if (auth()->check())
                    <li class="nav-item dropdown">
                        <button aria-expanded="false"
                                class="nav-link dropdown-toggle-custom d-flex align-items-center"
                                data-bs-toggle="dropdown">
                            <div class="avatar d-flex align-items-center">
                                <img alt="{{ auth()->user()->name }}"
                                     class="rounded-circle"
                                     src="{{ auth()->user()->avatar_url }}"
                                     style="width: 35px; height: 35px;" />
                                <span class="ms-2 fw-semibold">{{ str()->words(auth()->user()->name, 2, '') }}</span>
                            </div>
                        </button>
                        <ul class="dropdown-menu border-primary shadow-sm rounded-3">
                            <li>
                                <a class="dropdown-item"
                                   href="{{ auth()->user()->hasRole('admin') ? route('filament.admin.pages.dashboard') : route('filament.author.pages.dashboard') }}">
                                    <i class="bi bi-grid me-2"></i> Dashboard
                                </a>
                            </li>
                            <li>
                                @php
                                    $createPostUrl = '';

                                    if (auth()->user()->isAdmin()) {
                                        $createPostUrl = App\Filament\Shared\Resources\Posts\PostResource::getUrl(
                                            'create',
                                            panel: 'admin',
                                        );
                                    } elseif (auth()->user()->isAuthor()) {
                                        $createPostUrl = App\Filament\Shared\Resources\Posts\PostResource::getUrl(
                                            'create',
                                            panel: 'author',
                                        );
                                    }
                                @endphp

                                <a class="dropdown-item"
                                   href="{{ $createPostUrl }}">
                                    <i class="bi bi-pencil me-2"></i> Buat Tulisan
                                </a>
                            </li>
                            <li>
                                <form action="{{ route('auth.logout') }}"
                                      method="POST">
                                    @csrf
                                    <button class="dropdown-item"
                                            type="submit">
                                        <i class="bi bi-box-arrow-right me-2"></i> Logout
                                    </button>
                                </form>
                            </li>
                        </ul>
                    </li>
                @else
                    <li class="nav-item">
                        <a class="btn btn-outline-primary rounded-2 fw-semibold"
                           href="{{ route('auth.show.login') }}">Masuk/Daftar</a>
                    </li>
                @endif
            </ul>
        </div>
    </div>

    <style>
        .navbar-elevated .brand-logo {
            height: 40px;
            width: auto;
        }

        .navbar-elevated .nav-link,
        .navbar-elevated .dropdown-toggle-custom {
            position: relative;
            font-weight: 600;
            font-size: 0.975rem;
            color: #334155;
        }

        .navbar-elevated .nav-link:hover,
        .navbar-elevated .dropdown-toggle-custom:hover,
        .navbar-elevated .nav-link:focus {
            color: #0d6efd;
        }

        /* Underline hover animation - center-out using background-size (constant height) */
        .navbar-elevated .nav-link-animated {
            background-image: linear-gradient(#0d6efd, #0d6efd);
            background-position: 50% 100%;
            background-repeat: no-repeat;
            background-size: 0% 2px;
            transition: background-size 260ms ease;
        }

        .navbar-elevated .nav-link-animated:hover,
        .navbar-elevated .nav-link-animated.active {
            background-size: 100% 2px;
        }

        /* Improve dropdown look */
        .navbar-elevated .dropdown-menu {
            padding: .5rem;
        }

        .navbar-elevated .dropdown-item {
            border-radius: .5rem;
            font-weight: 500;
            color: #334155;
        }

        .navbar-elevated .dropdown-item:hover {
            background-color: #f1f5f9;
            color: #0d6efd;
        }

        /* Search form sizing: full width on mobile, wider on desktop */
        .navbar-elevated .search-form {
            width: 100%;
        }

        @media (min-width: 768px) {
            .navbar-elevated .search-form {
                width: 360px;
            }
        }

        @media (min-width: 992px) {
            .navbar-elevated .search-form {
                width: 440px;
            }
        }

        @media (min-width: 1200px) {
            .navbar-elevated .search-form {
                width: 520px;
            }
        }

        .navbar-elevated .search-input {
            font-size: 0.95rem;
            border: 1px solid #e2e8f0;
        }

        .navbar-elevated .search-input:focus {
            border-color: #b6d4fe;
            box-shadow: 0 0 0 .2rem rgba(13, 110, 253, 0.15);
        }

        .navbar-elevated .search-btn {
            border-color: #e2e8f0;
        }

        .navbar-elevated .search-btn:hover {
            color: #0d6efd;
            border-color: #b6d4fe;
            background-color: #fff;
        }
    </style>
</nav>
