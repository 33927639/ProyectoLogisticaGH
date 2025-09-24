<?php

namespace App\Filament\Resources\DosResource\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Venta;

class VentasMesActual extends BaseWidget
{
    public function table(Table $table): Table
    {
        return $table
            ->query(
                Venta::query()
                    ->whereYear('fecha_venta', now()->year)
                    ->whereMonth('fecha_venta', now()->month)
            )
            ->columns([
                Tables\Columns\TextColumn::make('id')
                    ->label('ID'),

                Tables\Columns\TextColumn::make('fecha_venta')
                    ->label('Fecha de Venta')
                    ->date(),

                Tables\Columns\TextColumn::make('monto')
                    ->label('Monto')
                    ->money('GTQ', true),
            ]);
    }
}
