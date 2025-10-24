<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\Vehicle;

class VehicleMaintenanceWidget extends BaseWidget
{
    protected static ?string $heading = '🚛 Estado de Vehículos - Kilometraje';
    
    protected static ?string $pollingInterval = '60s'; // ESTADÍSTICO: Tabla de vehículos
    
    protected static ?int $sort = 6;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Vehicle::query()
                    ->where('status', true)
                    ->orderByRaw("
                        CASE 
                            WHEN total_kilometers >= 10000 THEN 1
                            WHEN total_kilometers >= 9500 THEN 2
                            WHEN total_kilometers >= 9000 THEN 3
                            ELSE 4
                        END
                    ")
                    ->orderBy('total_kilometers', 'desc')
            )
            ->columns([
                Tables\Columns\TextColumn::make('license_plate')
                    ->label('Placa')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('total_kilometers')
                    ->label('Kilometraje Total')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' km')
                    ->sortable()
                    ->color(fn ($state): string => match (true) {
                        $state >= 10000 => 'danger',
                        $state >= 9500 => 'warning',
                        $state >= 9000 => 'info',
                        default => 'success',
                    })
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('maintenance_status')
                    ->label('Estado de Mantenimiento')
                    ->getStateUsing(function ($record) {
                        $km = $record->total_kilometers;
                        if ($km >= 10000) return 'URGENTE';
                        if ($km >= 9500) return 'Próximo';
                        if ($km >= 9000) return 'Se Acerca';
                        return 'Bueno';
                    })
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'URGENTE' => 'danger',
                        'Próximo' => 'warning',
                        'Se Acerca' => 'info',
                        'Bueno' => 'success',
                        default => 'gray',
                    })
                    ->icon(fn (string $state): string => match ($state) {
                        'URGENTE' => 'heroicon-o-exclamation-triangle',
                        'Próximo' => 'heroicon-o-clock',
                        'Se Acerca' => 'heroicon-o-information-circle',
                        'Bueno' => 'heroicon-o-check-circle',
                        default => 'heroicon-o-question-mark-circle',
                    }),
                    
                Tables\Columns\TextColumn::make('kilometers_until_maintenance')
                    ->label('Km Hasta Mantenimiento')
                    ->getStateUsing(function ($record) {
                        $nextMaintenance = ceil($record->total_kilometers / 10000) * 10000;
                        $remaining = max(0, $nextMaintenance - $record->total_kilometers);
                        return number_format($remaining, 2) . ' km';
                    })
                    ->color(function ($record): string {
                        $nextMaintenance = ceil($record->total_kilometers / 10000) * 10000;
                        $remaining = max(0, $nextMaintenance - $record->total_kilometers);
                        return match (true) {
                            $remaining <= 0 => 'danger',
                            $remaining <= 500 => 'warning',
                            $remaining <= 1000 => 'info',
                            default => 'success',
                        };
                    }),
                    
                Tables\Columns\TextColumn::make('capacity')
                    ->label('Capacidad')
                    ->formatStateUsing(fn ($state) => $state . ' kg')
                    ->sortable(),
                    
                Tables\Columns\IconColumn::make('available')
                    ->label('Disponible')
                    ->boolean()
                    ->sortable(),
            ])
            ->poll('60s') // Estadístico: menos frecuente
            ->emptyStateHeading('No hay vehículos registrados')
            ->emptyStateDescription('No se han encontrado vehículos activos.')
            ->emptyStateIcon('heroicon-o-truck')
            ->defaultSort('total_kilometers', 'desc')
            ->striped();
    }
}