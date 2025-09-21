<?php

namespace App\Filament\Resources\TblDeliveryResource\Pages;

use App\Filament\Resources\TblDeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblDelivery extends EditRecord
{
    protected static string $resource = TblDeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
