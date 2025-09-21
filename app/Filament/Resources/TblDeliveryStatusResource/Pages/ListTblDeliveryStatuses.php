<?php

namespace App\Filament\Resources\TblDeliveryStatusResource\Pages;

use App\Filament\Resources\TblDeliveryStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblDeliveryStatuses extends ListRecords
{
    protected static string $resource = TblDeliveryStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
