<?php

namespace App\Filament\Widgets;

use App\Models\Maintenance;
use App\Models\MaintenanceRequest;
use App\Models\Vehicle;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Gate;

class MaintenancePendingWidget extends BaseWidget
{
    protected static ?string $heading = 'Mantenimientos Pendientes';
    protected static ?int $sort = 5;
    protected int | string | array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                // Check authorization first
                Gate::allows('viewAny', Maintenance::class) 
                    ? Maintenance::query()
                        ->where('status', 'Pendiente')
                        ->with(['vehicle'])
                        ->latest('maintenance_date')
                    : Maintenance::query()->whereRaw('1 = 0') // Empty query if not authorized
            )
            ->columns([
                Tables\Columns\TextColumn::make('vehicle.license_plate')
                    ->label('Vehículo')
                    ->searchable()
                    ->sortable(),

                Tables\Columns\TextColumn::make('type')
                    ->label('Tipo de Mantenimiento')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Preventivo' => 'success',
                        'Correctivo' => 'warning',
                        'Emergencia' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\TextColumn::make('maintenance_date')
                    ->label('Fecha Mantenimiento')
                    ->date('d/m/Y')
                    ->sortable(),

                Tables\Columns\TextColumn::make('status')
                    ->label('Estado')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'Pendiente' => 'warning',
                        'En Proceso' => 'primary',
                        'Completado' => 'success',
                        'Cancelado' => 'danger',
                        default => 'gray',
                    }),

                Tables\Columns\IconColumn::make('approved')
                    ->label('Aprobado')
                    ->boolean(),
            ])
            ->actions([
                Tables\Actions\Action::make('view')
                    ->label('Ver')
                    ->icon('heroicon-m-eye')
                    ->url(fn (Maintenance $record): string => route('filament.admin.resources.maintenance.view', $record))
                    ->visible(fn (Maintenance $record): bool => Gate::allows('view', $record)),
            ])
            ->defaultSort('maintenance_date', 'asc')
            ->paginated([5, 10])
            ->defaultPaginationPageOption(5);
    }

    protected function isTablePaginationEnabled(): bool
    {
        return true;
    }
}