<?php

namespace App\Filament\Resources\TblIncomeResource\Pages;

use App\Filament\Resources\TblIncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblIncome extends EditRecord
{
    protected static string $resource = TblIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
