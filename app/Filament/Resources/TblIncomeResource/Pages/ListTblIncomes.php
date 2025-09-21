<?php

namespace App\Filament\Resources\TblIncomeResource\Pages;

use App\Filament\Resources\TblIncomeResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblIncomes extends ListRecords
{
    protected static string $resource = TblIncomeResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
