<?php

namespace App\Filament\Resources\TblKilometerResource\Pages;

use App\Filament\Resources\TblKilometerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblKilometer extends EditRecord
{
    protected static string $resource = TblKilometerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
