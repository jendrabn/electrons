<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1"
          name="viewport">

    <meta content="{{ csrf_token() }}"
          name="csrf-token">

    <meta content="ca-pub-9750508834370473"
          name="google-adsense-account">

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
    <script>
        // Synchronous theme bootstrap to avoid FOUC (flash of wrong theme).
        // Read the Filament-compatible `theme` key and set data-bs-theme before CSS loads.
        (function() {
            try {
                var t = localStorage.getItem('theme');
            } catch (e) {
                var t = null;
            }

            if (t === 'dark') {
                document.documentElement.setAttribute('data-bs-theme', 'dark');
            } else if (t === 'light') {
                document.documentElement.setAttribute('data-bs-theme', 'light');
            } else {
                // system or unset — respect prefers-color-scheme
                try {
                    var prefersDark = window.matchMedia && window.matchMedia('(prefers-color-scheme: dark)').matches;
                    document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
                } catch (e) {
                    document.documentElement.setAttribute('data-bs-theme', 'light');
                }
            }
        })();
    </script>

    <script async
            src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-9750508834370473"
            crossorigin="anonymous"></script>

    @vite('resources/scss/style.scss')
    @yield('styles')
    @stack('styles')
</head>

<body class="d-flex flex-column min-vh-100">
    <a class="skip-link"
       href="#main-content">Lewati ke Konten</a>
    <header class="sticky-top">
        @include('partials.navbar')
    </header>

    <main class="my-4 my-lg-5"
          id="main-content"
          role="main">
        @yield('content')

        @include('partials.ads.display-responsive', ['slot' => '8485643721'])
    </main>

    @include('partials.footer')
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.7/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/glider-js/1.7.9/glider.min.js"></script>

    <script>
        (adsbygoogle = window.adsbygoogle || []).push({});
    </script>

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


        });
    </script>
    <script>
        (function() {
            var allowedHosts = ['localhost', '127.0.0.1', '0.0.0.0'];
            if (
                'serviceWorker' in navigator &&
                (window.location.protocol === 'https:' || allowedHosts.includes(window.location.hostname))
            ) {
                window.addEventListener('load', function() {
                    navigator.serviceWorker
                        .register('{{ asset('service-worker.js') }}')
                        .catch(function(error) {
                            console.error('Service worker registration failed:', error);
                        });
                });
            }
        })();
    </script>

    <script>
        (function() {
            const KEY = 'theme'; // Filament-compatible key

            function applyTheme(mode) {
                // mode: 'system' | 'light' | 'dark'
                if (mode === 'system') {
                    const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
                    document.documentElement.setAttribute('data-bs-theme', prefersDark ? 'dark' : 'light');
                } else {
                    document.documentElement.setAttribute('data-bs-theme', mode === 'dark' ? 'dark' : 'light');
                }

                // Notify other scripts (Filament) if they listen
                window.dispatchEvent(new CustomEvent('theme:change', {
                    detail: {
                        theme: mode
                    }
                }));
            }

            function readStoredTheme() {
                try {
                    return localStorage.getItem(KEY) || 'system';
                } catch (e) {
                    return 'system';
                }
            }

            function storeTheme(value) {
                try {
                    localStorage.setItem(KEY, value);
                } catch (e) {
                    /* ignore */
                }
            }

            function getNext(mode) {
                return mode === 'system' ? 'light' : mode === 'light' ? 'dark' : 'system';
            }

            const buttons = document.querySelectorAll('.theme-toggle-btn');

            // Initialize
            let current = readStoredTheme();
            applyTheme(current);

            // Update icon based on mode for all buttons
            function updateAllButtons(mode) {
                buttons.forEach(btn => {
                    const icon = btn.querySelector('i');
                    if (!icon) return;
                    if (mode === 'system') icon.className = 'bi bi-display';
                    else if (mode === 'light') icon.className = 'bi bi-sun';
                    else icon.className = 'bi bi-moon-stars';
                });
            }

            updateAllButtons(current);

            // React to system changes when in 'system' mode
            const mq = window.matchMedia('(prefers-color-scheme: dark)');
            mq.addEventListener && mq.addEventListener('change', () => {
                if (readStoredTheme() === 'system') {
                    applyTheme('system');
                    updateAllButtons('system');
                }
            });

            // Attach click handlers to all theme buttons
            buttons.forEach(btn => {
                btn.addEventListener('click', () => {
                    current = getNext(readStoredTheme());
                    storeTheme(current);
                    applyTheme(current);
                    updateAllButtons(current);
                });
            });

            // Keep icons in sync if some other script changes the theme
            window.addEventListener('theme:change', (e) => {
                const mode = e?.detail?.theme || readStoredTheme();
                updateAllButtons(mode);
            });
        })();
    </script>
    @yield('scripts')
    @stack('scripts')
</body>

</html>
