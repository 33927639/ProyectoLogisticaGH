<?php

namespace App\Filament\Resources\ProductResource\Pages;

use App\Filament\Resources\ProductResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListProducts extends ListRecords
{
    protected static string $resource = ProductResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('exportCsv')
                ->label('Exportar CSV')
                ->icon('heroicon-o-arrow-down-tray')
                ->url(route('products.export'))
                ->openUrlInNewTab(),

            Actions\Action::make('printList')
                ->label('Imprimir listado')
                ->icon('heroicon-o-printer')
                ->url(route('products.print'))
                ->openUrlInNewTab(),
        ];
    }
}
