<nav class="navbar navbar-expand-lg bg-light navbar-light sticky-top navbar-elevated shadow-sm py-2">
    <div class="container">
        <a class="navbar-brand d-flex align-items-center me-4 me-lg-5 py-0"
           href="{{ route('home') }}">
            <img alt="Logo"
                 class="brand-logo"
                 src="{{ asset('images/logo.svg') }}">
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
                    <button aria-label="Cari"
                            class="btn btn-outline-secondary search-btn"
                            type="submit">
                        <i aria-hidden="true"
                           class="bi bi-search"></i>
                        <span class="visually-hidden">Cari</span>
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
</nav>
