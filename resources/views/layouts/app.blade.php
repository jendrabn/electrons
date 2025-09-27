<!doctype html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1"
          name="viewport">

    {!! SEO::generate(true) !!}
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
        // Small helper to show Bootstrap 5 toasts from JS
        function showToast(type, message) {
            try {
                const containerId = 'toast-container';
                let container = document.getElementById(containerId);
                if (!container) {
                    container = document.createElement('div');
                    container.id = containerId;
                    container.className = 'position-fixed top-0 end-0 p-3';
                    container.style.zIndex = '1080';
                    document.body.appendChild(container);
                }

                const wrapper = document.createElement('div');
                wrapper.className = 'toast mb-2 align-items-center text-white text-bg-' + (type === 'success' ? 'success' :
                    (
                        type === 'info' ? 'info' : 'danger')) + ' border-0';
                wrapper.role = 'alert';
                wrapper.setAttribute('aria-live', 'assertive');
                wrapper.setAttribute('aria-atomic', 'true');

                wrapper.innerHTML = `
                    <div class="d-flex">
                        <div class="toast-body">${message}</div>
                        <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast" aria-label="Close"></button>
                    </div>
                `;

                container.appendChild(wrapper);
                const toast = new bootstrap.Toast(wrapper, {
                    delay: 5000
                });
                toast.show();
                wrapper.addEventListener('hidden.bs.toast', function() {
                    wrapper.remove();
                });
            } catch (e) {
                console.error('showToast failed', e);
                alert(message);
            }
        }

        // Show validation errors (if any) as Bootstrap toasts
        @if ($errors->any())
            (function() {
                const messages = {!! json_encode($errors->all()) !!};
                messages.forEach(msg => showToast('danger', msg));
            })();
        @endif

        // Show session status / error as toasts as well (global)
        @if (session('status'))
            showToast('success', @json(session('status')));
        @endif

        @if (session('error'))
            showToast('danger', @json(session('error')));
        @endif

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
