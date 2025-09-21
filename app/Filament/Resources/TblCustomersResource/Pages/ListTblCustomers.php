<?php

namespace App\Filament\Resources\TblCustomersResource\Pages;

use App\Filament\Resources\TblCustomersResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblCustomers extends ListRecords
{
    protected static string $resource = TblCustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
