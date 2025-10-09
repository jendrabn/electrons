<nav class="navbar navbar-expand-lg bg-body-tertiary sticky-top shadow-sm py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center me-4 me-lg-5 py-0"
           href="{{ route('home') }}">
            <span class="brand-logo">
                <span aria-hidden="true"
                      class="brand-logo-symbol">
                    <span class="brand-logo-letter">E</span>
                </span>
                <span class="brand-logo-text">ELECTRONS</span>
            </span>
        </a>
        <button aria-controls="navbarSupportedContent"
                aria-expanded="false"
                aria-label="Toggle navigation"
                class="navbar-toggler"
                data-bs-target="#navbarSupportedContent"
                data-bs-toggle="collapse"
                type="button">
            <span class="navbar-toggler-icon"></span>
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
                  class="d-flex mb-3 mb-lg-0 search-form order-3 order-lg-2 mx-lg-2"
                  method="GET"
                  role="search">

                <div class="input-group">
                    <input class="form-control"
                           name="search"
                           placeholder="Apa yang kamu cari?"
                           type="text"
                           value="{{ request('search') }}">
                    <button class="btn"
                            type="submit">
                        <i aria-hidden="true"
                           class="bi bi-search"></i>
                    </button>
                </div>
            </form>

            <div class="d-flex align-items-center mb-2 mb-lg-0 ms-2 order-3 order-lg-2">
                <div class="dropdown theme-toggle-dropdown">
                    <button aria-expanded="false"
                            aria-label="Ubah tema"
                            class="btn rounded-2 px-2 py-1 theme-toggle-btn"
                            data-bs-toggle="dropdown"
                            title="Ubah tema"
                            type="button">
                        <i aria-hidden="true"
                           class="bi bi-circle-half"></i>
                        <span class="visually-hidden">Ubah tema</span>
                    </button>
                    <ul class="dropdown-menu border-primary shadow-sm rounded-3">
                        <li>
                            <a class="dropdown-item"
                               data-theme="system"
                               href="#">
                                <i aria-hidden="true"
                                   class="bi bi-display me-2"></i> Sistem
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               data-theme="light"
                               href="#">
                                <i aria-hidden="true"
                                   class="bi bi-sun me-2"></i> Terang
                            </a>
                        </li>
                        <li>
                            <a class="dropdown-item"
                               data-theme="dark"
                               href="#">
                                <i aria-hidden="true"
                                   class="bi bi-moon-stars me-2"></i> Gelap
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

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
</nav>
