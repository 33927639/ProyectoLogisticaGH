<?php

namespace App\Filament\Resources\TblRouteResource\Pages;

use App\Filament\Resources\TblRouteResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblRoutes extends ListRecords
{
    protected static string $resource = TblRouteResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
