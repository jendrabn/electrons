@extends('layouts.auth')

@section('auth-form')
    <div class="py-3">
        <div class="mx-auto"
             style="max-width:420px;">
            <h3 class="mb-3 fw-bold text-center">Login</h3>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form action="{{ route('auth.login') }}"
                  method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Email atau Username</label>
                    <input class="form-control"
                           name="login"
                           placeholder="Email atau username"
                           required
                           value="{{ old('login') }}" />
                    @error('login')
                        <div class="text-danger small">{{ $message }}</div>
                    @enderror
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input class="form-control"
                               id="login-password"
                               name="password"
                               placeholder="Password"
                               required
                               type="password" />
                        <button aria-label="Show password"
                                class="btn btn-outline-secondary"
                                id="toggle-login-password"
                                type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div class="form-check">
                        <input class="form-check-input"
                               id="remember"
                               name="remember"
                               type="checkbox" />
                        <label class="form-check-label"
                               for="remember">Remember</label>
                    </div>
                    <a href="{{ route('auth.forgot') }}">Lupa Password?</a>
                </div>

                <div class="d-grid mb-3">
                    <button class="btn btn-primary">Masuk</button>
                </div>

                <div class="text-center mb-2">atau</div>

                <div class="d-grid mb-3">
                    <a class="btn btn-outline-danger"
                       href="{{ route('auth.google') }}">
                        <i class="bi bi-google me-2"></i> Masuk dengan Google
                    </a>
                </div>

                <div class="text-center small">Belum punya akun? <a href="{{ route('auth.show.register') }}">Daftar</a>
                </div>
            </form>
        </div>
    </div>
@endsection
