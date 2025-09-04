 <nav class="navbar navbar-expand-lg bg-light navbar-light sticky-top">
     <div class="container">
         <a class="navbar-brand"
            href="{{ route('home') }}">
             <img alt="Logo"
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
             <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                 <li class="nav-item">
                     <a aria-current="page"
                        class="nav-link {{ request()->is('home') ? 'active' : '' }}"
                        href="{{ route('home') }}">Home</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link {{ request()->is('posts') ? 'active' : '' }}"
                        href="{{ route('posts.index') }}">Blog</a>
                 </li>
                 <li class="nav-item">
                     <a class="nav-link"
                        href="#">Forum</a>
                 </li>
                 <li class="nav-item dropdown-center">
                     <a aria-expanded="false"
                        class="nav-link dropdown-toggle-custom"
                        data-bs-toggle="dropdown"
                        href="#"
                        role="button">
                         Kategori <i class="bi bi-chevron-down"
                            style="font-size: 14px;"></i>
                     </a>
                     <ul class="dropdown-menu border-primary">
                         @foreach ($categories as $category)
                             <li>
                                 <a class="dropdown-item"
                                    href="{{ route('posts.category', $category->slug) }}">{{ $category->name }}
                                 </a>
                             </li>
                         @endforeach
                     </ul>
                 </li>
             </ul>

             <form action="{{ route('posts.index') }}"
                   class="d-flex mb-3 mb-lg-0 search-form"
                   method="GET"
                   role="search">
                 <div class="input-group">
                     <input aria-label="Search"
                            class="form-control"
                            name="search"
                            placeholder="Apa yang kamu cari?"
                            type="search"
                            value="{{ request('search') }}">
                     <button class="btn btn-default bg-white border border-start-0"
                             type="submit">
                         <i class="bi bi-search"></i>
                     </button>
                 </div>
             </form>

             <ul class="navbar-nav ms-auto mb-2 mb-lg-0">

                 @if (auth()->check())
                     <li class="nav-item dropdown">
                         <button aria-expanded="false"
                                 class="nav-link dropdown-toggle-custom"
                                 data-bs-toggle="dropdown">
                             <div class="avatar">
                                 <img alt="{{ auth()->user()->name }}"
                                      class="rounded-circle"
                                      src="{{ auth()->user()->avatar_url }}"
                                      style="width: 35px; height: 35px;" />

                                 <span class="ms-2">{{ str()->words(auth()->user()->name, 2, '') }}</span>
                             </div>
                         </button>
                         <ul class="dropdown-menu border-primary">
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
                                 @php
                                     $logoutUrl = '';

                                     if (auth()->user()->isAdmin()) {
                                         $logoutUrl = route('filament.admin.auth.logout');
                                     } elseif (auth()->user()->isAuthor()) {
                                         $logoutUrl = route('filament.author.auth.logout');
                                     }
                                 @endphp

                                 <form action="{{ $logoutUrl }}"
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
                         <a class="btn btn-outline-primary rounded-2"
                            href="{{ route('filament.author.auth.login') }}"
                            target="_blank">Masuk/Daftar</a>
                     </li>
                 @endif
             </ul>
         </div>
     </div>

 </nav>
