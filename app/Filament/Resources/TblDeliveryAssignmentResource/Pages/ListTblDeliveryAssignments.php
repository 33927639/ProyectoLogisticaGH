<?php

namespace App\Filament\Resources\TblDeliveryAssignmentResource\Pages;

use App\Filament\Resources\TblDeliveryAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblDeliveryAssignments extends ListRecords
{
    protected static string $resource = TblDeliveryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
