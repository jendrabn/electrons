@extends('layouts.auth')

@section('auth-form')
    <div class="py-3">
        <div class="mx-auto"
             style="max-width:420px;">
            <h3 class="mb-3 fw-bold text-center">Lupa Password</h3>

            @if (session('status'))
                <div class="alert alert-success">{{ session('status') }}</div>
            @endif

            <form action="{{ route('auth.send-reset') }}"
                  method="POST">
                @csrf
                <div class="mb-3">
                    <label class="form-label">Email</label>
                    <input class="form-control"
                           name="email"
                           required
                           value="{{ old('email') }}" />
                </div>
                <div class="d-grid mb-3">
                    <button class="btn btn-primary">Kirim Link Reset</button>
                </div>
                <div class="text-center small">Ingat password? <a href="{{ route('auth.show.login') }}">Masuk</a></div>
            </form>
        </div>
    </div>
@endsection
