<?php

namespace App\Filament\Resources\TblCustomerResource\Pages;

use App\Filament\Resources\TblCustomerResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblCustomer extends EditRecord
{
    protected static string $resource = TblCustomerResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
