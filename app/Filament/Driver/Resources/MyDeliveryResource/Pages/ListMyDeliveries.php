<?php

namespace App\Filament\Driver\Resources\MyDeliveryResource\Pages;

use App\Filament\Driver\Resources\MyDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyDeliveries extends ListRecords
{
    protected static string $resource = MyDeliveryResource::class;
    
    // Actualizar automáticamente cada 10 segundos
    protected static ?string $pollingInterval = '10s';

    protected function getHeaderActions(): array
    {
        return [
            // Los conductores no pueden crear entregas
        ];
    }
    
    protected function getHeaderWidgets(): array
    {
        return [
            \App\Filament\Driver\Widgets\MyConductorStatsWidget::class,
        ];
    }
}