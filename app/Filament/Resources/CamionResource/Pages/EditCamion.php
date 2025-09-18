<?php

namespace App\Filament\Resources\CamionResource\Pages;

use App\Filament\Resources\CamionResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditCamion extends EditRecord
{
    protected static string $resource = CamionResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
