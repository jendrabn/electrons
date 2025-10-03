<?php

namespace App\Providers\Filament;

use App\Filament\Shared\Pages\Auth\EditProfile;
use App\Http\Middleware\EnsureUserIsAuthor;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationItem;
use Filament\Pages\Dashboard;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Filament\Widgets\AccountWidget;
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
                'primary' => Color::Blue,
            ])
            ->topNavigation()
            ->navigationItems([
                NavigationItem::make('Blog')
                    ->url(url('/'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->sort(80),
                NavigationItem::make('Komunitas')
                    ->url(url('/community'))
                    ->icon('heroicon-o-arrow-top-right-on-square')
                    ->sort(90),
            ])
            ->resourceCreatePageRedirect('index')
            ->profile(EditProfile::class)
            ->emailVerification(isRequired: env('FILAMENT_EMAIL_VERIFICATION', false))
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
                EnsureUserIsAuthor::class,
            ]);
    }
}
