<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Support\Facades\DB;
use App\Models\TblProduct;


class EditTblProduct extends EditRecord
{
    protected static string $resource = TblProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }

    protected function handleRecordUpdate(\Illuminate\Database\Eloquent\Model $record, array $data): \Illuminate\Database\Eloquent\Model
    {
        DB::statement("CALL sp_products_update(?, ?, ?, ?, ?, ?)", [
            $record->id,
            $data['name'],
            $data['stock'],
            $data['price'],
            $data['status'] ?? 1,
            $data['descripcion'] ?? null, // 👈 agregado
        ]);

        return $record;
    }
}
