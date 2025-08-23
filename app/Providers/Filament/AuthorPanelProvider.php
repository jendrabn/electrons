<?php

namespace App\Providers\Filament;

use App\Filament\Author\Pages\Dashboard;
use App\Filament\Pages\Auth\EditProfile;
use App\Filament\Pages\Auth\Login;
use App\Filament\Pages\Auth\Register;
use App\Filament\Pages\Auth\ResetPassword;
use App\Http\Middleware\EnsureAuthorRole;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class AuthorPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('author')
            ->path('author')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(ResetPassword::class)
            ->emailVerification()
            ->profile(EditProfile::class, false)
            ->sidebarCollapsibleOnDesktop()
            ->sidebarFullyCollapsibleOnDesktop()
            ->darkMode(false)
            ->topNavigation()
            ->renderHook('panels::auth.login.form.after', fn() => view('filament.components.button-google'))
            ->colors([
                'primary' => Color::Blue,
            ])
            ->discoverResources(in: app_path('Filament/Author/Resources'), for: 'App\\Filament\\Author\\Resources')
            ->discoverPages(in: app_path('Filament/Author/Pages'), for: 'App\\Filament\\Author\\Pages')
            ->pages([])
            ->discoverWidgets(in: app_path('Filament/Author/Widgets'), for: 'App\\Filament\\Author\\Widgets')
            ->widgets([
                Widgets\AccountWidget::class,
            ])
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                VerifyCsrfToken::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
                EnsureAuthorRole::class,
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
