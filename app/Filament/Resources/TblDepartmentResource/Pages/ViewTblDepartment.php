<?php

namespace App\Filament\Resources\TblDepartmentResource\Pages;

use App\Filament\Resources\TblDepartmentResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;

class ViewTblDepartment extends ViewRecord
{
    protected static string $resource = TblDepartmentResource::class;

    public function getTitle(): string
    {
        return 'Ver Departamento';
    }

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make()
                ->label('Editar'),

            Actions\DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

    protected function getFormActions(): array
    {
        return [
            Actions\Action::make('cancel')
                ->label('Volver')
                ->url($this->getResource()::getUrl('index')),
        ];
    }
}
