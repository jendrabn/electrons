<?php

namespace App\Filament\Author\Pages\Auth;

use Filament\Auth\Http\Responses\Contracts\LoginResponse;
use Filament\Auth\Pages\Login as BaseLogin;
use Filament\Schemas\Schema;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Validation\ValidationException;

class Login extends BaseLogin
{

    public function form(Schema $schema): Schema
    {
        return $schema
            ->components([
                $this->getEmailFormComponent(),
                $this->getPasswordFormComponent(),
            ]);
    }

    public function authenticate(): ?LoginResponse
    {
        $key = $this->throttleKey();

        if (RateLimiter::tooManyAttempts($key, 3)) {
            $seconds = RateLimiter::availableIn($key);

            throw ValidationException::withMessages([
                'data.email' => __('auth.throttled', [
                    'seconds' => $seconds,
                    'minutes' => ceil($seconds / 60)
                ]),
            ]);
        }

        try {
            $response = parent::authenticate();

            RateLimiter::clear($key);

            return $response;
        } catch (ValidationException $e) {
            RateLimiter::hit($key, 60);

            throw $e;
        }
    }

    private function throttleKey(): string
    {
        $email = strtolower($this->form->getState()['email'] ?? '');
        $ip = request()->ip();

        return "login.attempts:$email|$ip";
    }
}
