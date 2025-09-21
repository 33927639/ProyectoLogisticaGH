<?php

namespace App\Filament\Resources\TblMaintenanceRequestResource\Pages;

use App\Filament\Resources\TblMaintenanceRequestResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblMaintenanceRequest extends EditRecord
{
    protected static string $resource = TblMaintenanceRequestResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
