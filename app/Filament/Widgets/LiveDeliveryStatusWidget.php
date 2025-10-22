<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\DeliveryAssignment;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Vehicle;
use Illuminate\Database\Eloquent\Builder;

class LiveDeliveryStatusWidget extends BaseWidget
{
    protected static ?string $heading = 'Estado de Entregas en Tiempo Real';
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DeliveryAssignment::query()
                    ->whereIn('driver_status', ['agarrado', 'en_ruta'])
                    ->orderBy('assigned_at', 'desc')
                    ->limit(10)
            )
            ->columns([
                Tables\Columns\TextColumn::make('delivery_info')
                    ->label('Entrega')
                    ->getStateUsing(function (DeliveryAssignment $record) {
                        $delivery = Delivery::find($record->delivery_id);
                        return $delivery ? 'Entrega #' . $delivery->id_delivery : 'N/A';
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('driver_name')
                    ->label('Conductor')
                    ->getStateUsing(function (DeliveryAssignment $record) {
                        $driver = Driver::find($record->driver_id);
                        return $driver ? $driver->name : 'N/A';
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('vehicle_plate')
                    ->label('Vehículo')
                    ->getStateUsing(function (DeliveryAssignment $record) {
                        $vehicle = Vehicle::find($record->vehicle_id);
                        return $vehicle ? $vehicle->license_plate : 'N/A';
                    })
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
                    }),
                    
                Tables\Columns\TextColumn::make('assigned_at')
                    ->label('Asignado')
                    ->dateTime()
                    ->sortable(),
            ])
            ->poll('10s') // Actualizar cada 10 segundos
            ->emptyStateHeading('No hay entregas activas')
            ->emptyStateDescription('No hay entregas con estado "agarrado" o "en ruta" en este momento.')
            ->emptyStateIcon('heroicon-o-truck');
    }
}
