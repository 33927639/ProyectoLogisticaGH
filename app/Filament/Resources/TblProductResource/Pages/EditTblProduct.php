<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;

class EditTblProduct extends EditRecord
{
    protected static string $resource = TblProductResource::class;

    protected static ?string $title = 'Editar Producto';

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->label('Eliminar'),
        ];
    }

  protected function getFormActions(): array
  {
        return [
            Actions\Action::make('save')
                ->label('Guardar') // 👈 Traducción de "Save"
                ->submit('save'),

            Actions\Action::make('cancel')
                ->label('Cancelar') // 👈 Traducción de "Cancel"
                    ->color('secondary')
                ->url($this->previousUrl ?? $this->getResource()::getUrl('index')),
        ];
  }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::statement("CALL sp_products_update(?, ?, ?, ?, ?)", [
            $record->id,
            $data['name'],
            $data['stock'],
            $data['price'],
            $data['status'] ?? 1,
        ]);

        return $record;
    }

    protected function handleRecordDeletion(\Illuminate\Database\Eloquent\Model $record): void
    {
        DB::statement("CALL sp_products_delete(?)", [
            $record->id,
        ]);
    }
}
