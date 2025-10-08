@extends('layouts.app')

@section('content')
    <div class="auth-wrapper d-flex align-items-center justify-content-center"
         style="min-height:75vh;">
        <div class="container">
            <div class="row shadow sahdow-lg rounded rounded-3 overflow-hidden">
                <div class="col-lg-6 d-none d-lg-block bg-auth-image"
                     style="background-image: url('{{ asset('images/auth_wallpaper.webp') }}'); background-size: cover; background-position:center;">
                </div>
                <div class="col-lg-6 p-4">
                    @yield('auth-form')
                </div>
            </div>
        </div>
    </div>
@endsection

@section('styles')
    <style>
        .bg-auth-image {
            min-height: 420px;
        }

        @media (max-width: 991px) {
            .bg-auth-image {
                display: none;
            }
        }
    </style>
@endsection

@push('scripts')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            function wireToggle(toggleId, inputId) {
                const btn = document.getElementById(toggleId);
                const input = document.getElementById(inputId);
                if (!btn || !input) return;
                btn.addEventListener('click', function() {
                    const icon = btn.querySelector('i');
                    if (input.type === 'password') {
                        input.type = 'text';
                        if (icon) {
                            icon.classList.remove('bi-eye');
                            icon.classList.add('bi-eye-slash');
                        }
                    } else {
                        input.type = 'password';
                        if (icon) {
                            icon.classList.remove('bi-eye-slash');
                            icon.classList.add('bi-eye');
                        }
                    }
                });
            }

            // Common toggles used across auth pages
            wireToggle('toggle-login-password', 'login-password');
            wireToggle('toggle-register-password', 'register-password');
            wireToggle('toggle-register-password-confirm', 'register-password-confirm');
            wireToggle('toggle-reset-password', 'reset-password');
            wireToggle('toggle-reset-password-confirm', 'reset-password-confirm');
        });
    </script>
@endpush
