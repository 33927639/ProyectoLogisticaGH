<?php

namespace App\Filament\Driver\Pages;

use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationLabel = 'Panel Principal';
    
    protected static ?string $title = 'Panel del Conductor';

    public function getWidgets(): array
    {
        return [
            \App\Filament\Driver\Widgets\DriverStatsWidget::class,
        ];
    }
    
    public function getColumns(): int | string | array
    {
        return 2;
    }
}