<?php

namespace App\Filament\Resources\TblDepartmentResource\Pages;

use App\Filament\Resources\TblDepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTblDepartments extends ListRecords
{
    protected static string $resource = TblDepartmentResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()
                ->label('Crear Departamento')
            ->icon('heroicon-s-plus')
        ];
    }
}
