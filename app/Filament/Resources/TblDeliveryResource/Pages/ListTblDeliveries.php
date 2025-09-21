<?php

namespace App\Filament\Resources\TblDeliveryResource\Pages;

use App\Filament\Resources\TblDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblDeliveries extends ListRecords
{
    protected static string $resource = TblDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
