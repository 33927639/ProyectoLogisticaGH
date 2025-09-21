<?php

namespace App\Filament\Resources\TblKilometerResource\Pages;

use App\Filament\Resources\TblKilometerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblKilometers extends ListRecords
{
    protected static string $resource = TblKilometerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
