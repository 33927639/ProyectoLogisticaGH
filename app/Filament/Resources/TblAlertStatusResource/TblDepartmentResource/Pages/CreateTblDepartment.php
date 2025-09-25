<?php

namespace App\Filament\Resources\TblDepartmentResource\Pages;

use App\Filament\Resources\TblDepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;

class CreateTblDepartment extends CreateRecord
{
    protected static string $resource = TblDepartmentResource::class;

    public function getTitle(): string
    {
        return 'Crear Departamento';
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('create')
                ->label('Guardar')
                ->submit('create'),

            Actions\Action::make('createAnother')
                ->label('Guardar y crear otro')
                ->submit('createAnother'),

            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
