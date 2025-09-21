<?php

namespace App\Filament\Resources\TblCustomerResource\Pages;

use App\Filament\Resources\TblCustomerResource;
use Filament\Resources\Pages\CreateRecord;
use Filament\Actions\Action;

class CreateTblCustomer extends CreateRecord
{
    protected static string $resource = TblCustomerResource::class;

    public function getTitle(): string
    {
        return 'Registrar Cliente';
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Crear') // 👈 Traducción de "Create"
                ->submit('create'),

            Action::make('createAnother')
                ->label('Crear y crear otro') // 👈 Traducción de "Create & create another"
                ->submit('createAnother'),

            Action::make('cancel')
                ->label('Cancelar') // 👈 Traducción de "Cancel"
                ->url($this->previousUrl ?? $this->getResource()::getUrl('index')),
        ];
    }
}
