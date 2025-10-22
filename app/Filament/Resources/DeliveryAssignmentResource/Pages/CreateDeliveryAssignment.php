<?php

namespace App\Filament\Resources\DeliveryAssignmentResource\Pages;

use App\Filament\Resources\DeliveryAssignmentResource;
use Filament\Resources\Pages\CreateRecord;

class CreateDeliveryAssignment extends CreateRecord
{
    protected static string $resource = DeliveryAssignmentResource::class;
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
