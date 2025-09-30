@extends('layouts.auth')

@section('auth-form')
    <div class="py-3">
        <div class="mx-auto"
             style="max-width:480px;">
            <h3 class="mb-3 fw-bold text-center">Reset Password</h3>

            <form action="{{ route('password.store') }}"
                  method="POST">
                @csrf
                <input name="token"
                       type="hidden"
                       value="{{ $token ?? '' }}">

                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control"
                           name="email"
                           required
                           value="{{ old('email', request('email')) }}" />
                </div>

                <div class="mb-3">
                    <label class="form-label">Password Baru</label>
                    <div class="input-group">
                        <input class="form-control"
                               id="reset-password"
                               name="password"
                               required
                               type="password" />
                        <button class="btn btn-outline-secondary"
                                id="toggle-reset-password"
                                type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Konfirmasi Password</label>
                    <div class="input-group">
                        <input class="form-control"
                               id="reset-password-confirm"
                               name="password_confirmation"
                               required
                               type="password" />
                        <button class="btn btn-outline-secondary"
                                id="toggle-reset-password-confirm"
                                type="button">
                            <i class="bi bi-eye"></i>
                        </button>
                    </div>
                </div>

                <div class="d-grid mb-3">
                    <button class="btn btn-primary">Reset Password</button>
                </div>
            </form>
        </div>
    </div>
@endsection
