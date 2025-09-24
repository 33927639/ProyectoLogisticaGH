<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Illuminate\Support\Facades\DB;
use App\Models\TblProduct;

class CreateTblProduct extends CreateRecord
{
    protected static string $resource = TblProductResource::class;
    protected function handleRecordCreation(array $data): \Illuminate\Database\Eloquent\Model
    {
        $result = DB::select("CALL sp_products_insert(?, ?, ?, ?, ?)", [
            $data['name'],
            $data['stock'],
            $data['price'],
            $data['status'] ?? 1,
            $data['descripcion'] ?? null,
        ]);

        $id = $result[0]->id ?? null;
        return TblProduct::findOrFail($id);
    }
}
