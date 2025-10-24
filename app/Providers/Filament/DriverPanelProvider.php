<?php

namespace App\Providers\Filament;

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

class DriverPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->id('driver')
            ->path('conductor')
            ->brandName('SanaLogistics - Conductor')
            ->darkMode(false)
            ->login()
            ->colors([
                'primary' => Color::Blue,
            ])
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label(fn() => auth('driver')->user() ? auth('driver')->user()->first_name . ' ' . auth('driver')->user()->last_name : 'Usuario')
                    ->url(fn() => '#'),
                'logout_clean' => \Filament\Navigation\MenuItem::make()
                    ->label('Logout Completo')
                    ->url('/logout-all')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ])
            ->discoverResources(in: app_path('Filament/Driver/Resources'), for: 'App\\Filament\\Driver\\Resources')
            ->discoverPages(in: app_path('Filament/Driver/Pages'), for: 'App\\Filament\\Driver\\Pages')
            ->discoverWidgets(in: app_path('Filament/Driver/Widgets'), for: 'App\\Filament\\Driver\\Widgets')
            ->widgets([
                \App\Filament\Driver\Widgets\MyConductorStatsWidget::class,
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
            ])
            ->authGuard('driver');
    }
}
