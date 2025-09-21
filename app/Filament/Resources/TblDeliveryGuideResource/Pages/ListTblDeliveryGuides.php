<?php

namespace App\Filament\Resources\TblDeliveryGuideResource\Pages;

use App\Filament\Resources\TblDeliveryGuideResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblDeliveryGuides extends ListRecords
{
    protected static string $resource = TblDeliveryGuideResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
