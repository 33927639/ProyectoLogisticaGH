<?php

namespace App\Filament\Driver\Resources\MyDeliveryResource\Pages;

use App\Filament\Driver\Resources\MyDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListMyDeliveries extends ListRecords
{
    protected static string $resource = MyDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    
    public function getTitle(): string
    {
        return 'Mis Entregas Asignadas';
    }
}