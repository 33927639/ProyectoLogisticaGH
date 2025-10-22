<?php

namespace App\Filament\Resources\DeliveryAssignmentResource\Pages;

use App\Filament\Resources\DeliveryAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListDeliveryAssignments extends ListRecords
{
    protected static string $resource = DeliveryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
