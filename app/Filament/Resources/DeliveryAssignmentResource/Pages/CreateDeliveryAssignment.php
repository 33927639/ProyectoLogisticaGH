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

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        // Asegurar que assignment_date tenga valor
        if (empty($data['assignment_date'])) {
            $data['assignment_date'] = now()->format('Y-m-d');
        }
        
        // Asegurar que assigned_at tenga valor
        if (empty($data['assigned_at'])) {
            $data['assigned_at'] = now();
        }

        return $data;
    }
}
