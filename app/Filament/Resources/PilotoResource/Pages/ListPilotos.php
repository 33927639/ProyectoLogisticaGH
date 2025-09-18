<?php

namespace App\Filament\Resources\PilotoResource\Pages;

use App\Filament\Resources\PilotoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPilotos extends ListRecords
{
    protected static string $resource = PilotoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
