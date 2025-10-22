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

class AdminPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('admin')
            ->path('admin')
            ->brandName('SanaLogistics - Admin')
            ->login()
            ->userMenuItems([
                'profile' => \Filament\Navigation\MenuItem::make()
                    ->label(fn() => auth()->user() ? auth()->user()->first_name . ' ' . auth()->user()->last_name : 'Usuario')
                    ->url(fn() => '#'),
                'logout_clean' => \Filament\Navigation\MenuItem::make()
                    ->label('Logout Completo')
                    ->url('/logout-all')
                    ->icon('heroicon-o-arrow-right-on-rectangle'),
            ])
            ->colors([
                'primary' => Color::Amber,
            ])
            ->discoverResources(in: app_path('Filament/Resources'), for: 'App\\Filament\\Resources')
            ->discoverPages(in: app_path('Filament/Pages'), for: 'App\\Filament\\Pages')
            ->discoverWidgets(in: app_path('Filament/Widgets'), for: 'App\\Filament\\Widgets')
            ->widgets([
                // Notification System
                \App\Filament\Widgets\NotificationBellWidget::class,
                
                // Custom Dashboard Widgets
                \App\Filament\Widgets\DeliveryStatsWidget::class,
                \App\Filament\Widgets\LiveDeliveryStatusWidget::class,
                \App\Filament\Widgets\FleetStatsWidget::class,
                \App\Filament\Widgets\FinancialStatsWidget::class,
                \App\Filament\Widgets\DeliveryTrendChart::class,
                \App\Filament\Widgets\FinancialTrendChart::class,
                \App\Filament\Widgets\MaintenancePendingWidget::class,
                \App\Filament\Widgets\AlertsWidget::class,
                
                // Default Filament Widgets
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
            ])
            ->authMiddleware([
                Authenticate::class,
            ]);
    }
}
