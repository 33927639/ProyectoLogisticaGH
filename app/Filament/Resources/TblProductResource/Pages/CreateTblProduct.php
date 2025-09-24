<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use App\Models\TblProduct;
use Filament\Actions;
use Filament\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;

class CreateTblProduct extends CreateRecord
{
    protected static string $resource = TblProductResource::class;

    public function getTitle(): string
    {
        return 'Crear Producto';
    }

    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $result = DB::select("CALL sp_products_insert(?, ?, ?, ?)", [
            $data['name'],
            $data['stock'],
            $data['price'],
            $data['status'] ?? 1,
        ]);


        $id = $result[0]->id ?? null;

        return TblProduct::findOrFail($id);
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('create');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('create')
                ->label('Crear') // 👈 Traducción de "Create"
                ->submit('create'),

            Action::make('createAnother')
                ->label('Crear y crear otro') // 👈 Traducción de "Create & create another"
                ->color('secondary')
                ->submit('createAnother'),

            Action::make('cancel')
                ->label('Cancelar') // 👈 Traducción de "Cancel"
                ->color('secondary')
                ->url($this->previousUrl ?? $this->getResource()::getUrl('index')),
        ];
    }
}
