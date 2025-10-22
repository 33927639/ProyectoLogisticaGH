<?php

namespace App\Filament\Resources\DeliveryAssignmentResource\Pages;

use App\Filament\Resources\DeliveryAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditDeliveryAssignment extends EditRecord
{
    protected static string $resource = DeliveryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
    
    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
