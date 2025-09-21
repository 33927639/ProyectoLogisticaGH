<?php

namespace App\Filament\Resources\TblAlertStatusResource\Pages;

use App\Filament\Resources\TblAlertStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblAlertStatuses extends ListRecords
{
    protected static string $resource = TblAlertStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
