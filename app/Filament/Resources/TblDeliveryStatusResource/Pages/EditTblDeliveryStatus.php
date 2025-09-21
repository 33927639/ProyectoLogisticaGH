<?php

namespace App\Filament\Resources\TblDeliveryStatusResource\Pages;

use App\Filament\Resources\TblDeliveryStatusResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblDeliveryStatus extends EditRecord
{
    protected static string $resource = TblDeliveryStatusResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
