<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\DeliveryAssignment;
use App\Models\Delivery;
use App\Models\Driver;
use App\Models\Vehicle;
use Carbon\Carbon;

class TodayDeliveriesWidget extends BaseWidget
{
    protected static ?string $heading = '📋 Todas las Entregas de Hoy - Tiempo Real';
    
    protected static ?string $pollingInterval = '5s'; // CRÍTICO: Entregas del día
    
    protected static ?int $sort = 5;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                DeliveryAssignment::query()
                    ->whereDate('assignment_date', today())
                    ->with(['driver', 'vehicle', 'delivery'])
                    ->orderByRaw("
                        CASE driver_status 
                            WHEN 'en_ruta' THEN 1
                            WHEN 'agarrado' THEN 2
                            WHEN 'pendiente' THEN 3
                            WHEN 'completado' THEN 4
                            ELSE 5
                        END
                    ")
                    ->orderBy('updated_at', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('delivery.id_delivery')
                    ->label('ID Entrega')
                    ->formatStateUsing(fn ($state) => $state ? "#{$state}" : 'N/A')
                    ->searchable()
                    ->sortable(),
                    
                Tables\Columns\TextColumn::make('driver.name')
                    ->label('Conductor')
                    ->searchable()
                    ->sortable()
                    ->default('Sin asignar')
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin asignar'),
                    
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Vehículo')
                    ->searchable()
                    ->default('Sin asignar')
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin asignar'),
                    
                Tables\Columns\TextColumn::make('delivery.customer_name')
                    ->label('Cliente')
                    ->searchable()
                    ->limit(25)
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin nombre'),
                    
                Tables\Columns\TextColumn::make('delivery.delivery_address')
                    ->label('Dirección')
                    ->searchable()
                    ->limit(30)
                    ->formatStateUsing(fn ($state) => $state ?: 'Sin dirección'),
                    
                Tables\Columns\TextColumn::make('driver_status')
                    ->label('Estado del Conductor')
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
                    })
                    ->formatStateUsing(fn (string $state): string => match ($state) {
                        'pendiente' => 'Pendiente',
                        'agarrado' => 'Agarrado',
                        'en_ruta' => 'En Ruta',
                        'completado' => 'Completado',
                        default => ucfirst($state),
                    }),
                    
                Tables\Columns\TextColumn::make('delivery.total_amount')
                    ->label('Monto')
                    ->money('GTQ')
                    ->sortable()
                    ->formatStateUsing(fn ($state) => $state ? 'GTQ ' . number_format($state, 2) : 'GTQ 0.00'),
                    
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Última Actividad')
                    ->dateTime('H:i:s')
                    ->sortable()
                    ->since()
                    ->tooltip(fn ($record) => $record->updated_at ? $record->updated_at->format('d/m/Y H:i:s') : 'Sin fecha'),
            ])
            ->poll('3s') // Actualizar cada 3 segundos para máximo tiempo real
            ->emptyStateHeading('No hay entregas programadas para hoy')
            ->emptyStateDescription('No se han programado entregas para el día de hoy.')
            ->emptyStateIcon('heroicon-o-calendar-days')
            ->defaultSort('updated_at', 'desc')
            ->defaultPaginationPageOption(25)
            ->striped()
            ->paginated([10, 25, 50]);
    }
}