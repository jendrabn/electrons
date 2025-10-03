@extends('layouts.app')

@section('content')
    {{-- Halaman indeks blog: container utama dan metadata Blog schema --}}
    <section aria-label="Blog Index"
             class="container"
             itemscope
             itemtype="https://schema.org/Blog">
        <div class="row">
            <div class="col-lg-8">
                {{-- Header: judul halaman dan deskripsi kategori/umum --}}
                @php
                    /** Menentukan kategori saat ini (jika rute kategori aktif) */
                    $currentCategory = null;
                    if (request()->routeIs('posts.category')) {
                        $catParam = request()->route('category');
                        $currentCategory = is_object($catParam)
                            ? $catParam
                            : \App\Models\Category::query()->where('slug', $catParam)->first();
                    }
                @endphp
                <header class="mb-5 text-center text-md-start blog-header">
                    <h1 class="fw-bold display-5 mb-2 d-inline-flex align-items-center"
                        itemprop="name">
                        {{ request()->has('search') && trim(request('search')) !== '' ? 'Blog' : $title ?? ($currentCategory->name ?? 'Blog') }}
                    </h1>
                    <p class="text-muted lead blog-subtitle mb-0"
                       itemprop="description">
                        {{ $currentCategory?->description ?? 'Temukan artikel, tutorial, dan insight seputar teknologi, pemrograman, dan produktivitas developer.' }}
                    </p>
                </header>

                {{-- Pemberitahuan hasil pencarian (SEO-friendly: H2 sesuai konteks) --}}
                @if (request()->has('search') && trim(request('search')) !== '')
                    <section aria-label="Hasil Pencarian"
                             class="mb-4 search-results">
                        <h2 class="h5 mb-2">Hasil Pencarian</h2>
                        <p class="text-muted">
                            <i aria-hidden="true"
                               class="bi bi-search me-2"></i>
                            Ditemukan untuk kata kunci "<span class="fw-semibold">{{ request('search') }}</span>"
                        </p>
                    </section>
                @else
                    <h2 class="visually-hidden">Daftar Artikel Blog</h2>
                @endif

                {{-- Daftar artikel --}}
                <div aria-label="Daftar Artikel"
                     class="d-flex flex-column gap-3"
                     role="list">
                    @forelse ($posts as $post)
                        {{-- Kartu artikel (schema.org BlogPosting) --}}
                        <x-post-item :post="$post"
                                     type="horizontal" />
                    @empty
                        {{-- Keadaan kosong: tidak ada artikel --}}
                        <div class="text-center py-5">
                            <div aria-hidden="true"
                                 class="mb-3">
                                <i class="bi bi-journal-x display-1 text-muted"></i>
                            </div>
                            <h2>Belum ada artikel</h2>
                            <p class="text-muted">
                                Tidak ada artikel yang ditemukan.
                            </p>
                        </div>
                    @endforelse
                </div>

                {{-- Navigasi pagination --}}
                <nav aria-label="Pagination"
                     class="mt-4">
                    {{ $posts->links() }}
                </nav>
            </div>
            {{-- Sidebar: konten tambahan, populer, terbaru, kategori, tag --}}
            <div class="col-lg-4">
                @include('partials.sidebar')
            </div>
        </div>
    </section>
@endsection
