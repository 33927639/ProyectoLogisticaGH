<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblProducts extends ListRecords
{
    protected static string $resource = TblProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Producto'),
        ];
    }
}
