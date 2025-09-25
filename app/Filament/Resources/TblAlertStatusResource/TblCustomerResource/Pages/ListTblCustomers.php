<?php

namespace App\Filament\Resources\TblCustomerResource\Pages;

use App\Filament\Resources\TblCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblCustomers extends ListRecords
{
    protected static string $resource = TblCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Cliente'),
        ];
    }
}
