<?php

namespace App\Filament\Resources\TblRoleResource\Pages;

use App\Filament\Resources\TblRoleResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblRole extends EditRecord
{
    protected static string $resource = TblRoleResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
