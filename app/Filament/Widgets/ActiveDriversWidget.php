<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\DeliveryAssignment;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;

class ActiveDriversWidget extends BaseWidget
{
    protected static ?string $heading = 'Conductores Activos - Tiempo Real';
    
    protected static ?int $sort = 3;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DeliveryAssignment::query()
                    ->whereIn('driver_status', ['agarrado', 'en_ruta'])
                    ->with(['driver', 'vehicle', 'delivery'])
                    ->orderBy('updated_at', 'desc')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Conductor')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Vehículo')
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('delivery.id_delivery')
                    ->label('Entrega #')
                    ->formatStateUsing(fn ($state) => "#{$state}")
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('driver_status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'agarrado' => 'warning',
                        'en_ruta' => 'primary',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'agarrado' => 'heroicon-o-hand-raised',
                        'en_ruta' => 'heroicon-o-truck',
                        default => 'heroicon-o-question-mark-circle',
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'agarrado' => 'Agarrado',
                        'en_ruta' => 'En Ruta',
                        default => ucfirst($state),
                    }),
                    
                Tables\Columns\TextColumn::make('delivery.customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->limit(20),
                    
                Tables\Columns\TextColumn::make('delivery.delivery_address')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(30)
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin dirección'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actividad')
                    ->dateTime('H:i:s')
                    ->sortable()
                    ->since()
                    ->formatStateUsing(fn ($state) => $state ? $state->format('H:i:s') : 'Sin fecha'),
            ])
            ->poll('3s') // Actualizar cada 3 segundos para máximo tiempo real
            ->emptyStateHeading('No hay conductores activos')
            ->emptyStateDescription('No hay conductores con entregas activas en este momento.')
            ->emptyStateIcon('heroicon-o-user-group')
            ->defaultSort('updated_at', 'desc');
    }
}