<?php

namespace App\Filament\Resources\TblDeliveryAssignmentResource\Pages;

use App\Filament\Resources\TblDeliveryAssignmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblDeliveryAssignment extends EditRecord
{
    protected static string $resource = TblDeliveryAssignmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
