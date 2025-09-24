<?php

namespace App\Filament\Resources\TblExpenseResource\Pages;

use App\Filament\Resources\TblExpenseResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblExpenses extends ListRecords
{
    protected static string $resource = TblExpenseResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
