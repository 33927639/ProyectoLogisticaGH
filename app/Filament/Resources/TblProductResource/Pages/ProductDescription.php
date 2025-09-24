<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use Filament\Resources\Pages\ViewRecord;

class ProductDescription extends ViewRecord
{
    protected static string $resource = TblProductResource::class;

    public function getTitle(): string
    {
        return 'Descripción del Producto';
    }
}
