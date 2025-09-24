<?php

namespace App\Filament\Resources\TblRouteResource\Pages;

use App\Filament\Resources\TblRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblRoute extends EditRecord
{
    protected static string $resource = TblRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
