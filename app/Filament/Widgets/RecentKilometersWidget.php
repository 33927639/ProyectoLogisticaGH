<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use App\Models\TblKilometer;

class RecentKilometersWidget extends BaseWidget
{
    protected static ?string $heading = '🚗 Últimos Registros de Kilometraje';
    
    protected static ?string $pollingInterval = '30s'; // ESTADÍSTICO: Registros recientes
    
    protected static ?int $sort = 8;
    
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                TblKilometer::query()
                    ->with(['vehicle', 'user'])
                    ->orderBy('created_at', 'desc')
                    ->limit(15)
            )
            ->columns([
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Fecha/Hora')
                    ->dateTime('d/m H:i:s')
                    ->sortable()
                    ->since()
                    ->icon('heroicon-o-clock'),
                    
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Vehículo')
                    ->searchable()
                    ->weight('bold')
                    ->icon('heroicon-o-truck')
                    ->color('primary'),
                    
                Tables\Columns\TextColumn::make('kilometers')
                    ->label('Km Agregados')
                    ->formatStateUsing(fn ($state) => '+' . number_format($state, 2) . ' km')
                    ->color('success')
                    ->weight('medium')
                    ->icon('heroicon-o-plus-circle'),
                    
                Tables\Columns\TextColumn::make('vehicle.total_kilometers')
                    ->label('Total Actual')
                    ->formatStateUsing(fn ($state) => number_format($state, 2) . ' km')
                    ->color(fn ($state): string => match (true) {
                        $state >= 10000 => 'danger',
                        $state >= 9500 => 'warning',
                        $state >= 9000 => 'info',
                        default => 'success',
                    })
                    ->weight('bold'),
                    
                Tables\Columns\TextColumn::make('notes')
                    ->label('Descripción')
                    ->limit(40)
                    ->tooltip(function ($record) {
                        return $record->notes;
                    })
                    ->searchable(),
                    
                Tables\Columns\TextColumn::make('user_name')
                    ->label('Por')
                    ->getStateUsing(function ($record) {
                        if ($record->user) {
                            return $record->user->first_name . ' ' . $record->user->last_name;
                        }
                        return 'Sistema';
                    })
                    ->icon('heroicon-o-user')
                    ->color('gray'),
            ])
            ->poll('5s') // Actualizar cada 5 segundos
            ->emptyStateHeading('No hay registros de kilometraje')
            ->emptyStateDescription('No se han registrado kilómetros recientemente.')
            ->emptyStateIcon('heroicon-o-chart-bar')
            ->striped();
    }
}