<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1"
          name="viewport">

    {!! SEO::generate() !!}
    {!! JsonLd::generate() !!}

    <title>{{ config('app.name') }}</title>

    <link href="{{ asset('favicon.ico') }}"
          rel="icon"
          type="image/x-icon">

    <link href="https://fonts.googleapis.com"
          rel="preconnect">
    <link crossorigin
          href="https://fonts.gstatic.com"
          rel="preconnect">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
          rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.9/glider.min.css"
          rel="stylesheet">

    <meta content="{{ csrf_token() }}"
          name="csrf-token">

    @vite('resources/scss/style.scss')
    @yield('styles')
    @stack('styles')
</head>

<body>
    <header class="sticky-top">
        @include('partials.navbar')
    </header>

    <main class="my-5">
        @yield('content')
    </main>

    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.9/glider.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const dropdowns = document.querySelectorAll('.dropdown-toggle-custom');
            dropdowns.forEach(dropdown => {
                dropdown.addEventListener('show.bs.dropdown', function() {
                    dropdown.querySelector('i').classList.remove('bi-chevron-down');
                    dropdown.querySelector('i').classList.add('bi-chevron-up');
                });
                dropdown.addEventListener('hide.bs.dropdown', function() {
                    dropdown.querySelector('i').classList.remove('bi-chevron-up');
                    dropdown.querySelector('i').classList.add('bi-chevron-down');
                });
            });

            const tooltipTriggerList = document.querySelectorAll('[data-bs-toggle="tooltip"]')
            const tooltipList = [...tooltipTriggerList].map(tooltipTriggerEl => new bootstrap.Tooltip(
                tooltipTriggerEl))

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
        });
    </script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>
