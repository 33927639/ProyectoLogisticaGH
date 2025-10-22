<?php

namespace App\Filament\Widgets;

use App\Models\Vehicle;
use App\Services\KilometerService;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Forms;

class VehicleKilometerWidget extends BaseWidget
{
    protected static ?string $heading = 'Control de Kilómetros por Vehículo';

    protected int | string | array $columnSpan = 'full';
    
    protected static ?int $sort = 3;

    public function table(Table $table): Table
    {
        return $table
            ->query(
                auth()->user()->hasRole(['Super Administrador', 'Administrador', 'Supervisor', 'Operador'])
                    ? Vehicle::query()->where('status', true)
                    : Vehicle::query()->whereRaw('1 = 0')
            )
            ->columns([
                Tables\Columns\TextColumn::make('plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('current_mileage')
                    ->label('Kilómetros Totales')
                    ->getStateUsing(fn (Vehicle $record): string => 
                        number_format(KilometerService::getTotalKilometers($record->id_vehicle)) . ' km'
                    )
                    ->sortable(),
                Tables\Columns\TextColumn::make('efficiency')
                    ->label('Eficiencia')
                    ->getStateUsing(function (Vehicle $record): string {
                        $totalKm = KilometerService::getTotalKilometers($record->id_vehicle);
                        $efficiency = $totalKm > 0 ? round($totalKm / max($record->fuel_capacity ?? 1, 1), 2) : 0;
                        return $efficiency . ' km/gal';
                    })
                    ->icon('heroicon-m-bolt')
                    ->iconColor('success'),
                Tables\Columns\TextColumn::make('maintenance_status')
                    ->label('Estado de Mantenimiento')
                    ->color(function (Vehicle $record): string {
                        $totalKm = KilometerService::getTotalKilometers($record->id_vehicle);
                        if ($totalKm > 15000) {
                            return 'danger';
                        } elseif ($totalKm > 10000) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->icon(function (Vehicle $record): string {
                        $totalKm = KilometerService::getTotalKilometers($record->id_vehicle);
                        if ($totalKm > 15000) {
                            return 'heroicon-s-exclamation-triangle';
                        } elseif ($totalKm > 10000) {
                            return 'heroicon-s-clock';
                        }
                        return 'heroicon-s-check-circle';
                    }),
                Tables\Columns\TextColumn::make('next_maintenance')
                    ->label('Próximo Mantenimiento')
                    ->getStateUsing(function (Vehicle $record): string {
                        $totalKm = KilometerService::getTotalKilometers($record->id_vehicle);
                        $nextMaintenance = (floor($totalKm / 5000) + 1) * 5000;
                        $remaining = $nextMaintenance - $totalKm;
                        return number_format($remaining) . ' km';
                    })
                    ->color(function (Vehicle $record): string {
                        $totalKm = KilometerService::getTotalKilometers($record->id_vehicle);
                        $nextMaintenance = (floor($totalKm / 5000) + 1) * 5000;
                        $remaining = $nextMaintenance - $totalKm;
                        if ($remaining <= 500) {
                            return 'danger';
                        } elseif ($remaining <= 1000) {
                            return 'warning';
                        }
                        return 'success';
                    })
                    ->icon('heroicon-m-wrench-screwdriver'),
                Tables\Columns\TextColumn::make('last_service')
                    ->label('Último Servicio')
                    ->getStateUsing(function (Vehicle $record): string {
                        $lastMaintenance = \App\Models\Maintenance::where('vehicle_id', $record->id_vehicle)
                            ->orderBy('maintenance_date', 'desc')
                            ->first();
                        
                        return $lastMaintenance 
                            ? $lastMaintenance->maintenance_date->format('d/m/Y')
                            : 'Sin servicios';
                    })
                    ->icon('heroicon-m-calendar-days'),
            ])
            ->actions([
                Tables\Actions\Action::make('add_kilometers')
                    ->label('Registrar Km')
                    ->icon('heroicon-m-plus')
                    ->color('info')
                    ->form([
                        Forms\Components\TextInput::make('kilometers')
                            ->label('Kilómetros a Agregar')
                            ->numeric()
                            ->required()
                            ->minValue(1),
                        Forms\Components\TextArea::make('description')
                            ->label('Descripción del Viaje')
                            ->placeholder('Ej: Entrega Guatemala - Antigua'),
                    ])
                    ->action(function (Vehicle $record, array $data): void {
                        KilometerService::addKilometers(
                            $record->id_vehicle,
                            $data['kilometers'],
                            $data['description'] ?? 'Kilómetros agregados manualmente'
                        );
                        
                        \Filament\Notifications\Notification::make()
                            ->title('Kilómetros registrados exitosamente')
                            ->success()
                            ->send();
                    })
                    ->modalHeading('Registrar Kilómetros')
                    ->modalContent(function (Vehicle $record): string {
                        $totalKm = KilometerService::getTotalKilometers($record->id_vehicle);
                        return "Kilómetros actuales del vehículo {$record->plate}: " . number_format($totalKm) . " km";
                    }),
            ])
            ->emptyStateHeading('Sin vehículos registrados')
            ->emptyStateDescription('No hay vehículos registrados en el sistema.')
            ->emptyStateIcon('heroicon-o-truck');
    }
}