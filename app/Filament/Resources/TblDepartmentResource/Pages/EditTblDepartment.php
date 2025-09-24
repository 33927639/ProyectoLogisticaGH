<?php

namespace App\Filament\Resources\TblDepartmentResource\Pages;

use App\Filament\Resources\TblDepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTblDepartment extends EditRecord
{
    protected static string $resource = TblDepartmentResource::class;

    public function getTitle(): string
    {
        return 'Editar Departamento';
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('save')
                ->label('Guardar cambios')
                ->submit('save'),

            Actions\Action::make('cancel')
                ->label('Cancelar')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}

