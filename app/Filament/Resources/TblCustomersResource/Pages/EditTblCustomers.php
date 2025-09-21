<?php

namespace App\Filament\Resources\TblCustomersResource\Pages;

use App\Filament\Resources\TblCustomersResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblCustomers extends EditRecord
{
    protected static string $resource = TblCustomersResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
