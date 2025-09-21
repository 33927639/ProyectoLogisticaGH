<?php

namespace App\Filament\Resources\TblExpenseTypeResource\Pages;

use App\Filament\Resources\TblExpenseTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblExpenseType extends EditRecord
{
    protected static string $resource = TblExpenseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
