<?php

namespace App\Providers\Filament;

use App\Filament\Author\Pages\Auth\EditProfile;
use App\Filament\Author\Pages\Auth\Login;
use App\Filament\Author\Pages\Auth\Register;
use App\Filament\Author\Pages\Auth\ResetPassword;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
use Filament\Widgets\FilamentInfoWidget;
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
            ->id('author')
            ->path('author')
            ->colors([
                'primary' => Color::Amber,
            ])
            ->topNavigation()
            ->resourceCreatePageRedirect('index')
            ->login(Login::class)
            ->registration(Register::class)
            ->passwordReset(ResetPassword::class)
            ->emailVerification()
            ->profile(EditProfile::class, false)
            ->renderHook('panels::auth.login.form.after', fn() => view('filament.components.button-google'))
            ->discoverResources(in: app_path('Filament/Author/Resources'), for: 'App\Filament\Author\Resources')
            ->discoverPages(in: app_path('Filament/Author/Pages'), for: 'App\Filament\Author\Pages')
            ->pages([
                Dashboard::class,
            ])
            ->discoverWidgets(in: app_path('Filament/Author/Widgets'), for: 'App\Filament\Author\Widgets')
            ->widgets([
                AccountWidget::class,
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
