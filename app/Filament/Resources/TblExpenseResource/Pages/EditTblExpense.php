<?php

namespace App\Filament\Resources\TblExpenseResource\Pages;

use App\Filament\Resources\TblExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblExpense extends EditRecord
{
    protected static string $resource = TblExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
