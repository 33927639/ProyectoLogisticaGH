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
    
    protected static ?string $pollingInterval = '5s'; // CRÍTICO: Actualizar cada 5s
    
    protected static ?int $sort = 2;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DeliveryAssignment::query()
                    ->whereIn('driver_status', ['agarrado', 'en_ruta'])
                    ->orderBy('created_at', 'desc')
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
                        'pendiente' => 'gray',
                        'agarrado' => 'warning',
                        'en_ruta' => 'primary',
                        'completado' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'pendiente' => 'heroicon-o-clock',
                        'agarrado' => 'heroicon-o-hand-raised',
                        'en_ruta' => 'heroicon-o-truck',
                        'completado' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                    
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Asignado')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : 'Sin fecha'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actualización')
                    ->dateTime()
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? $state->format('d/m/Y H:i') : 'Sin fecha'),
            ])
            ->poll('5s') // Actualizar cada 5 segundos para mayor tiempo real
            ->emptyStateHeading('No hay entregas activas')
            ->emptyStateDescription('No hay entregas con estado "agarrado" o "en ruta" en este momento.')
            ->emptyStateIcon('heroicon-o-truck');
    }
}
