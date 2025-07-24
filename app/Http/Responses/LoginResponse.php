<?php

namespace App\Http\Responses;

use App\Enums\Role;
use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;
use Filament\Http\Responses\Auth\LoginResponse as BaseLoginResponse;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (auth()->user()->hasRole(Role::ADMIN->value)) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        } else if (auth()->user()->hasRole(Role::AUTHOR->value)) {
            return redirect()->to(Dashboard::getUrl(panel: 'author'));
        }

        return parent::toResponse($request);
    }
}
