<?php

namespace App\Filament\Resources\TblExpenseTypeResource\Pages;

use App\Filament\Resources\TblExpenseTypeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblExpenseTypes extends ListRecords
{
    protected static string $resource = TblExpenseTypeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
