<?php

namespace App\Filament\Resources\TblVehicleResource\Pages;

use App\Filament\Resources\TblVehicleResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblVehicles extends ListRecords
{
    protected static string $resource = TblVehicleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
