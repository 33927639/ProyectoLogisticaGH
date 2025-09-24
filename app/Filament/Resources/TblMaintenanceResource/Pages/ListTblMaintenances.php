<?php

namespace App\Filament\Resources\TblMaintenanceResource\Pages;

use App\Filament\Resources\TblMaintenanceResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblMaintenances extends ListRecords
{
    protected static string $resource = TblMaintenanceResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
