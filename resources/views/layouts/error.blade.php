<!doctype html>
<html lang="id">

<head>
    <meta charset="utf-8">
    <meta content="width=device-width, initial-scale=1"
          name="viewport">
    <title>@yield('title', 'Kesalahan') - {{ config('app.name') }}</title>

    <link href="{{ asset('favicon.ico') }}"
          rel="icon"
          type="image/x-icon">

    @vite('resources/scss/style.scss')

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css"
          rel="stylesheet">

    <style>
        html,
        body {
            height: 100%;
            margin: 0;
            font-family: ui-sans-serif, system-ui, -apple-system, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif;
            background: linear-gradient(180deg, #f8fafc 0%, #ffffff 100%);
            color: #111827;
        }

        .error-page {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
        }

        .error-card {
            max-width: 820px;
            width: 100%;
            border-radius: 12px;
            box-shadow: 0 20px 50px rgba(2, 6, 23, 0.08);
            border: 1px solid rgba(15, 23, 42, 0.04);
            background: #fff;
        }

        .error-code {
            font-size: 4rem;
            font-weight: 700;
            line-height: 1;
        }

        .error-title {
            font-size: 1.25rem;
            font-weight: 700;
        }

        .error-sub {
            color: #6b7280;
        }

        @media (max-width: 576px) {
            .error-code {
                font-size: 2.25rem;
            }
        }
    </style>
</head>

<body>
    <main class="error-page">
        <div class="card error-card">
            <div class="card-body p-4 p-md-5">
                <div class="row g-3 align-items-center">
                    <div class="col-auto text-center">
                        <div class="d-inline-flex align-items-baseline">
                            <div class="error-code text-primary me-3">@yield('code')</div>
                        </div>
                    </div>
                    <div class="col">
                        <div class="error-title mb-1">@yield('title')</div>
                        <p class="error-sub mb-3">@yield('message')</p>

                        <div class="d-flex gap-2">
                            <a class="btn btn-primary btn-sm"
                               href="{{ route('home') ?? url('/') }}">
                                <i class="bi bi-house-door-fill me-1"></i> Back to Home
                            </a>
                            <a class="btn btn-outline-secondary btn-sm"
                               href="{{ url()->previous() }}">
                                <i class="bi bi-arrow-left-circle me-1"></i> Back to Previous Page
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>
