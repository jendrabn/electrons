 <nav class="navbar navbar-expand-lg bg-light navbar-light">
     <div class="container">
         <a class="navbar-brand"
            href="{{ route('home') }}">
             <img alt="Logo"
                  src="{{ asset('images/logo.png') }}"
                  style="max-width: 200px;">
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
             <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                 <li class="nav-item">
                     <a aria-current="page"
                        class="nav-link {{ request()->is('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">Home</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link {{ request()->is('about') ? 'active' : '' }}"
                        href="{{ route('about') }}">Tentang</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link {{ request()->is('posts') ? 'active' : '' }}"
                        href="{{ route('posts.index') }}">Blog</a>
                 </li>

                 <li class="nav-item dropdown">
                     <a aria-expanded="false"
                        class="nav-link dropdown-toggle"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button">
                         Kategori
                     </a>
                     <ul class="dropdown-menu">
                         @foreach ($categories as $category)
                             <li>
                                 <a class="dropdown-item"
                                    href="{{ route('posts.category', $category->slug) }}">{{ $category->name }}
                                 </a>
                             </li>
                         @endforeach
                     </ul>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link {{ request()->is('contact') ? 'active' : '' }}"
                        href="{{ route('contact') }}">Kontak</a>
                 </li>
             </ul>

             <form action="{{ route('posts.index') }}"
                   class="d-flex w-50 mx-5 search-form"
                   method="GET"
                   role="search">
                 <div class="input-group">
                     <input aria-label="Search"
                            class="form-control"
                            name="search"
                            placeholder="Cari..."
                            type="search"
                            value="{{ request('search') }}">
                     <button class="btn btn-default"
                             type="submit">
                         <i class="bi bi-search"></i>
                     </button>
                 </div>
             </form>

             <ul class="navbar-nav ms-auto mb-2 mb-lg-0">
                 @if (auth()->check())
                     <li class="nav-item dropdown">
                         <a aria-expanded="false"
                            class="nav-link dropdown-toggle"
                            data-bs-toggle="dropdown"
                            href="#"
                            role="button">
                             {{ auth()->user()->name }}
                         </a>
                         <ul class="dropdown-menu">
                             <li>
                                 <a class="dropdown-item"
                                    href="{{ auth()->user()->hasRole('admin') ? route('filament.admin.pages.dashboard') : route('filament.author.pages.dashboard') }}">
                                     Dashboard
                                 </a>
                             </li>
                             <li>
                                 <a class="dropdown-item"
                                    href="{{ auth()->user()->hasRole('admin') ? route('filament.admin.auth.logout') : route('filament.author.auth.logout') }}">Logout</a>
                             </li>
                         </ul>
                     </li>
                 @else
                     <li class="nav-item">
                         <a class="btn btn-outline-primary rounded-2"
                            href="{{ route('filament.author.auth.login') }}"
                            target="_blank">Masuk/Daftar</a>
                     </li>
                 @endif
             </ul>
         </div>
     </div>
 </nav>
