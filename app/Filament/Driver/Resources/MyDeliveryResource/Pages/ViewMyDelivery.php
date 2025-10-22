<?php

namespace App\Filament\Driver\Resources\MyDeliveryResource\Pages;

use App\Filament\Driver\Resources\MyDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewMyDelivery extends ViewRecord
{
    protected static string $resource = MyDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }
    
    public function getTitle(): string
    {
        return 'Detalle de Entrega #' . $this->record->id_delivery;
    }
}