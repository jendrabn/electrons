<?php

namespace App\Http\Responses;

use Filament\Auth\Http\Responses\LoginResponse as BaseLoginResponse;
use Filament\Pages\Dashboard;
use Illuminate\Http\RedirectResponse;
use Livewire\Features\SupportRedirects\Redirector;

class LoginResponse extends BaseLoginResponse
{
    public function toResponse($request): RedirectResponse|Redirector
    {
        if (auth()->user()->isAdmin()) {
            return redirect()->to(Dashboard::getUrl(panel: 'admin'));
        }

        if (auth()->user()->isAuthor()) {
            return redirect()->to(Dashboard::getUrl(panel: 'author'));
        }

        return parent::toResponse($request);
    }
}
