<?php

namespace App\Filament\Resources\TblProductResource\Pages;

use App\Filament\Resources\TblProductResource;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;

class ProductDescription extends ViewRecord
{
    protected static string $resource = TblProductResource::class;

    public function getTitle(): string
    {
        return 'Descripción del Producto';
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist->schema([
            Section::make('Descripción')
                ->icon('heroicon-o-document-text')
                ->description('Detalle completo del producto seleccionado')
                ->schema([
                    TextEntry::make('descripcion')
                        ->label(false) // Oculta la etiqueta para que solo se muestre el texto
                        ->markdown()   // Permite saltos de línea y formato si lo usas
                        ->size('lg')
                        ->color('gray'),
                ])
                ->columnSpanFull(),
        ]);
    }
}
