@extends('layouts.auth')

@section('auth-form')
    <div class="py-3">
        <div class="mx-auto"
             style="max-width:520px;">
            <h3 class="mb-3 fw-bold text-center">Daftar</h3>

            <form action="{{ route('auth.register') }}"
                  method="POST">
                @csrf

                <div class="mb-3">
                    <label class="form-label">Nama</label>
                    <input class="form-control"
                           name="name"
                           required
                           value="{{ old('name') }}" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Username</label>
                    <input class="form-control"
                           name="username"
                           required
                           value="{{ old('username') }}" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control"
                           name="email"
                           required
                           value="{{ old('email') }}" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Password</label>
                    <div class="input-group">
                        <input class="form-control"
                               id="register-password"
                               name="password"
                               required
                               type="password" />
                        <button class="btn btn-outline-secondary"
                                id="toggle-register-password"
                                type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input class="form-control"
                               id="register-password-confirm"
                               name="password_confirmation"
                               required
                               type="password" />
                        <button class="btn btn-outline-secondary"
                                id="toggle-register-password-confirm"
                                type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button class="btn btn-primary">Daftar</button>
                </div>

                <div class="text-center small mt-3">Sudah punya akun? <a href="{{ route('auth.show.login') }}">Masuk</a>
                </div>
            </form>
        </div>
    </div>
@endsection
