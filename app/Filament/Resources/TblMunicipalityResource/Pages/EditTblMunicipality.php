<?php

namespace App\Filament\Resources\TblMunicipalityResource\Pages;

use App\Filament\Resources\TblMunicipalityResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblMunicipality extends EditRecord
{
    protected static string $resource = TblMunicipalityResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
