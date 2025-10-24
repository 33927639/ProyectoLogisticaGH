<?php

namespace App\Filament\Resources\DeliveryResource\Pages;

use App\Filament\Resources\DeliveryResource;
use Filament\Actions;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists;
use Filament\Infolists\Infolist;

class ViewDelivery extends ViewRecord
{
    protected static string $resource = DeliveryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\EditAction::make(),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Infolists\Components\Section::make('Información de Entrega')
                    ->schema([
                        Infolists\Components\Grid::make(2)
                            ->schema([
                                Infolists\Components\TextEntry::make('delivery_date')
                                    ->label('Fecha de Entrega')
                                    ->date(),
                                Infolists\Components\TextEntry::make('route')
                                    ->label('Ruta')
                                    ->getStateUsing(function ($record) {
                                        if ($record->route) {
                                            return $record->route->origin->name_municipality . ' → ' . $record->route->destination->name_municipality;
                                        }
                                        return 'Sin ruta asignada';
                                    }),
                                Infolists\Components\TextEntry::make('delivery_status.name_status')
                                    ->label('Estado de Entrega')
                                    ->badge()
                                    ->color(fn (string $state): string => match ($state) {
                                        'Pendiente' => 'warning',
                                        'En Ruta' => 'info',
                                        'Entregado' => 'success',
                                        'Cancelado' => 'danger',
                                        default => 'gray',
                                    }),
                                Infolists\Components\IconEntry::make('status')
                                    ->label('Activa')
                                    ->boolean(),
                            ]),
                    ]),
                    
                Infolists\Components\Section::make('Productos de la Entrega')
                    ->schema([
                        Infolists\Components\ViewEntry::make('products_table')
                            ->label('Productos')
                            ->view('filament.infolists.delivery-products-table')
                            ->viewData(function ($record) {
                                $products = \DB::table('delivery_products')
                                    ->join('products', 'delivery_products.product_id', '=', 'products.id_product')
                                    ->where('delivery_products.delivery_id', $record->id_delivery)
                                    ->select([
                                        'products.name as product_name',
                                        'delivery_products.quantity',
                                        'delivery_products.unit_price',
                                        'delivery_products.subtotal'
                                    ])
                                    ->get();
                                    
                                $total = $products->sum('subtotal');
                                
                                return [
                                    'products' => $products,
                                    'total' => $total
                                ];
                            })
                            ->columnSpanFull(),
                    ]),
            ]);
    }
}