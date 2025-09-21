<?php

namespace App\Filament\Resources\TblVehicleResource\Pages;

use App\Filament\Resources\TblVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblVehicle extends EditRecord
{
    protected static string $resource = TblVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
