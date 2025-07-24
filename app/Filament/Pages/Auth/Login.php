<?php

namespace App\Filament\Pages\Auth;

use Filament\Forms\Form;
use Filament\Http\Responses\Auth\Contracts\LoginResponse;
use Filament\Pages\Auth\Login as BaseLogin;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        // Mendapatkan IP address untuk rate limiting
        $key = $this->throttleKey();

        // Cek apakah sudah mencapai batas maksimal percobaan
        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'data.email' => __('auth.throttle', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60),
                ]),
            ]);
        }

        try {
            // Coba authenticate menggunakan parent method
            $response = parent::authenticate();

            // Jika berhasil, clear rate limiter
            RateLimiter::clear($key);

            return $response;
        } catch (ValidationException $exception) {
            // Jika gagal login, tambah hit ke rate limiter
            RateLimiter::hit($key, 60); // 60 detik

            throw $exception;
        }
    }

    /**
     * Generate unique key untuk rate limiting berdasarkan email + IP
     */
    protected function throttleKey(): string
    {
        $email = strtolower($this->form->getState()['email'] ?? '');
        $ip = request()->ip();

        return 'login.attempts:' . $email . '|' . $ip;
    }
}
